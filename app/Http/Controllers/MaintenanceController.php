<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\MaintenancePhoto;
use App\Models\Unit;
use App\Models\Tenant;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with('unit.property', 'tenant.user', 'assignedTo')
            ->latest()
            ->get();

        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        $units   = Unit::with('property')->get();
        $tenants = Tenant::with('user')->get();

        return view('maintenance.create', compact('units', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id'     => 'required|exists:units,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required|in:plumbing,electrical,structural,cleaning,pest_control,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'photos.*'    => 'nullable|image|max:2048',
        ]);

        $maintenance = MaintenanceRequest::create([
            'unit_id'     => $request->unit_id,
            'tenant_id'   => $request->tenant_id,
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance', 'public');
                MaintenancePhoto::create([
                    'maintenance_request_id' => $maintenance->id,
                    'file_path'              => $path,
                    'photo_type'             => 'before',
                ]);
            }
        }

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request submitted successfully.');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $maintenance->load('unit.property', 'tenant.user', 'assignedTo', 'photos');
        return view('maintenance.show', compact('maintenance'));
    }

    public function edit(MaintenanceRequest $maintenance)
    {
        $units = Unit::with('property')->get();
        return view('maintenance.edit', compact('maintenance', 'units'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        $request->validate([
            'status'   => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'cost'     => 'nullable|numeric|min:0',
            'photos.*' => 'nullable|image|max:2048',
        ]);

        $maintenance->update([
            'status'           => $request->status,
            'priority'         => $request->priority,
            'cost'             => $request->cost,
            'resolution_notes' => $request->resolution_notes,
            'resolved_at'      => in_array($request->status, ['resolved', 'closed']) ? now() : null,
        ]);

        // Handle additional photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance', 'public');
                MaintenancePhoto::create([
                    'maintenance_request_id' => $maintenance->id,
                    'file_path'              => $path,
                    'photo_type'             => $request->photo_type ?? 'after',
                ]);
            }
        }

        return redirect()->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance request updated.');
    }

    public function destroy(MaintenanceRequest $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request deleted.');
    }
}