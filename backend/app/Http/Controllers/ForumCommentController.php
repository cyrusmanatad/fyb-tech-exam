<?php

namespace App\Http\Controllers;

use App\Models\ForumsComment;
use App\Http\Requests\StoreForumCommentRequest;
use App\Http\Requests\UpdateForumRequest;
use App\Services\ForumCommentService;

class ForumCommentController extends Controller
{
    public function __construct(protected ForumCommentService $forumCommentService){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ForumsComment::with(['user'])->orderByDesc('created_at');
        
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
    public function store(StoreForumCommentRequest $request)
    {
        $forum = $this->forumCommentService->create($request->validated());

        return response()->json([
            'message' => 'Forum created successfully',
            'data' => $forum
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ForumsComment $forum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ForumsComment $forum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateForumRequest $request, ForumsComment $forum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ForumsComment $forum)
    {
        //
    }
}
