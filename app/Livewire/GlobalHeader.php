<?php

namespace App\Livewire;

use App\Models\Alert;
use Livewire\Component;
use Livewire\Attributes\On;

class GlobalHeader extends Component
{
    public $showAlerts = false;
    public $showHelp = false;

    #[On('alert-created')]
    public function updateAlerts()
    {
        // This triggers a re-render to update the bell icon count when a new alert is generated globally.
    }

    public function markAlertRead($id)
    {
        $alert = Alert::find($id);
        if ($alert) {
            $alert->update(['read' => true]);
        }
    }

    public function clearAlerts()
    {
        Alert::query()->delete();
        $this->showAlerts = false;
    }

    public function render()
    {
        $alerts = Alert::orderBy('created_at', 'desc')->take(20)->get();
        $unreadCount = Alert::where('read', false)->count();

        return view('livewire.global-header', [
            'alerts' => $alerts,
            'unreadCount' => $unreadCount,
        ]);
    }
}
