<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Technician::with('company')->withKpis();
        
        // Recherche par nom, prénom, téléphone ou email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $technicians = $query
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(200)
            ->withQueryString();
        
        return view('technicians.index', compact('technicians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        
        return view('technicians.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        // Gérer le cas où la checkbox n'est pas cochée (non envoyée dans la requête)
        $validated['is_active'] = $request->boolean('is_active');

        Technician::create($validated);

        return redirect()->route('technicians.index')
            ->with('success', 'Technicien créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Technician $technician): View
    {
        $technician->load([
            'company',
            'interventions' => function ($query) {
                $query->latest()->limit(10);
            },
        ])->loadAvg(
            ['interventions as average_rating' => fn ($query) => $query->whereNotNull('note')],
            'note',
        );
        
        return view('technicians.show', compact('technician'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technician $technician): View
    {
        $companies = Company::orderBy('name')->get();
        
        return view('technicians.edit', compact('technician', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technician $technician): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        // Gérer le cas où la checkbox n'est pas cochée (non envoyée dans la requête)
        $validated['is_active'] = $request->boolean('is_active');

        $technician->update($validated);

        return redirect()->route('technicians.index')
            ->with('success', 'Technicien mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician): RedirectResponse
    {
        $technician->delete();

        return redirect()->route('technicians.index')
            ->with('success', 'Technicien supprimé avec succès.');
    }
}

