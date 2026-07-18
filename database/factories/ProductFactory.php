<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return ['category_id' => Category::factory(), 'name' => Str::title($name), 'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 99999), 'short_description' => fake()->sentence(), 'description' => fake()->paragraph(), 'price' => fake()->randomFloat(2, 100, 1500), 'sku' => 'TEST-'.Str::upper(Str::random(10)), 'stock_quantity' => 20, 'reserved_quantity' => 0, 'origin_region' => 'Uttarakhand', 'status' => true];
    }
}
