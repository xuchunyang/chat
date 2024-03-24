<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Room $room)
    {
        Gate::authorize('viewAny', Message::class);

        $messages = $room->messages()->with('user')->get();

        return MessageResource::collection($messages);
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
    public function store(StoreMessageRequest $request, Room $room)
    {
        $request->user()->can('create', [Message::class, $room]);

        $message = Message::create($request->validated());

        $message->load('user');

        return new MessageResource($message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        $message->update($request->validated());

        return new MessageResource($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        Gate::authorize('delete', $message);

        $message->delete();

        return new MessageResource($message);
    }
}
