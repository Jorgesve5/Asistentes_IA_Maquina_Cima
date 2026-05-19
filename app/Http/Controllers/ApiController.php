<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Alert;
use App\Models\SupervisorMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Get all machines list.
     */
    public function getMachines()
    {
        $machines = Machine::all();
        return response()->json([
            'status' => 'success',
            'data' => $machines
        ], 200);
    }

    /**
     * Get details of a specific machine including manuals.
     */
    public function getMachine($id)
    {
        $machine = Machine::with('manuals')->find($id);

        if (!$machine) {
            return response()->json([
                'status' => 'error',
                'message' => "Machine with ID '{$id}' not found."
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $machine
        ], 200);
    }

    /**
     * Update machine status and register an incident.
     */
    public function updateMachineStatus(Request $request, $id)
    {
        $machine = Machine::find($id);

        if (!$machine) {
            return response()->json([
                'status' => 'error',
                'message' => "Machine with ID '{$id}' not found."
            ], 404);
        }

        // Validate status and optional reason
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:online,maintenance,waiting,warning',
            'reason' => 'required_if:status,maintenance,waiting,warning|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $newStatus = $request->input('status');
        $reason = trim($request->input('reason', ''));

        $statusNames = [
            'online' => 'Disponible',
            'warning' => 'Avería',
            'maintenance' => 'Mantenimiento',
            'waiting' => 'En Espera',
        ];

        // Format subLabel based on status and reason
        $subLabel = '';
        if ($newStatus !== 'online') {
            $subLabel = $newStatus === 'warning' ? "AVERÍA: {$reason}" : 
                        ($newStatus === 'maintenance' ? "MANT: {$reason}" : "ESPERA: {$reason}");
        }

        // Update the machine record
        $machine->update([
            'status' => $newStatus,
            'subLabel' => $subLabel
        ]);

        $alertMessage = "API Incidencia: {$machine->name} marcada en " . $statusNames[$newStatus];
        if (!empty($reason)) {
            $alertMessage .= ". Motivo: {$reason}";
        }

        // Create alert notification
        Alert::create([
            'id' => 'alert-' . now()->timestamp . '-' . uniqid(),
            'machine_id' => $machine->id,
            'machine_name' => $machine->name,
            'message' => $alertMessage,
            'type' => $newStatus === 'online' ? 'info' : $newStatus,
            'timestamp' => now()->format('d/m H:i'),
            'read' => false
        ]);

        // Auto-post a message in the supervisor log if it's an incident
        if ($newStatus !== 'online') {
            SupervisorMessage::create([
                'id' => 'msg-' . now()->timestamp . '-' . uniqid(),
                'machine_id' => $machine->id,
                'machine_name' => $machine->name,
                'text' => "⚠️ Reportó Incidencia vía API ({$statusNames[$newStatus]}): {$reason}",
                'from' => 'operator',
                'senderName' => 'API Integración',
                'timestamp' => now()->format('H:i'),
                'read' => false
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Machine status updated to '{$statusNames[$newStatus]}' successfully.",
            'data' => $machine
        ], 200);
    }

    /**
     * Get recent system alerts.
     */
    public function getAlerts()
    {
        $alerts = Alert::orderBy('created_at', 'desc')->take(20)->get();
        return response()->json([
            'status' => 'success',
            'data' => $alerts
        ], 200);
    }
}
