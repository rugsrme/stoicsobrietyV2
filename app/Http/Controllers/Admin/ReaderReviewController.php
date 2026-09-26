<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReaderReviewRequest;
use App\Http\Requests\Admin\UpdateReaderReviewRequest;
use App\Models\ReaderReview;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReaderReviewController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/reader-reviews/Index', [
            'reviews' => ReaderReview::query()->orderBy('sort_order')->latest('id')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/reader-reviews/Create');
    }

    public function store(StoreReaderReviewRequest $request): RedirectResponse
    {
        ReaderReview::create([...$request->validated(), 'sort_order' => $request->integer('sort_order')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Reader review added.')]);

        return to_route('admin.reader-reviews.index');
    }

    public function edit(ReaderReview $readerReview): Response
    {
        return Inertia::render('admin/reader-reviews/Edit', [
            'review' => $readerReview,
        ]);
    }

    public function update(UpdateReaderReviewRequest $request, ReaderReview $readerReview): RedirectResponse
    {
        $data = $request->validated();

        if (array_key_exists('sort_order', $data)) {
            $data['sort_order'] = (int) $data['sort_order'];
        }

        $readerReview->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Reader review updated.')]);

        return back();
    }

    public function destroy(ReaderReview $readerReview): RedirectResponse
    {
        $readerReview->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Reader review deleted.')]);

        return to_route('admin.reader-reviews.index');
    }
}
