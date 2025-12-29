<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->get('search');

                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $user = auth()->user();
        
        // Seul le propriétaire peut voir toutes les organisations et groupes
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
            $groups = Group::with('organization')->orderBy('name')->get();
        } else {
            // Sinon, filtrer selon les permissions : l'utilisateur doit appartenir à un groupe avec can_write
            $organizations = $user->getOrganizationsWhereCanInvite();
            $groups = $user->getGroupsWhereCanInvite();
        }
        
        return view('users.create', compact('organizations', 'groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Validation des groupes : vérifier que l'utilisateur peut inviter dans ces groupes
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'is_admin' => 'nullable|boolean',
            'password' => 'required|string|min:8|confirmed',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
        ]);
        
        // Vérifier que l'utilisateur peut inviter dans les groupes sélectionnés
        if ($request->filled('group_ids') && !$user->isOwner()) {
            $allowedGroupIds = $user->getGroupsWhereCanInvite()->pluck('id')->toArray();
            $requestedGroupIds = $request->input('group_ids');
            
            foreach ($requestedGroupIds as $groupId) {
                if (!in_array($groupId, $allowedGroupIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Vous n\'avez pas la permission d\'inviter dans certains groupes sélectionnés.']);
                }
            }
        }

        // Sauvegarder le mot de passe en clair temporairement pour l'envoyer par email
        $temporaryPassword = $validated['password'];

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_admin' => $request->boolean('is_admin'),
            'password' => Hash::make($validated['password']),
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        // Attacher l'utilisateur aux groupes (sans permissions car elles sont sur le groupe)
        if ($request->filled('group_ids')) {
            $user->groups()->sync($request->input('group_ids'));
        }

        // Envoyer l'email de bienvenue avec le mot de passe temporaire
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user, $temporaryPassword));
        } catch (\Exception $e) {
            // En cas d'erreur d'envoi d'email, on continue quand même
            // L'utilisateur est créé, seul l'email n'a pas pu être envoyé
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur {$user->full_name} créé avec succès. Un email contenant ses identifiants a été envoyé à son adresse.");
    }

    public function edit(User $user): View
    {
        // Empêcher l'accès à la page d'édition du compte owner
        if ($user->isOwner()) {
            abort(403, 'Le compte propriétaire ne peut pas être modifié par un administrateur.');
        }

        $currentUser = auth()->user();
        
        // Seul le propriétaire peut voir toutes les organisations et groupes
        if ($currentUser->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
            $groups = Group::with('organization')->orderBy('name')->get();
        } else {
            // Sinon, filtrer selon les permissions
            $organizations = $currentUser->getOrganizationsWhereCanInvite();
            $groups = $currentUser->getGroupsWhereCanInvite();
        }
        
        $user->load('groups');
        
        return view('users.edit', compact('user', 'organizations', 'groups'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // Empêcher toute modification du compte owner par un administrateur
        if ($user->isOwner()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Le compte propriétaire ne peut pas être modifié par un administrateur. Le propriétaire doit modifier son mot de passe via son profil.');
        }

        $currentUser = auth()->user();
        
        // Pour les autres utilisateurs, modification complète autorisée
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:30',
            'is_admin' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
        ]);
        
        // Vérifier que l'utilisateur peut inviter dans les groupes sélectionnés
        if ($request->filled('group_ids') && !$currentUser->isOwner()) {
            $allowedGroupIds = $currentUser->getGroupsWhereCanInvite()->pluck('id')->toArray();
            $requestedGroupIds = $request->input('group_ids');
            
            foreach ($requestedGroupIds as $groupId) {
                if (!in_array($groupId, $allowedGroupIds)) {
                    return back()
                        ->withInput()
                        ->withErrors(['group_ids' => 'Vous n\'avez pas la permission d\'inviter dans certains groupes sélectionnés.']);
                }
            }
        }

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => true,
            ]);
        }

        // Synchroniser les groupes (sans permissions car elles sont sur le groupe)
        if ($request->filled('group_ids')) {
            $user->groups()->sync($request->input('group_ids'));
        } else {
            $user->groups()->sync([]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur {$user->full_name} mis à jour.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Empêcher la suppression du compte owner
        if ($user->isOwner()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Le compte propriétaire ne peut pas être supprimé.');
        }

        if ($request->user()->is($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur {$user->full_name} supprimé.");
    }
}
