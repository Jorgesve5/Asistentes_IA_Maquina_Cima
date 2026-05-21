<?php

namespace Tests\Feature;

use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MachineDetailChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_initializes_welcome_message_and_persists_to_session()
    {
        $machine = Machine::create([
            'id' => 'm1',
            'name' => 'Cortadora Láser',
            'status' => 'online',
            'serial' => 'SN-12345',
            'indicator' => 'green',
            'column' => 1,
            'row' => 1,
        ]);

        $test = Livewire::test(\App\Livewire\MachineDetail::class, ['id' => 'm1']);
        $contextKey = $test->instance()->contextKey;

        $test->assertSet('machineId', 'm1')
            ->assertCount('chatMessages', 1);

        $this->assertTrue(session()->has($contextKey));
        $this->assertCount(1, session($contextKey));
        $this->assertEquals('welcome', session($contextKey)[0]['id']);
    }

    public function test_chatbot_loads_existing_session_history_but_keeps_ui_clean()
    {
        $machine = Machine::create([
            'id' => 'm1',
            'name' => 'Cortadora Láser',
            'status' => 'online',
            'serial' => 'SN-12345',
            'indicator' => 'green',
            'column' => 1,
            'row' => 1,
        ]);

        // Compute the expected user-namespaced context key
        $userId = 'guest_' . session()->getId();
        $contextKey = "machine_chat_context_{$userId}_m1";

        $existingHistory = [
            [
                'id' => 'welcome',
                'sender' => 'bot',
                'text' => 'Welcome',
                'timestamp' => '10:00'
            ],
            [
                'id' => 'msg-1',
                'sender' => 'user',
                'text' => 'Hello',
                'timestamp' => '10:01'
            ]
        ];

        session([$contextKey => $existingHistory]);

        // When starting/loading component, UI has only the welcome message, but session context contains the full history
        Livewire::test(\App\Livewire\MachineDetail::class, ['id' => 'm1'])
            ->assertCount('chatMessages', 1)
            ->assertSet('chatMessages', [
                [
                    'id' => 'welcome',
                    'sender' => 'bot',
                    'text' => "Hola, soy el asistente virtual de la **Cortadora Láser**. ¿En qué puedo ayudarte hoy con esta unidad?",
                    'timestamp' => now()->format('H:i')
                ]
            ]);

        $this->assertEquals($existingHistory, session($contextKey));
    }

    public function test_chatbot_persist_updates_session_on_message_sent()
    {
        $machine = Machine::create([
            'id' => 'm1',
            'name' => 'Cortadora Láser',
            'status' => 'online',
            'serial' => 'SN-12345',
            'indicator' => 'green',
            'column' => 1,
            'row' => 1,
        ]);

        $test = Livewire::test(\App\Livewire\MachineDetail::class, ['id' => 'm1']);
        $contextKey = $test->instance()->contextKey;

        $test->set('userInput', '¿Cómo enciendo la máquina?')
            ->call('sendChatbotMessage')
            ->assertCount('chatMessages', 2);

        $sessionMessages = session($contextKey);
        $this->assertCount(2, $sessionMessages);
        $this->assertEquals('user', $sessionMessages[1]['sender']);
        $this->assertEquals('¿Cómo enciendo la máquina?', $sessionMessages[1]['text']);
    }

    public function test_clear_chat_history_resets_session_to_welcome()
    {
        $machine = Machine::create([
            'id' => 'm1',
            'name' => 'Cortadora Láser',
            'status' => 'online',
            'serial' => 'SN-12345',
            'indicator' => 'green',
            'column' => 1,
            'row' => 1,
        ]);

        $test = Livewire::test(\App\Livewire\MachineDetail::class, ['id' => 'm1']);
        $contextKey = $test->instance()->contextKey;

        $test->set('userInput', 'Mensaje de prueba')
            ->call('sendChatbotMessage')
            ->assertCount('chatMessages', 2)
            ->call('clearChatHistory')
            ->assertCount('chatMessages', 1);

        $sessionMessages = session($contextKey);
        $this->assertCount(1, $sessionMessages);
        $this->assertEquals('welcome', $sessionMessages[0]['id']);
    }
}
