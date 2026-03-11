<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmartListRequest;
use App\Http\Requests\UpdateSmartListRequest;
use App\Models\SmartList;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SmartListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $lists = SmartList::all()
            ->map(function (SmartList $list) {
                return [
                    'model' => $list,
                    'count' => $list->itemsCount(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('smart-lists.index', [
            'lists' => $lists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('smart-lists.create', [
            'smartList' => new SmartList,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSmartListRequest $request): RedirectResponse
    {
        $smartList = SmartList::create($request->validated());

        return redirect()
            ->route('smart-lists.index')
            ->with('status', 'Smart list created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SmartList $smartList): View
    {
        $items = $smartList->itemsQuery()->paginate(25);

        return view('smart-lists.show', [
            'smartList' => $smartList,
            'items' => $items,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SmartList $smartList): View
    {
        return view('smart-lists.edit', [
            'smartList' => $smartList,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSmartListRequest $request, SmartList $smartList): RedirectResponse
    {
        $smartList->update($request->validated());

        return redirect()
            ->route('smart-lists.show', $smartList)
            ->with('status', 'Smart list updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SmartList $smartList): RedirectResponse
    {
        $smartList->delete();

        return redirect()
            ->route('smart-lists.index')
            ->with('status', 'Smart list deleted.');
    }
}
