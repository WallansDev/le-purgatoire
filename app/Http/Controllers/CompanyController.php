<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Ne pas bloquer l'accès même si l'utilisateur n'a pas la permission
        // La page se chargera mais n'affichera rien si l'utilisateur n'a pas accès
        
        $query = Company::withCount('technicians');
        
        // Filtrer selon les organisations où l'utilisateur a la permission companies_read (sauf le propriétaire)
        if (!$user->isOwner()) {
            // Récupérer les IDs des organisations où l'utilisateur a la permission companies_read
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            
            // Si l'utilisateur n'a accès à aucune organisation, filtrer pour ne rien retourner
            // La page se chargera quand même mais affichera "Aucune entreprise trouvée"
            if (empty($organizationIds)) {
                $query->whereRaw('1 = 0'); // Condition impossible pour ne rien retourner
            } else {
                // Filtrer les entreprises liées à ces organisations
                $query->whereHas('organizations', function ($q) use ($organizationIds) {
                    $q->whereIn('organizations.id', $organizationIds);
                });
            }
        }
        
        // Recherche par nom ou SIRET
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('siret', 'like', "%{$search}%");
            });
        }
        
        $companies = $query->latest()->paginate(15)->withQueryString();
        
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture (nécessaire pour créer)
        if (!$user->canWriteCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de créer des entreprises.');
        }
        
        // Récupérer les organisations où l'utilisateur a la permission companies_write (sauf le propriétaire)
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizations = Organization::whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('groups.companies_write', true);
            })->orderBy('name')->get();
        }
        
        return view('companies.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de créer des entreprises.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'siret' => 'nullable|string|max:14',
            'logo_path' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ]);

        // Vérifier que l'utilisateur a la permission companies_write pour les organisations sélectionnées (sauf le propriétaire)
        if ($request->filled('organization_ids') && !$user->isOwner()) {
            $allowedOrganizationIds = Organization::whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('groups.companies_write', true);
            })->pluck('id')->toArray();
            
            $requestedOrganizationIds = $request->input('organization_ids');
            
            foreach ($requestedOrganizationIds as $organizationId) {
                if (!in_array($organizationId, $allowedOrganizationIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(['organization_ids' => 'Vous n\'avez pas la permission de créer des entreprises pour certaines organisations sélectionnées.']);
                }
            }
        }

        $company = Company::create($validated);
        
        // Attacher les organisations à l'entreprise
        if ($request->filled('organization_ids')) {
            $company->organizations()->sync($request->input('organization_ids'));
        }

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les entreprises.');
        }
        
        // Vérifier que l'utilisateur a la permission companies_read pour cette entreprise (sauf le propriétaire)
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            
            if (empty($organizationIds) || !$company->organizations()->whereIn('organizations.id', $organizationIds)->exists()) {
                abort(403, 'Vous n\'avez pas accès à cette entreprise.');
            }
        }
        
        $company->load(['technicians' => function ($query) {
            $query->withCount('interventions')->latest();
        }, 'organizations']);
        
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture (nécessaire pour modifier)
        if (!$user->canWriteCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des entreprises.');
        }
        
        // Vérifier que l'utilisateur a la permission companies_read pour cette entreprise (sauf le propriétaire)
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            
            if (empty($organizationIds) || !$company->organizations()->whereIn('organizations.id', $organizationIds)->exists()) {
                abort(403, 'Vous n\'avez pas accès à cette entreprise.');
            }
        }
        
        // Récupérer les organisations où l'utilisateur a la permission companies_write (sauf le propriétaire)
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizations = Organization::whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('groups.companies_write', true);
            })->orderBy('name')->get();
        }
        
        $company->load('organizations');
        
        return view('companies.edit', compact('company', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des entreprises.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'siret' => 'nullable|string|max:14',
            'logo_path' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'organization_ids' => 'nullable|array',
            'organization_ids.*' => 'exists:organizations,id',
        ]);

        // Vérifier que l'utilisateur a la permission companies_write pour les organisations sélectionnées (sauf le propriétaire)
        if ($request->filled('organization_ids') && !$user->isOwner()) {
            $allowedOrganizationIds = Organization::whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('groups.companies_write', true);
            })->pluck('id')->toArray();
            
            $requestedOrganizationIds = $request->input('organization_ids');
            
            foreach ($requestedOrganizationIds as $organizationId) {
                if (!in_array($organizationId, $allowedOrganizationIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(['organization_ids' => 'Vous n\'avez pas la permission de modifier des entreprises pour certaines organisations sélectionnées.']);
                }
            }
        }

        $company->update($validated);
        
        // Synchroniser les organisations avec l'entreprise
        // Si organization_ids est présent dans la requête (même vide), on synchronise
        if ($request->has('organization_ids')) {
            $company->organizations()->sync($request->input('organization_ids', []));
        }
        // Si organization_ids n'est pas présent, on ne modifie pas les organisations existantes

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteCompanies()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des entreprises.');
        }
        
        // Vérifier que l'utilisateur a la permission companies_delete pour cette entreprise (sauf le propriétaire)
        if (!$user->isOwner()) {
            // Vérifier d'abord la permission de lecture
            $readOrganizationIds = $user->getOrganizationIdsWithCompaniesRead();
            
            if (empty($readOrganizationIds) || !$company->organizations()->whereIn('organizations.id', $readOrganizationIds)->exists()) {
                abort(403, 'Vous n\'avez pas accès à cette entreprise.');
            }
            
            // Vérifier la permission de suppression
            $deleteOrganizationIds = Organization::whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('groups.companies_delete', true);
            })->pluck('id')->toArray();
            
            if (empty($deleteOrganizationIds) || !$company->organizations()->whereIn('organizations.id', $deleteOrganizationIds)->exists()) {
                abort(403, 'Vous n\'avez pas la permission de supprimer cette entreprise.');
            }
        }
        
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }
}

