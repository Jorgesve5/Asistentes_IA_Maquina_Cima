<?php

namespace App\Livewire;

use App\Models\Alert;
use Livewire\Attributes\On;
use Livewire\Component;

class GlobalToast extends Component
{
    public $toast = null;
    public $lastAlertId = null;

    public function mount()
    {
        $latestAlert = Alert::orderBy('created_at', 'desc')->first();
        $this->lastAlertId = $latestAlert ? $latestAlert->id : null;
    }

    #[On('alert-created')]
    public function pollUpdates()
    {
        $latest = Alert::orderBy('created_at', 'desc')->first();
        
        if ($latest && $latest->id !== $this->lastAlertId) {
            $reason = '';
            if (strpos($latest->message, 'Motivo:') !== false) {
                $parts = explode('Motivo:', $latest->message);
                $reason = trim(end($parts));
            }

            $statusMap = [
                'warning' => 'Avería',
                'maintenance' => 'Mantenimiento',
                'waiting' => 'En Espera',
                'info' => 'Disponible'
            ];

            $this->toast = [
                'id' => $latest->id,
                'machineName' => $latest->machine_name,
                'status' => $statusMap[$latest->type] ?? 'Disponible',
                'reason' => $reason ?: 'Sin motivo especificado',
                'type' => $latest->type === 'info' ? 'online' : $latest->type,
            ];
            
            $this->lastAlertId = $latest->id;
            
            // Dispatch event to play sound in frontend
            $this->dispatch('play-sound', ['type' => 'success']);
        } elseif (!$this->lastAlertId && $latest) {
            $this->lastAlertId = $latest->id;
        }
    }

    public function dismissToast()
    {
        $this->toast = null;
    }

    public function render()
    {
        return view('livewire.global-toast');
    }
}
