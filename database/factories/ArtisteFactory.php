<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Artiste>
 */
class ArtisteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=> User::inRandomOrder()->value('id'),
            'profession' => $this->faker->randomElement(['Tatoueur', 'Pierceur', 'Maquilleur', 'Nailartiste', 'Esthéticienne']),
            'style' => $this->faker->randomElement(['Old School', 'New School', 'Réalisme', 'Aquarelle', 'Tribal', 'Japonais']),
            // 'telephone' => fake("fr_FR")->phoneNumber(),
            'pseudo' => fake("fr_FR")->userName(),
            // 'adresse' => fake("fr_FR")->address(),
            'bio' => $this->faker->paragraph(3, true),
            'instagram' => $this->faker->url(),
            'facebook' => $this->faker->url(),
            'banniereUrl' => $this->faker->imageUrl(),
            'vignetteUrl' => $this->faker->imageUrl(),
            'css' => "body { background-color: #f0f0f0; }". $this->faker->safecolorName(),
                
        ];
    }
}
