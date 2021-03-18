<?php

namespace Tests\Unit;

use App\Models\Actor;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_get_actors()
    {
        $r = $this->getJson('api/actors');
        $r->assertStatus(200);
        $r->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'link',
                    'id',
                    'full_name',
                    'movies'
                ]
            ]
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_store_actor()
    {
        $r = $this->createActor("Mon prénom", "Mon nom de famille");
        $r->assertStatus(201);
        $r->assertJsonStructure([
            "first_name",
            "last_name",
            "updated_at",
            "created_at",
            "id"
        ]);
        $this->deleteLastActor();
    }

    /**
     * @param $firstName
     * @param $lastname
     * @return TestResponse
     */
    public function createActor(string $firstName, string $lastname): TestResponse
    {
        $actor = array('first_name' => $firstName, 'last_name' => $lastname);
        return $this->postJson('api/actors', $actor);
    }

    public function deleteLastActor(): TestResponse
    {
        $actor = Actor::all()->last();
        return $this->deleteJson("api/actors/" . $actor->id);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_show_actor()
    {
        $r = $this->getJson('api/actors/1');
        $r->assertStatus(200);
        $r->assertJsonStructure([
            'data' => [
                'link',
                'id',
                'full_name',
                'movies'
            ]
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_update_actor()
    {
        $this->createActor("Louis", "Totain");
        $actorId = Actor::all()->last()->id;
        $newActor = array('first_name' => 'Benjamin', 'last_name' => 'dav');

        $r = $this->putJson('api/actors/' . $actorId, $newActor);
        $r->assertStatus(200);
        $r->assertJsonStructure([
            "first_name",
            "last_name",
            "updated_at",
            "created_at",
            "id"
        ]);

        $this->deleteLastActor();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_delete_actor()
    {
        $r = $this->deleteLastActor();
        $r->assertStatus(204);
    }


}
