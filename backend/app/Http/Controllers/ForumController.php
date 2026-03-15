<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Http\Requests\StoreForumRequest;
use App\Http\Requests\UpdateForumRequest;
use App\Services\ForumService;

class ForumController extends Controller
{
    public function __construct(protected ForumService $forumService){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Forum::with(['user','comments.user'])->orderByDesc('created_at');
        
        return $query->paginate(10);
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
    public function store(StoreForumRequest $request)
    {
        $forum = $this->forumService->create($request->validated());

        return response()->json([
            'message' => 'Forum created successfully',
            'data' => $forum
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Forum $forum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Forum $forum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateForumRequest $request, Forum $forum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Forum $forum)
    {
        //
    }
}
