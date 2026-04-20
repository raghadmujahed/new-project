<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortfolioEntryRequest;
use App\Http\Requests\UpdatePortfolioEntryRequest;
use App\Http\Resources\PortfolioEntryResource;
use App\Models\PortfolioEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class PortfolioEntryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PortfolioEntry::class, 'portfolio_entry');
    }

    public function index(Request $request)
    {
        ActivityLogger::log(
            'portfolio_entry',
            'view_list',
            'Viewed portfolio entries page',
            null,
            [],
            $request->user()
        );

        $query = PortfolioEntry::with('studentPortfolio');

        if ($request->has('student_portfolio_id')) {
            $query->where('student_portfolio_id', $request->student_portfolio_id);
        }

        $entries = $query->latest()->paginate($request->per_page ?? 15);

        return PortfolioEntryResource::collection($entries);
    }

    public function store(StorePortfolioEntryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('portfolio', 'public');
        }

        $entry = PortfolioEntry::create($data);

        ActivityLogger::log(
            'portfolio_entry',
            'created',
            'Created portfolio entry',
            $entry,
            [
                'entry_id' => $entry->id,
                'student_portfolio_id' => $entry->student_portfolio_id,
            ],
            $request->user()
        );

        return new PortfolioEntryResource($entry);
    }

    public function show(PortfolioEntry $portfolioEntry, Request $request)
    {
        ActivityLogger::log(
            'portfolio_entry',
            'view',
            'Viewed portfolio entry',
            $portfolioEntry,
            ['entry_id' => $portfolioEntry->id],
            $request->user()
        );

        return new PortfolioEntryResource($portfolioEntry->load('studentPortfolio'));
    }

    public function update(UpdatePortfolioEntryRequest $request, PortfolioEntry $portfolioEntry)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($portfolioEntry->file_path) {
                Storage::disk('public')->delete($portfolioEntry->file_path);
            }

            $data['file_path'] = $request->file('file')->store('portfolio', 'public');
        }

        $portfolioEntry->update($data);

        ActivityLogger::log(
            'portfolio_entry',
            'updated',
            'Updated portfolio entry',
            $portfolioEntry,
            ['entry_id' => $portfolioEntry->id],
            $request->user()
        );

        return new PortfolioEntryResource($portfolioEntry);
    }

    public function destroy(PortfolioEntry $portfolioEntry, Request $request)
    {
        if ($portfolioEntry->file_path) {
            Storage::disk('public')->delete($portfolioEntry->file_path);
        }

        $id = $portfolioEntry->id;

        $portfolioEntry->delete();

        ActivityLogger::log(
            'portfolio_entry',
            'deleted',
            'Deleted portfolio entry',
            null,
            ['entry_id' => $id],
            $request->user()
        );

        return response()->json(['message' => 'تم حذف المدخل بنجاح']);
    }
}