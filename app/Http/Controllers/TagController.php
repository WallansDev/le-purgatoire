<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tag::query()->withCount('interventions');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('tags.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
            'description' => 'nullable|string|max:1000',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{3,6}$/'],
        ]);

        if (! empty($validated['color'])) {
            $validated['color'] = Str::start($validated['color'], '#');
        }

        Tag::create($validated);

        return redirect()->route('tags.index')->with('success', 'Tag créé avec succès.');
    }

    public function show(Tag $tag): View
    {
        $tag->load('interventions.technician.company');

        return view('tags.show', compact('tag'));
    }

    public function edit(Tag $tag): View
    {
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string|max:1000',
            'color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{3,6}$/'],
        ]);

        if (! empty($validated['color'])) {
            $validated['color'] = Str::start($validated['color'], '#');
        }

        $tag->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag mis à jour avec succès.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag supprimé avec succès.');
    }
}
