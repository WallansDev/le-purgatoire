<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Tag;
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
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les interventions.');
        }
        
        $query = Intervention::with(['technician.company', 'tags']);
        
        // Filtrer les interventions pour n'afficher que celles dont le technicien appartient à une company
        // qui fait partie d'une organisation où l'utilisateur a la permission de lecture des companies
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $query->where(function ($q) use ($organizationIds) {
                // Soit l'intervention a un technicien dont la company est accessible
                $q->whereHas('technician.company.organizations', function ($orgQuery) use ($organizationIds) {
                    $orgQuery->whereIn('organizations.id', $organizationIds);
                })
                // Soit l'intervention n'a pas de technicien (technician_id est null)
                ->orWhereNull('technician_id');
            });
        }
        
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

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->get('tag'));
            });
        }
        
        // Recherche par date si fournie
        if ($request->filled('date_search')) {
            $dateSearch = $request->get('date_search');
            $query->whereDate('scheduled_at', $dateSearch);
        }
        
        $interventions = $query->latest('scheduled_at')->paginate(15)->withQueryString();
        $tags = Tag::orderBy('name')->get();
        
        return view('interventions.index', compact('interventions', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de créer des interventions.');
        }
        
        $query = Technician::where('is_active', true)
            ->with('company')
            ->orderBy('last_name')
            ->orderBy('first_name');
        
        // Filtrer les techniciens pour n'afficher que ceux dont la company appartient à une organisation
        // où l'utilisateur a la permission de lecture des companies
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $query->whereHas('company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        
        $technicians = $query->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('interventions.create', compact('technicians', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de créer des interventions.');
        }
        
        $validated = $request->validate([
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_at' => 'required|date',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|integer|min:0|max:5',
            'service_note' => 'nullable|integer|min:0|max:5',
            'no_note' => 'nullable|boolean',
            'no_service_note' => 'nullable|boolean',
            'is_completed' => 'nullable|boolean',
            'non_completion_reason' => 'nullable|string|required_if:is_completed,false',
            'notes' => 'nullable|string',
            'client_comments' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Vérifier que le technicien sélectionné est accessible si un technicien est fourni
        if (isset($validated['technician_id']) && $validated['technician_id'] !== null && !$user->isOwner()) {
            $technician = Technician::with('company')->findOrFail($validated['technician_id']);
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                return back()
                    ->withInput()
                    ->withErrors(['technician_id' => 'Vous n\'avez pas accès à ce technicien.']);
            }
        }

        // Si "Non noté" est coché, mettre note à NULL
        if ($request->boolean('no_note')) {
            $validated['note'] = null;
        }

        if ($request->boolean('no_service_note')) {
            $validated['service_note'] = null;
        }

        $intervention = Intervention::create($validated);

        $intervention->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Intervention $intervention): View
    {
        $user = auth()->user();
        
        // Vérifier la permission de lecture
        if (!$user->canReadInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de consulter les interventions.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir cette intervention
        // (via le technicien et sa company)
        if (!$user->isOwner() && $intervention->technician_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $intervention->technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette intervention.');
            }
        }
        
        $intervention->load('technician.company', 'tags');
        
        return view('interventions.show', compact('intervention'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intervention $intervention): View
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des interventions.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir cette intervention
        // (via le technicien et sa company)
        if (!$user->isOwner() && $intervention->technician_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $intervention->technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette intervention.');
            }
        }
        
        $query = Technician::where('is_active', true)
            ->with('company')
            ->orderBy('last_name')
            ->orderBy('first_name');
        
        // Filtrer les techniciens pour n'afficher que ceux dont la company appartient à une organisation
        // où l'utilisateur a la permission de lecture des companies
        if (!$user->isOwner()) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $query->whereHas('company.organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });
        }
        
        $technicians = $query->get();
        
        $tags = Tag::orderBy('name')->get();

        return view('interventions.edit', compact('intervention', 'technicians', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intervention $intervention): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission d'écriture
        if (!$user->canWriteInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de modifier des interventions.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir cette intervention
        // (via le technicien et sa company)
        if (!$user->isOwner() && $intervention->technician_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $intervention->technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette intervention.');
            }
        }
        
        $validated = $request->validate([
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_at' => 'required|date',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|integer|min:0|max:5',
            'service_note' => 'nullable|integer|min:0|max:5',
            'no_note' => 'nullable|boolean',
            'no_service_note' => 'nullable|boolean',
            'is_completed' => 'nullable|boolean',
            'non_completion_reason' => 'nullable|string|required_if:is_completed,false|required_if:is_completed,0',
            'notes' => 'nullable|string',
            'client_comments' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Vérifier que le technicien sélectionné est accessible si un technicien est fourni
        if (isset($validated['technician_id']) && $validated['technician_id'] !== null && !$user->isOwner()) {
            $technician = Technician::with('company')->findOrFail($validated['technician_id']);
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                return back()
                    ->withInput()
                    ->withErrors(['technician_id' => 'Vous n\'avez pas accès à ce technicien.']);
            }
        }

        $validated['is_completed'] = $request->boolean('is_completed');

        if ($validated['is_completed']) {
            $validated['non_completion_reason'] = null;
        }

        // Si "Non noté" est coché, mettre note à NULL
        if ($request->boolean('no_note')) {
            $validated['note'] = null;
        }

        if ($request->boolean('no_service_note')) {
            $validated['service_note'] = null;
        }

        $intervention->update($validated);
        $intervention->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intervention $intervention): RedirectResponse
    {
        $user = auth()->user();
        
        // Vérifier la permission de suppression
        if (!$user->canDeleteInterventions()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer des interventions.');
        }
        
        // Vérifier que l'utilisateur a le droit de voir cette intervention
        // (via le technicien et sa company)
        if (!$user->isOwner() && $intervention->technician_id !== null) {
            $organizationIds = $user->getOrganizationIdsWithCompaniesRead();
            $hasAccess = $intervention->technician->company->organizations()
                ->whereIn('organizations.id', $organizationIds)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Vous n\'avez pas accès à cette intervention.');
            }
        }
        
        $intervention->delete();

        return redirect()->route('interventions.index')
            ->with('success', 'Intervention supprimée avec succès.');
    }
}
