<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadGroups()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les groupes.');
        }
        
        $query = Group::with(['organization', 'users'])->withCount('users');
        
        // Recherche par nom ou organisation
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('organization', function ($orgQuery) use ($search) {
                      $orgQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filtre par organisation
        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->get('organization_id'));
        }
        
        $groups = $query->latest()->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get();
        
        return view('groups.index', compact('groups', 'organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de créer des groupes.');
        }
        
        $organizations = Organization::orderBy('name')->get();
        return view('groups.create', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de créer des groupes.');
        }
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
            // Permissions Companies
            'companies_read' => 'nullable|boolean',
            'companies_write' => 'nullable|boolean',
            'companies_delete' => 'nullable|boolean',
            // Permissions Technicians
            'technicians_read' => 'nullable|boolean',
            'technicians_write' => 'nullable|boolean',
            'technicians_delete' => 'nullable|boolean',
            // Permissions Interventions
            'interventions_read' => 'nullable|boolean',
            'interventions_write' => 'nullable|boolean',
            'interventions_delete' => 'nullable|boolean',
            // Permissions Organizations
            'organizations_read' => 'nullable|boolean',
            'organizations_write' => 'nullable|boolean',
            'organizations_delete' => 'nullable|boolean',
            // Permissions Groups
            'groups_read' => 'nullable|boolean',
            'groups_write' => 'nullable|boolean',
            'groups_delete' => 'nullable|boolean',
            // Permission Invite
            'can_invite' => 'nullable|boolean',
        ]);

        // Vérifier l'unicité du nom dans l'organisation
        $exists = Group::where('organization_id', $validated['organization_id'])
            ->where('name', $validated['name'])
            ->exists();
            
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette organisation.']);
        }

        Group::create($validated);

        return redirect()->route('groups.index')
            ->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadGroups()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les groupes.');
        }
        
        $group->load(['organization', 'users']);
        
        // Récupérer tous les utilisateurs pour le formulaire d'ajout
        $allUsers = User::orderBy('first_name')->orderBy('last_name')->get();
        $groupUserIds = $group->users->pluck('id')->toArray();
        
        return view('groups.show', compact('group', 'allUsers', 'groupUserIds'));
    }
    
    /**
     * Add a user to the group.
     */
    public function addMember(Request $request, Group $group): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture (nécessaire pour modifier un groupe)
        if (!$user->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des groupes.');
        }
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $userId = $validated['user_id'];
        
        // Vérifier si l'utilisateur n'est pas déjà membre
        if ($group->users()->where('users.id', $userId)->exists()) {
            return back()
                ->with('error', 'Cet utilisateur est déjà membre de ce groupe.');
        }
        
        $group->users()->attach($userId);
        
        return back()
            ->with('success', 'Utilisateur ajouté au groupe avec succès.');
    }
    
    /**
     * Remove a user from the group.
     */
    public function removeMember(Group $group, User $user): RedirectResponse
    {
        $currentUser = auth()->user();
        
        // Vérifier la permission d'écriture (nécessaire pour modifier un groupe)
        if (!$currentUser->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des groupes.');
        }
        
        $group->users()->detach($user->id);
        
        return back()
            ->with('success', 'Utilisateur retiré du groupe avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des groupes.');
        }
        
        $organizations = Organization::orderBy('name')->get();
        return view('groups.edit', compact('group', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des groupes.');
        }
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
            // Permissions Companies
            'companies_read' => 'nullable|boolean',
            'companies_write' => 'nullable|boolean',
            'companies_delete' => 'nullable|boolean',
            // Permissions Technicians
            'technicians_read' => 'nullable|boolean',
            'technicians_write' => 'nullable|boolean',
            'technicians_delete' => 'nullable|boolean',
            // Permissions Interventions
            'interventions_read' => 'nullable|boolean',
            'interventions_write' => 'nullable|boolean',
            'interventions_delete' => 'nullable|boolean',
            // Permissions Organizations
            'organizations_read' => 'nullable|boolean',
            'organizations_write' => 'nullable|boolean',
            'organizations_delete' => 'nullable|boolean',
            // Permissions Groups
            'groups_read' => 'nullable|boolean',
            'groups_write' => 'nullable|boolean',
            'groups_delete' => 'nullable|boolean',
            // Permission Invite
            'can_invite' => 'nullable|boolean',
        ]);

        // Vérifier l'unicité du nom dans l'organisation (sauf pour ce groupe)
        $exists = Group::where('organization_id', $validated['organization_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $group->id)
            ->exists();
            
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette organisation.']);
        }

        $group->update($validated);

        return redirect()->route('groups.index')
            ->with('success', 'Groupe mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteGroups()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des groupes.');
        }
        
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Groupe supprimé avec succès.');
    }
}

