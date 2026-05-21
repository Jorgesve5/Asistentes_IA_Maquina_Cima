<?php

namespace App\Livewire;

use App\Models\Manual;
use App\Models\Machine;
use Livewire\Component;
use Livewire\WithPagination;

class ManualsExplorer extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $machineId = '';
    public $fileType = '';

    // Viewer modal state
    public $viewingManualId = null;
    public $showViewerModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'machineId' => ['except' => ''],
        'fileType' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingMachineId()
    {
        $this->resetPage();
    }

    public function updatingFileType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'machineId', 'fileType']);
        $this->resetPage();
    }

    public function openViewer($manualId)
    {
        $this->viewingManualId = $manualId;
        $this->showViewerModal = true;
    }

    public function closeViewer()
    {
        $this->showViewerModal = false;
        $this->viewingManualId = null;
    }

    public function render()
    {
        $query = Manual::with('machine');

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('fileName', 'like', $searchTerm)
                  ->orWhere('text', 'like', $searchTerm)
                  ->orWhereHas('machine', function($mq) use ($searchTerm) {
                      $mq->where('name', 'like', $searchTerm);
                  });
            });
        }

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        if (!empty($this->machineId)) {
            $query->where('machine_id', $this->machineId);
        }

        if (!empty($this->fileType)) {
            $query->where('file_type', $this->fileType);
        }

        $manuals = $query->latest()->paginate(12);
        $machines = Machine::orderBy('name')->get();
        
        $categories = [
            'Manual de Operación',
            'Esquema Eléctrico',
            'Guía Rápida',
            'Hoja de Registro',
            'Imágenes',
            'Otro'
        ];

        $viewingManual = $this->viewingManualId ? Manual::find($this->viewingManualId) : null;

        return view('livewire.manuals-explorer', [
            'manuals' => $manuals,
            'machines' => $machines,
            'categories' => $categories,
            'viewingManual' => $viewingManual,
        ])->layout('layouts.app', ['bodyClass' => 'bg-slate-50 text-slate-900'])
          ->title('Manuales y Recursos - Arancalo');
    }
}
