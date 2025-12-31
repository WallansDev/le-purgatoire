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
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les techniciens.');
        }
        
        $query = Technician::with('company')->withKpis();
        
        // Filtrer les techniciens pour n'afficher que ceux dont la company appartient à une organisation
        // où l'utilisateur a la permission de lecture des companies
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $query->whereHas('company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        
        // Filtre par entreprise
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->get('company_id'));
        }
        
        // Filtre par département
        if ($request->filled('department')) {
            $query->where('department', $request->get('department'));
        }
        
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
        
        // Récupérer les entreprises et départements pour les filtres
        $companiesQuery = Company::query();
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $companiesQuery->whereHas('organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        $companies = $companiesQuery->orderBy('name')->get();
        
        // Récupérer les départements distincts
        $departmentsQuery = Technician::select('department')->distinct()->whereNotNull('department');
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $departmentsQuery->whereHas('company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        $departments = $departmentsQuery->orderBy('department')->pluck('department')->filter()->values();
        
        return view('technicians.index', compact('technicians', 'companies', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de créer des techniciens.');
        }
        
        // Filtrer les companies pour n'afficher que celles des organisations où l'utilisateur a la permission de lecture
        if ($user->isOwner()) {
            $companies = Company::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $companies = Company::whereHas('organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            })->orderBy('name')->get();
        }
        
        return view('technicians.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de créer des techniciens.');
        }
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'department' => 'nullable|string|max:255',
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
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les techniciens.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir la company de ce technicien
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à ce technicien.');
            }
        }
        
        $technician->load([
            'company',
            'interventions' => function ($query) {
                $query->latest()->limit(10);
            },
        ])->loadAvg(
            ['interventions as average_rating' => fn ($query) => $query->whereNotNull('service_note')],
            'service_note',
        );
        
        return view('technicians.show', compact('technician'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technician $technician): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des techniciens.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir la company de ce technicien
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à ce technicien.');
            }
        }
        
        // Filtrer les companies pour n'afficher que celles des organisations où l'utilisateur a la permission de lecture
        if ($user->isOwner()) {
            $companies = Company::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $companies = Company::whereHas('organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            })->orderBy('name')->get();
        }
        
        return view('technicians.edit', compact('technician', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technician $technician): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des techniciens.');
        }
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'department' => 'nullable|string|max:255',
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
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteTechnicians()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des techniciens.');
        }
        
        $technician->delete();

        return redirect()->route('technicians.index')
            ->with('success', 'Technicien supprimé avec succès.');
    }
}
