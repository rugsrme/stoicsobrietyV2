<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function show(User $user): Response
    {
        $user->loadCount('orders');

        return Inertia::render('admin/users/Show', [
            'user' => $user,
            'orders' => $user->orders()->latest('created_at')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'is_admin' => ['required', 'boolean'],
        ]);

        if ($user->is($request->user()) && ! $data['is_admin']) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('You cannot remove your own admin access.')]);

            return back();
        }

        $user->update(['is_admin' => $data['is_admin']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return back();
    }
}
