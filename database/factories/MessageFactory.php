<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => \App\Models\Conversation::factory(),
            'sender_id' => null,
            'whatsapp_message_id' => 'msg_' . $this->faker->uuid(),
            'direction' => $this->faker->randomElement(['inbound', 'outbound']),
            'type' => 'text',
            'content' => $this->faker->sentence(),
            'media_data' => null,
            'interactive_data' => null,
            'status' => 'sent',
            'whatsapp_timestamp' => now(),
            'is_automated' => false,
            'flow_step' => null,
            'metadata' => null,
        ];
    }
}
