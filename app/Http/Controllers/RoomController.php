<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Room::class);

        return RoomResource::collection(Room::with([
            'user',
            // NOTE 获得最近的 999 个消息，注意顺序反了，Laravel 中没法儿简单地实现这个需求
            // https://stackoverflow.com/a/65947845/2999892
            'messages' => function (Builder $query) {
                $query->latest()->limit(999)->with('user');
            },
            'messages.user',
        ])->get());
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
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        $room->load([
            'user',
            'messages.user',
        ]);

        return new RoomResource($room);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        Gate::authorize('view', $room);

        return new RoomResource($room);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return new RoomResource($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        Gate::authorize('delete', $room);

        $room->delete();

        return new RoomResource($room);
    }
}
