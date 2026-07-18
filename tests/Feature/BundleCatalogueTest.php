<?php

namespace Tests\Feature;

use App\Models\Bundle;
use Database\Seeders\BundleSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BundleCatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_curated_gift_boxes_are_complete_discounted_and_visible(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(BundleSeeder::class);

        $bundles = Bundle::with('products')->where('is_active', true)->get();
        $this->assertCount(12, $bundles);

        foreach ($bundles as $bundle) {
            $this->assertGreaterThanOrEqual(4, $bundle->products->count());
            $this->assertGreaterThan(0, $bundle->available_stock);
            $this->assertGreaterThan((float) $bundle->price, $bundle->retail_value);
            $this->assertTrue(Storage::disk('public')->exists($bundle->image));
        }

        $this->get(route('bundles.index'))
            ->assertOk()
            ->assertSee('Uttarakhand Breakfast Gift Box')
            ->assertSee('Premium Himalayan Festive Hamper')
            ->assertSee('Save ₹')
            ->assertSee('Add gift box');
    }
}
