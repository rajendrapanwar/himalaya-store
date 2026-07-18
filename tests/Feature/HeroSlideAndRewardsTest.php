<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroSlideAndRewardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_only_current_active_slides(): void
    {
        HeroSlide::query()->delete();
        HeroSlide::create($this->slideData(['title' => 'Visible mountain campaign']));
        HeroSlide::create($this->slideData(['title' => 'Inactive mountain campaign', 'is_active' => false, 'sort_order' => 20]));
        HeroSlide::create($this->slideData(['title' => 'Future mountain campaign', 'starts_at' => now()->addDay(), 'sort_order' => 30]));
        HeroSlide::create($this->slideData(['title' => 'Expired mountain campaign', 'ends_at' => now()->subDay(), 'sort_order' => 40]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Visible mountain campaign')
            ->assertDontSee('Inactive mountain campaign')
            ->assertDontSee('Future mountain campaign')
            ->assertDontSee('Expired mountain campaign')
            ->assertSee('data-hero-slider', false);
    }

    public function test_rewards_guide_explains_benefits_and_shows_signed_in_customers_link(): void
    {
        $user = User::factory()->create(['referral_code' => 'HILLS123']);

        $this->actingAs($user)->get(route('rewards'))
            ->assertOk()
            ->assertSee('Good things are better when shared.')
            ->assertSee('100')
            ->assertSee('points for you')
            ->assertSee('50')
            ->assertSee('points for your friend')
            ->assertSee(route('refer', 'HILLS123'), false)
            ->assertSee('FAQPage');
    }

    public function test_administrator_can_create_update_and_delete_a_slide(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);
        $session = ['admin_2fa_verified_at' => now()->timestamp];

        $payload = $this->slideData([
            'image' => UploadedFile::fake()->image('campaign.webp', 1600, 800),
            'is_active' => '1',
        ]);
        unset($payload['image_path']);

        $this->actingAs($admin)->withSession($session)
            ->post(route('admin.hero-slides.store'), $payload)
            ->assertRedirect(route('admin.hero-slides.index'));

        $slide = HeroSlide::where('title', $payload['title'])->firstOrFail();
        Storage::disk('public')->assertExists($slide->image_path);

        $update = $this->slideData(['title' => 'Updated homepage campaign', 'is_active' => '1']);
        unset($update['image_path']);
        $this->actingAs($admin)->withSession($session)
            ->put(route('admin.hero-slides.update', $slide), $update)
            ->assertRedirect(route('admin.hero-slides.index'));
        $this->assertDatabaseHas('hero_slides', ['id' => $slide->id, 'title' => 'Updated homepage campaign']);

        $this->actingAs($admin)->withSession($session)
            ->delete(route('admin.hero-slides.destroy', $slide))
            ->assertRedirect(route('admin.hero-slides.index'));
        $this->assertDatabaseMissing('hero_slides', ['id' => $slide->id]);
    }

    public function test_catalogue_staff_can_manage_slides_but_support_staff_cannot(): void
    {
        $catalogue = User::factory()->create(['role' => 'catalogue']);
        $support = User::factory()->create(['role' => 'support']);
        $session = ['admin_2fa_verified_at' => now()->timestamp];

        $this->actingAs($catalogue)->withSession($session)->get(route('admin.hero-slides.index'))->assertOk();
        $this->actingAs($support)->withSession($session)->get(route('admin.hero-slides.index'))->assertForbidden();
    }

    private function slideData(array $overrides = []): array
    {
        return array_merge([
            'eyebrow' => 'A mountain campaign',
            'title' => 'Seasonal produce from the hills',
            'description' => 'A clear supporting message about this homepage campaign and its customer benefit.',
            'image_path' => 'assets/img/hero/himalayan-collection-v2.webp',
            'image_alt' => 'A collection of Himalayan pantry produce',
            'primary_label' => 'Shop now',
            'primary_url' => '/products',
            'secondary_label' => 'Learn more',
            'secondary_url' => '/about',
            'text_theme' => 'light',
            'text_position' => 'left',
            'sort_order' => 10,
            'is_active' => true,
        ], $overrides);
    }
}
