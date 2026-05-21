<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use App\Models\SupervisorMessage;
use App\Models\Alert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $machine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Jorge',
            'email' => 'admin@arancalo.com',
            'password' => bcrypt('password123'),
        ]);

        $this->machine = Machine::create([
            'id' => 'm1',
            'name' => 'Torno CNC A',
            'status' => 'online',
            'serial' => 'SN-CNC-001',
            'indicator' => 'green',
            'column' => 1,
            'row' => 1,
        ]);
    }

    public function test_admin_dashboard_redirects_unauthenticated_user()
    {
        Livewire::test(\App\Livewire\AdminDashboard::class)
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_select_machine_and_marks_operator_messages_as_read()
    {
        // Create an unread message from operator
        SupervisorMessage::create([
            'id' => 'msg-1',
            'machine_id' => 'm1',
            'machine_name' => 'Torno CNC A',
            'text' => 'Falla en el husillo',
            'from' => 'operator',
            'senderName' => 'Operario Carlos',
            'timestamp' => '10:00',
            'read' => false,
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\AdminDashboard::class)
            ->assertSet('selectedMachineIdForMessages', 'm1')
            ->call('selectMachineForMessages', 'm1')
            ->assertSet('adminReplyInput', '');

        // Verify it was marked as read
        $this->assertTrue(SupervisorMessage::find('msg-1')->read);
    }

    public function test_admin_can_send_reply()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\AdminDashboard::class)
            ->set('selectedMachineIdForMessages', 'm1')
            ->set('adminReplyInput', 'Entendido, el soporte va en camino.')
            ->call('sendAdminReply')
            ->assertSet('adminReplyInput', '');

        $this->assertDatabaseHas('supervisor_messages', [
            'machine_id' => 'm1',
            'from' => 'admin',
            'text' => 'Entendido, el soporte va en camino.',
            'senderName' => 'Supervisor Arancalo'
        ]);
    }

    public function test_admin_can_change_status_from_chat()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\AdminDashboard::class)
            ->set('selectedMachineIdForMessages', 'm1')
            ->call('changeMachineStatusFromChat', 'warning');

        // Check machine status changed
        $this->assertEquals('warning', $this->machine->fresh()->status);

        // Check system message logged in chat
        $this->assertDatabaseHas('supervisor_messages', [
            'machine_id' => 'm1',
            'from' => 'system',
            'text' => '🚨 [SUPERVISOR] Cambió el estado de la unidad a: AVERÍA'
        ]);

        // Check alert created
        $this->assertDatabaseHas('alerts', [
            'machine_id' => 'm1',
            'type' => 'warning'
        ]);
    }

    public function test_admin_can_generate_ai_message_suggestion_fallback()
    {
        // Pre-create an operator message that triggers fallback
        SupervisorMessage::create([
            'id' => 'msg-op-1',
            'machine_id' => 'm1',
            'machine_name' => 'Torno CNC A',
            'text' => 'La máquina tiene una averia grave en el motor',
            'from' => 'operator',
            'senderName' => 'Carlos',
            'timestamp' => '10:10',
            'read' => false
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\AdminDashboard::class)
            ->set('selectedMachineIdForMessages', 'm1')
            ->call('generateAiMessageSuggestion')
            ->assertSet('isGeneratingSuggestion', false)
            ->assertNotSet('adminReplyInput', '');

        // It should contain the fallback string keywords
        $adminInput = Livewire::test(\App\Livewire\AdminDashboard::class)
            ->set('selectedMachineIdForMessages', 'm1')
            ->call('generateAiMessageSuggestion')
            ->get('adminReplyInput');

        $this->assertStringContainsString('avería', $adminInput);
        $this->assertStringContainsString('Torno CNC A', $adminInput);
    }
}
