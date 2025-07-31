<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Folder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        dd(Folder::withCount(['emails' => fn($q) => $q->where('virtual_user_id', $user->id)])
            ->where('user_id', $user->id)
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->orderBy('is_system', 'desc')->toSql());
        return Inertia::render('Emails/Index', [
            'folders' => Folder::withCount(['emails' => fn($q) => $q->where('virtual_user_id', $user->id)])
                ->where('user_id', $user->id)
                ->orWhere('is_system', true)
                ->orderBy('name')
                ->orderBy('is_system', 'desc')
                ->get(),

            'emails' => Email::query()
                ->with(['categories', 'folder'])
                ->where('virtual_user_id', $user->id)
                ->where('folder_id', request('folder_id', 1)) // 1 - inbox
                ->orderBy('received_at', 'desc')
                ->paginate(20)
                ->withQueryString(),

            'current_folder' => request('folder_id', 1)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
