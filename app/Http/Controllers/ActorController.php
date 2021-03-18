<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActorCollection;
use App\Http\Resources\ActorResource;
use App\Models\Actor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ActorCollection
     */
    public function index()
    {
        $actors = Actor::with('movies')->whereIn('id', [3, 4])->get(['id', 'first_name', 'last_name']);
        return new ActorCollection($actors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ActorResource
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $actor = Actor::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
            if ($request->has('movies')) $actor->movies()->attach($request->movies);
            return $actor;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Actor $actor
     * @return ActorResource
     */
    public function show(Actor $actor)
    {
        return new ActorResource($actor->load('movies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Actor $actor
     * @return ActorResource
     */
    public function update(Request $request, Actor $actor)
    {
        return DB::transaction(function () use ($actor, $request) {
            $actor->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            if ($request->has('movies')) {
                $actor->movies()->detach();
                $actor->movies()->attach($request->movies);
            }

            return $actor;
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Actor $actor
     * @return bool|Application|ResponseFactory|Response
     */
    public function destroy(Actor $actor)
    {
        $deleted = DB::transaction(function () use ($actor) {
            $r = $actor->movies()->count() > 0 ? $actor->movies()->detach() : true;
            $m = $actor->delete();
            return $r && $m;
        });
        if ($deleted) {
            return response('Deleted', 204)->header('Content-Type', 'text/plain');
        }
        abort(404);
    }
}
