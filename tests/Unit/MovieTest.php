<?php

namespace Tests\Unit;

use App\Models\Movie;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MovieTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_get_movies()
    {
        $r = $this->getJson('api/movies');
        $r->assertStatus(200);
        $r->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'link',
                    'id',
                    'title',
                    'description',
                    'release_date',
                    'director',
                    'producer',
                    'category' => [
                        'link',
                        'id',
                        'title',
                        'description',
                        'movies'
                    ],
                    'actors'
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
        $r = $this->createMovie();
        $r->assertStatus(201);
        $r->assertJsonStructure([
            "title",
            "description",
            "release_date",
            "director",
            "producer",
            'category_id',
            'updated_at',
            'created_at',
            'id'
        ]);
        $this->deleteLastMovie();
    }

    /**
     * @return TestResponse
     */
    public function createMovie(): TestResponse
    {
        $movie = Movie::factory()->make()->attributesToArray();
        $movie["category"] = 1;
        $movie["release_date"] = "2003-04-05";

        return $this->postJson('api/movies', $movie);
    }

    public function deleteLastMovie(): TestResponse
    {
        $movie = Movie::all()->last();
        $this->assertDatabaseMissing('movies',$movie);
        return $this->deleteJson("api/movies/" . $movie->id);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_show_movie()
    {
        $r = $this->getJson('api/movies/1');
        $r->assertStatus(200);
        $r->assertJsonStructure([
            'data' => [
                'link',
                'id',
                'title',
                'description',
                'release_date',
                'director',
                'producer',
                'category' => [
                    'link',
                    'id',
                    'title',
                    'description',
                    'movies'],
                'actors'
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
        $this->createMovie();
        $movieId = Movie::all()->last()->id;

        $newMovie = Movie::factory()->make()->attributesToArray();
        $newMovie["category"] = 1;
        $newMovie["release_date"] = "2003-04-05";

        $r = $this->putJson('api/movies/' . $movieId, $newMovie);
        $r->assertStatus(200);
        $r->assertJsonStructure([
            'id',
            "title",
            "description",
            "release_date",
            "director",
            "producer",
            'category_id',
            'updated_at',
            'created_at'
        ]);

        $this->deleteLastMovie();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_delete_actor()
    {
        $r = $this->deleteLastMovie();
        $r->assertStatus(204);

    }


}
