<?php

namespace Tests\Feature;

use App\Models\JournalPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_journals_are_visible_with_article_metadata(): void
    {
        $post = JournalPost::create($this->articleData());
        JournalPost::create($this->articleData(['title' => 'Hidden Draft', 'slug' => 'hidden-draft', 'is_published' => false]));

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee($post->title)
            ->assertDontSee('Hidden Draft')
            ->assertSee('CollectionPage');

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee($post->author_name)
            ->assertSee('Article')
            ->assertSee('Origin and place');

        $this->get(route('blog.show', 'hidden-draft'))->assertNotFound();
    }

    public function test_administrator_can_create_update_and_delete_a_journal_article(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);
        $session = ['admin_2fa_verified_at' => now()->timestamp];
        $payload = $this->articleData(['slug' => '', 'is_published' => '1', 'is_featured' => '1']);

        $this->actingAs($admin)->withSession($session)
            ->post(route('admin.journals.store'), $payload)
            ->assertRedirect(route('admin.journals.index'));

        $post = JournalPost::where('title', $payload['title'])->firstOrFail();
        $this->assertSame('a-useful-mountain-story', $post->slug);
        $this->assertTrue($post->is_featured);
        $this->assertSame('Himalayan ingredient sourcing', $post->focus_keyword);

        $this->actingAs($admin)->withSession($session)
            ->put(route('admin.journals.update', $post), $this->articleData(['title' => 'Updated Mountain Story', 'slug' => $post->slug, 'is_published' => '1']))
            ->assertRedirect(route('admin.journals.index'));

        $this->assertDatabaseHas('journal_posts', ['id' => $post->id, 'title' => 'Updated Mountain Story']);

        $this->actingAs($admin)->withSession($session)
            ->delete(route('admin.journals.destroy', $post))
            ->assertRedirect(route('admin.journals.index'));

        $this->assertDatabaseMissing('journal_posts', ['id' => $post->id]);
    }

    public function test_catalogue_staff_can_manage_journals_but_support_staff_cannot(): void
    {
        $catalogue = User::factory()->create(['role' => 'catalogue']);
        $support = User::factory()->create(['role' => 'support']);
        $session = ['admin_2fa_verified_at' => now()->timestamp];

        $this->actingAs($catalogue)->withSession($session)->get(route('admin.journals.index'))->assertOk();
        $this->actingAs($support)->withSession($session)->get(route('admin.journals.index'))->assertForbidden();
    }

    private function articleData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'A Useful Mountain Story',
            'slug' => 'a-useful-mountain-story',
            'category' => 'Sourcing',
            'author_name' => 'Rajendra Panwar',
            'excerpt' => 'A practical and transparent introduction to a mountain ingredient and the people connected to it.',
            'content' => "## Origin and place\n\nThis is a detailed paragraph about a mountain ingredient, its region and the importance of clear sourcing information for customers.\n\n## Everyday use\n\nThis second section explains safe storage, thoughtful use and the natural variation customers may notice from one seasonal batch to another.",
            'image_alt' => 'A carefully sourced mountain ingredient',
            'seo_title' => 'A Useful Mountain Story and Sourcing Guide',
            'meta_description' => 'A practical guide to mountain ingredients, transparent sourcing, safe storage and understanding natural seasonal variation.',
            'focus_keyword' => 'Himalayan ingredient sourcing',
            'is_featured' => false,
            'is_published' => true,
            'published_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ], $overrides);
    }
}
