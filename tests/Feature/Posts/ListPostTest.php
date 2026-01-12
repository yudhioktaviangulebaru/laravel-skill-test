<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPostTest extends TestCase
{
    use RefreshDatabase;

    private string $route = 'posts.';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_can_view_list_posts()
    {
        $post1 = Post::factory()->make()->toArray();
        $post2 = Post::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route($this->route.'store'), $post1);
        $this->actingAs($this->user)->post(route($this->route.'store'), $post2);

        $post = Post::first();
        $post->is_draft = false;
        $post->published_at = now()->subDay();
        $post->save();

        $response = $this->actingAs($this->user)->get(route($this->route.'index'));
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_user_cannot_see_draft_post()
    {
        $post = Post::factory()->make()->toArray();
        $this->actingAs($this->user)->post(route($this->route.'store'), $post);

        $response = $this->actingAs($this->user)->get(route($this->route.'index'));
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(0, $data);
    }
}
