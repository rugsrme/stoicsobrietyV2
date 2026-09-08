<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $latestPost = Post::query()
            ->published()
            ->with('author:id,name,display_name')
            ->latest('published_at')
            ->first();

        return Inertia::render('Dashboard', [
            'latestPost' => $latestPost,
        ]);
    }
}
