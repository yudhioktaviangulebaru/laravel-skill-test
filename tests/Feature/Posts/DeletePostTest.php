<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Metadata\Test;
use Tests\TestCase;

class DeletePostTest extends TestCase
{
    use RefreshDatabase;

    private string $route = 'posts.';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    #[Test]
    public function test_allow_authorized_user_to_delete_post()
    {
        $postRequest = Post::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();

        $response = $this->actingAs($this->user)->delete(route($this->route.'destroy', $post));
        $response->assertOk();

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_prevent_unauthorized_user_to_delete_post()
    {
        $postRequest = Post::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();

        $response = $this->actingAs($this->otherUser)->delete(route($this->route.'destroy', $post));
        $response->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }
}
