<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les organisations.');
        }
        
        $query = Organization::withCount('groups');
        
        // Filtrer pour n'afficher que les organisations auxquelles l'utilisateur appartient (sauf le propriétaire)
        if (!$user->isOwner()) {
            $query->whereHas('groups.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        
        // Recherche par nom ou slug
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }
        
        $organizations = $query->latest()->paginate(15)->withQueryString();
        
        return view('organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de créer des organisations.');
        }
        
        return view('organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de créer des organisations.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug',
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:16',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Organization::create($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Organisation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les organisations.');
        }
        
        // Vérifier que l'utilisateur appartient à l'organisation (sauf le propriétaire)
        if (!$user->isOwner() && !$organization->groups()->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists()) {
            abort(403, 'Vous n\'avez pas accès à cette organisation.');
        }
        
        $organization->load(['groups' => function ($query) use ($user) {
            $query->withCount('users')->latest();

            // Les groupes sont déjà filtrés au niveau de l'organisation, pas besoin de filtrer supplémentaire
        }]);
        
        return view('organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des organisations.');
        }
        
        // Vérifier que l'utilisateur appartient à l'organisation (sauf le propriétaire)
        if (!$user->isOwner() && !$organization->groups()->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists()) {
            abort(403, 'Vous n\'avez pas accès à cette organisation.');
        }
        
        return view('organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des organisations.');
        }
        
        // Vérifier que l'utilisateur appartient à l'organisation (sauf le propriétaire)
        if (!$user->isOwner() && !$organization->groups()->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists()) {
            abort(403, 'Vous n\'avez pas accès à cette organisation.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug,' . $organization->id,
            'description' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:16',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Organisation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteOrganizations()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des organisations.');
        }
        
        // Vérifier que l'utilisateur appartient à l'organisation (sauf le propriétaire)
        if (!$user->isOwner() && !$organization->groups()->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists()) {
            abort(403, 'Vous n\'avez pas accès à cette organisation.');
        }
        
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Organisation supprimée avec succès.');
    }
}

