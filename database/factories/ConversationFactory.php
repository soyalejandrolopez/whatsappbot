<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => \App\Models\WhatsappContact::factory(),
            'assigned_user_id' => null,
            'status' => 'active',
            'type' => 'chatbot',
            'language' => 'es',
            'current_flow_id' => null,
            'flow_context' => null,
            'message_count' => 0,
            'last_message_at' => now(),
            'started_at' => now(),
            'ended_at' => null,
            'satisfaction_rating' => null,
            'satisfaction_comment' => null,
            'metadata' => null,
        ];
    }
}
