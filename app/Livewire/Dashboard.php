<?php

namespace App\Livewire;

use App\Models\Machine;
use App\Models\Alert;
use Livewire\Component;

class Dashboard extends Component
{
    public $showAlerts = false;
    public $showHelp = false;

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
    }

    public function render()
    {
        $machines = Machine::all();
        $alerts = Alert::orderBy('created_at', 'desc')->take(20)->get();
        $unreadCount = Alert::where('read', false)->count();

        $countOnline = $machines->where('status', 'online')->count();
        $countMaintenance = $machines->where('status', 'maintenance')->count();
        $countWaiting = $machines->where('status', 'waiting')->count();
        $countWarning = $machines->where('status', 'warning')->count();

        $columns = [];
        for ($c = 1; $c <= 4; $c++) {
            $columns[$c] = $machines->where('column', $c)->sortBy('row');
        }

        return view('livewire.dashboard', [
            'columns' => $columns,
            'alerts' => $alerts,
            'unreadCount' => $unreadCount,
            'countOnline' => $countOnline,
            'countMaintenance' => $countMaintenance,
            'countWaiting' => $countWaiting,
            'countWarning' => $countWarning,
        ])->title('ZONA MÁQUINAS - CIMA');
    }
}
