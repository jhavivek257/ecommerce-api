<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => fake()->unique()->randomNumber(8),
            'description' => fake()->paragraph(),
            'mrp' => fake()->randomFloat(2, 100, 1000),
            'salePrice' => fake()->randomFloat(2, 100, 1000),
            'stock'=> fake()->numberBetween(1, 100),
            'thumbnail' => 'https://via.placeholder.com/200x250',
            'catId' => Category::inRandomOrder()->first()->id ?? 1,
            'status' => fake()->boolean(80),
        ];
    }
}
