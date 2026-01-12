<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPostTest extends TestCase
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
    public function test_user_can_view_post()
    {
        $postRequest = Post::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();
        $post->is_draft = false;
        $post->published_at = now()->subDay();
        $post->save();

        $response = $this->actingAs($this->user)->get(route($this->route.'show', $post->id));
        $response->assertOk();
    }

    public function test_user_cannot_view_draft_or_scheduled_post()
    {
        $postRequest = Post::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();

        $response = $this->actingAs($this->user)->get(route($this->route.'show', $post->id));
        $response->assertNotFound();
    }
}
