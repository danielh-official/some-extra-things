<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmartListRequest;
use App\Http\Requests\UpdateSmartListRequest;
use App\Models\Item;
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
        $invert = request()->boolean('invert');
        $kanban = request()->input('kanban', 'vertical');

        $items = $smartList->itemsQuery($invert)->get();

        $bucketOrder = ['Inbox', 'Today', 'Upcoming', 'Anytime', 'Someday', 'Logbook'];

        $grouped = $items
            ->groupBy(fn (Item $item) => $this->bucket($item))
            ->sortBy(fn ($_, $key) => array_search($key, $bucketOrder));

        return view('smart-lists.show', [
            'smartList' => $smartList,
            'grouped' => $grouped,
            'invert' => $invert,
            'kanban' => $kanban,
        ]);
    }

    /**
     * Classify an item into a display bucket.
     */
    protected function bucket(Item $item): string
    {
        if ($item->is_inbox) {
            return 'Inbox';
        }

        if ($item->is_logged) {
            return 'Logbook';
        }

        if ($item->start_date && $item->start_date->lte(today())) {
            return 'Today';
        }

        if ($item->start_date && $item->start_date->gt(today())) {
            return 'Upcoming';
        }

        if ($item->start === 'Someday') {
            return 'Someday';
        }

        return 'Anytime';
    }

    /**
     * Show the create form pre-filled with data from another smart list.
     */
    public function duplicate(SmartList $smartList): View
    {
        $copy = $smartList->replicate();

        return view('smart-lists.create', [
            'smartList' => $copy,
            'heading' => 'Duplicate smart list',
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
