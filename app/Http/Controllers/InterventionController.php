<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InterventionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Intervention::with('technician.company');
        
        // Recherche par technicien, client (title) ou date
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('technician', function ($technicianQuery) use ($search) {
                      $technicianQuery->where('first_name', 'like', "%{$search}%")
                                     ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Recherche par date si fournie
        if ($request->filled('date_search')) {
            $dateSearch = $request->get('date_search');
            $query->whereDate('scheduled_at', $dateSearch);
        }
        
        $interventions = $query->latest('scheduled_at')->paginate(15)->withQueryString();
        
        return view('interventions.index', compact('interventions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $technicians = Technician::where('is_active', true)
            ->with('company')
            ->orderBy('first_name')
            ->get();
        
        return view('interventions.create', compact('technicians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'scheduled_at' => 'required|date',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|integer|min:0|max:5',
            'is_completed' => 'nullable|boolean',
            'non_completion_reason' => 'nullable|string|required_if:is_completed,false',
            'notes' => 'nullable|string',
            'client_comments' => 'nullable|string',
        ]);

        Intervention::create($validated);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Intervention $intervention): View
    {
        $intervention->load('technician.company');
        
        return view('interventions.show', compact('intervention'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intervention $intervention): View
    {
        $technicians = Technician::where('is_active', true)
            ->with('company')
            ->orderBy('first_name')
            ->get();
        
        return view('interventions.edit', compact('intervention', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intervention $intervention): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'scheduled_at' => 'required|date',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|integer|min:0|max:5',
        ]);

        $intervention->update($validated);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intervention $intervention): RedirectResponse
    {
        $intervention->delete();

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention supprimée avec succès.');
    }
}

