<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadTags()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les tags.');
        }
        
        $query = Tag::query()->with(['organization'])->withCount('interventions');

        // Filtrer les tags par organisation selon les permissions de l'utilisateur
        if (!$user->isOwner()) {
            // Récupérer les IDs des organisations où l'utilisateur a la permission de lecture des tags
            // Pour les tags, on utilise la même logique que pour les autres ressources
            // On peut utiliser les organisations où l'utilisateur a la permission tags_read
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'read');
            $query->whereIn('organization_id', $organizationIds);
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->get('organization_id'));
        }

        $tags = $query->orderBy('name')->paginate(15)->withQueryString();

        // Récupérer les organisations pour le filtre
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'read');
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }

        return view('tags.index', compact('tags', 'organizations'));
    }

    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTags()) {
            abort(403, 'Vous n\'avez pas la permission de créer des tags.');
        }
        
        // Filtrer les organisations : seulement celles où l'utilisateur a la permission d'écriture des tags
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'write');
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }
        
        return view('tags.create', compact('organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTags()) {
            abort(403, 'Vous n\'avez pas la permission de créer des tags.');
        }
        
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{3,6}$/'],
        ]);

        // Vérifier l'unicité du nom dans l'organisation
        $exists = Tag::where('organization_id', $validated['organization_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un tag avec ce nom existe déjà dans cette organisation.']);
        }

        // Vérifier que l'utilisateur peut créer des tags dans cette organisation
        if (!$user->isOwner() && !in_array($validated['organization_id'], $user->getOrganizationIdsWithPermission('tags', 'write'))) {
            abort(403, 'Vous n\'avez pas la permission de créer des tags dans cette organisation.');
        }

        if (! empty($validated['color'])) {
            $validated['color'] = Str::start($validated['color'], '#');
        }

        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag créé avec succès.');
    }

    public function show(Tag $tag): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadTags()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les tags.');
        }
        
        // Vérifier que l'utilisateur a accès à ce tag via son organisation
        if (!$user->isOwner() && $tag->organization_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'read');
            if (!in_array($tag->organization_id, $organizationIds)) {
                abort(403, 'Vous n\'avez pas accès à ce tag.');
            }
        }
        
        $tag->load(['organization', 'interventions.technician.company']);

        return view('tags.show', compact('tag'));
    }

    public function edit(Tag $tag): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTags()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des tags.');
        }
        
        // Vérifier que l'utilisateur a accès à ce tag via son organisation
        if (!$user->isOwner() && $tag->organization_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'write');
            if (!in_array($tag->organization_id, $organizationIds)) {
                abort(403, 'Vous n\'avez pas accès à ce tag.');
            }
        }
        
        // Filtrer les organisations : seulement celles où l'utilisateur a la permission d'écriture des tags
        if ($user->isOwner()) {
            $organizations = Organization::orderBy('name')->get();
        } else {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'write');
            $organizations = Organization::whereIn('id', $organizationIds)->orderBy('name')->get();
        }
        
        return view('tags.edit', compact('tag', 'organizations'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteTags()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des tags.');
        }
        
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{3,6}$/'],
        ]);

        // Vérifier l'unicité du nom dans l'organisation (sauf pour ce tag)
        $exists = Tag::where('organization_id', $validated['organization_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $tag->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un tag avec ce nom existe déjà dans cette organisation.']);
        }

        // Vérifier que l'utilisateur peut modifier des tags dans cette organisation
        if (!$user->isOwner() && !in_array($validated['organization_id'], $user->getOrganizationIdsWithPermission('tags', 'write'))) {
            abort(403, 'Vous n\'avez pas la permission de modifier des tags dans cette organisation.');
        }

        if (! empty($validated['color'])) {
            $validated['color'] = Str::start($validated['color'], '#');
        }

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag mis à jour avec succès.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteTags()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des tags.');
        }
        
        // Vérifier que l'utilisateur a accès à ce tag via son organisation
        if (!$user->isOwner() && $tag->organization_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithPermission('tags', 'delete');
            if (!in_array($tag->organization_id, $organizationIds)) {
                abort(403, 'Vous n\'avez pas accès à ce tag.');
            }
        }
        
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag supprimé avec succès.');
    }
}
