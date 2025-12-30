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
        
        // Filtrer pour n'afficher que les groupes des organisations où l'utilisateur a la permission de lecture des groupes
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithGroupsRead();
            $query->whereIn('organization_id', $organizationIds);
        }
        
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
        
        // Filtrer les organisations pour le filtre (seulement celles où l'utilisateur a la permission de lecture des groupes)
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithGroupsRead();
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }
        
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

        // Filtrer les organisations : seulement celles où l'utilisateur a la permission d'écriture des groupes
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithGroupsWrite();
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }

        // Récupérer toutes les permissions disponibles
        $permissions = \App\Models\Permission::orderBy('resource')->orderBy('action')->get();

        return view('groups.create', compact('organizations', 'permissions'));
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
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Vérifier que l'utilisateur peut créer des groupes dans cette organisation
        if (!$user->isOwner() && !in_array($validated['organization_id'], $user->getOrganizationIdsWithGroupsWrite())) {
            abort(403, 'Vous n\'avez pas la permission de créer des groupes dans cette organisation.');
        }

        // Vérifier l'unicité du nom dans l'organisation
        $exists = Group::where('organization_id', $validated['organization_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette organisation.']);
        }

        $group = Group::create([
            'organization_id' => $validated['organization_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_default' => $validated['is_default'] ?? false,
        ]);

        // Attacher les permissions
        if (isset($validated['permissions'])) {
            $group->permissions()->attach($validated['permissions'], ['scope' => 'organization']);
        }

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
        
        // Vérifier que l'utilisateur a la permission d'écriture sur les groupes de cette organisation
        if (!$user->isOwner() && !in_array($group->organization_id, $user->getOrganizationIdsWithGroupsWrite())) {
            abort(403, 'Vous n\'avez pas accès à ce groupe.');
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
        
        // Vérifier que l'utilisateur a la permission d'écriture sur les groupes de cette organisation
        if (!$user->isOwner() && !in_array($group->organization_id, $user->getOrganizationIdsWithGroupsWrite())) {
            abort(403, 'Vous n\'avez pas accès à ce groupe.');
        }
        
        // Filtrer les organisations : seulement celles où l'utilisateur a la permission d'écriture des groupes
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithGroupsWrite();
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }

        // Charger les permissions du groupe
        $group->load('permissions');
        
        // Récupérer toutes les permissions disponibles
        $permissions = \App\Models\Permission::orderBy('resource')->orderBy('action')->get();

        return view('groups.edit', compact('group', 'organizations', 'permissions'));
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

        // Vérifier que l'utilisateur a la permission d'écriture sur les groupes de cette organisation
        if (!$user->isOwner() && !in_array($group->organization_id, $user->getOrganizationIdsWithGroupsWrite())) {
            abort(403, 'Vous n\'avez pas accès à ce groupe.');
        }

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_default' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Vérifier que l'utilisateur peut modifier des groupes dans cette organisation (si l'organisation change)
        if (!$user->isOwner() && !in_array($validated['organization_id'], $user->getOrganizationIdsWithGroupsWrite())) {
            abort(403, 'Vous n\'avez pas la permission de déplacer ce groupe vers cette organisation.');
        }

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

        $group->update([
            'organization_id' => $validated['organization_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_default' => $validated['is_default'] ?? false,
        ]);

        // Synchroniser les permissions
        $group->permissions()->sync($validated['permissions'] ?? []);

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

        // Vérifier que l'utilisateur a la permission de suppression sur les groupes de cette organisation
        if (!$user->isOwner() && !in_array($group->organization_id, $user->getOrganizationIdsWithGroupsDelete())) {
            abort(403, 'Vous n\'avez pas accès à ce groupe.');
        }

        // Vérifier qu'il reste au moins un groupe dans l'organisation
        if ($group->organization->groups()->count() <= 1) {
            return back()
                ->with('error', 'Impossible de supprimer le dernier groupe d\'une organisation. Chaque organisation doit avoir au moins un groupe.');
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Groupe supprimé avec succès.');
    }
}

