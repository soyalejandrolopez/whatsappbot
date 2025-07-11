<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatsappContact>
 */
class WhatsappContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone_number' => '+52' . $this->faker->numerify('##########'),
            'whatsapp_id' => $this->faker->numerify('52##########'),
            'name' => $this->faker->name(),
            'profile_name' => $this->faker->firstName(),
            'language' => 'es',
            'profile_data' => null,
            'is_blocked' => false,
            'opt_in' => true,
            'last_interaction_at' => now(),
            'tags' => null,
            'notes' => null,
        ];
    }
}
