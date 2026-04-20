<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentPortfolioRequest;
use App\Http\Requests\UpdateStudentPortfolioRequest;
use App\Http\Requests\StorePortfolioEntryRequest;
use App\Http\Requests\UpdatePortfolioEntryRequest;
use App\Http\Resources\StudentPortfolioResource;
use App\Models\StudentPortfolio;
use App\Models\PortfolioEntry;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class StudentPortfolioController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StudentPortfolio::class, 'student_portfolio');
    }

    /**
     * Get current user's portfolio + تسجيل دخول الصفحة
     */
    public function getStudentPortfolio(Request $request)
    {
        ActivityLogger::log(
            'student_portfolio',
            'viewed_list',
            'Viewed my portfolio page',
            null,
            [],
            $request->user()
        );

        $user = $request->user();

        $portfolio = StudentPortfolio::where('user_id', $user->id)->first();

        if (!$portfolio) {
            return response()->json([
                'data' => null,
                'message' => 'لا توجد محفظة إنجاز لهذا الطالب بعد'
            ], 200);
        }

        ActivityLogger::log(
            'student_portfolio',
            'viewed',
            'Viewed own portfolio',
            $portfolio,
            ['portfolio_id' => $portfolio->id],
            $request->user()
        );

        return response()->json($portfolio);
    }

    /**
     * List portfolios
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'student_portfolio',
            'viewed_list',
            'Viewed student portfolios page',
            null,
            [],
            $request->user()
        );

        $query = StudentPortfolio::with(['user', 'trainingAssignment']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return StudentPortfolioResource::collection(
            $query->paginate($request->per_page ?? 15)
        );
    }

    /**
     * Show single portfolio
     */
    public function show(StudentPortfolio $studentPortfolio)
    {
        ActivityLogger::log(
            'student_portfolio',
            'viewed',
            'Viewed portfolio details',
            $studentPortfolio,
            ['portfolio_id' => $studentPortfolio->id],
            auth()->user()
        );

        return new StudentPortfolioResource(
            $studentPortfolio->load(['user', 'trainingAssignment', 'entries'])
        );
    }

    /**
     * Store portfolio
     */
    public function store(StoreStudentPortfolioRequest $request)
    {
        $portfolio = StudentPortfolio::create($request->validated());

        ActivityLogger::log(
            'student_portfolio',
            'created',
            'Created student portfolio',
            $portfolio,
            ['portfolio_id' => $portfolio->id],
            $request->user()
        );

        return new StudentPortfolioResource($portfolio);
    }

    /**
     * Update portfolio
     */
    public function update(UpdateStudentPortfolioRequest $request, StudentPortfolio $studentPortfolio)
    {
        ActivityLogger::log(
            'student_portfolio',
            'updated',
            'Updated student portfolio',
            $studentPortfolio,
            ['portfolio_id' => $studentPortfolio->id],
            $request->user()
        );

        return new StudentPortfolioResource($studentPortfolio);
    }

    /**
     * Delete portfolio
     */
    public function destroy(StudentPortfolio $studentPortfolio)
    {
        $id = $studentPortfolio->id;

        $studentPortfolio->delete();

        ActivityLogger::log(
            'student_portfolio',
            'deleted',
            'Deleted student portfolio',
            null,
            ['portfolio_id' => $id],
            auth()->user()
        );

        return response()->json([
            'message' => 'تم حذف ملف الإنجاز'
        ]);
    }

    // ================= Entries =================

    public function addEntry(StorePortfolioEntryRequest $request, StudentPortfolio $studentPortfolio)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('portfolio', 'public');
        }

        $data['student_portfolio_id'] = $studentPortfolio->id;

        $entry = PortfolioEntry::create($data);

        ActivityLogger::log(
            'portfolio_entry',
            'created',
            'Added portfolio entry',
            $entry,
            [
                'entry_id' => $entry->id,
                'portfolio_id' => $studentPortfolio->id,
            ],
            $request->user()
        );

        return response()->json($entry, 201);
    }

    public function updateEntry(UpdatePortfolioEntryRequest $request, PortfolioEntry $entry)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($entry->file_path) {
                Storage::disk('public')->delete($entry->file_path);
            }
            $data['file_path'] = $request->file('file')->store('portfolio', 'public');
        }

        $entry->update($data);

        ActivityLogger::log(
            'portfolio_entry',
            'updated',
            'Updated portfolio entry',
            $entry,
            ['entry_id' => $entry->id],
            $request->user()
        );

        return response()->json($entry);
    }

    public function deleteEntry(PortfolioEntry $entry)
    {
        $id = $entry->id;

        $entry->delete();

        ActivityLogger::log(
            'portfolio_entry',
            'deleted',
            'Deleted portfolio entry',
            null,
            ['entry_id' => $id],
            auth()->user()
        );

        return response()->json([
            'message' => 'تم حذف الإدخال'
        ]);
    }
}