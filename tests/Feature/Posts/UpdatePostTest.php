<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePostTest extends TestCase
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

    /** @test */
    public function test_allow_authorized_user_to_show_edit_form()
    {
        $postRequest = [
            'title' => 'bar',
            'content' => 'baz',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();

        $response = $this->actingAs($this->user)->get(route($this->route.'edit', $post));
        $response->assertOk();
    }

    public function test_prevent_user_to_show_other_user_post_edit_form()
    {
        $postRequest = [
            'title' => 'bar',
            'content' => 'baz',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();

        $response = $this->actingAs($this->otherUser)->get(route($this->route.'edit', $post));
        $response->assertForbidden();
    }

    public function test_allow_authorized_user_to_update_post()
    {
        $postRequest = [
            'title' => 'bar',
            'content' => 'baz',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();
        $postRequest['title'] = 'foo';
        $response = $this->actingAs($this->user)->put(route($this->route.'update', $post), $postRequest);
        $response->assertOk();

        $this->assertDatabaseHas('posts', [
            'title' => 'foo',
        ]);
    }

    public function test_patch_method()
    {
        $postRequest = [
            'title' => 'bar',
            'content' => 'baz',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest);

        $post = Post::first();
        $postRequest['title'] = 'foo';
        $response = $this->actingAs($this->user)->patch(route($this->route.'update', $post), $postRequest);
        $response->assertOk();

        $this->assertDatabaseHas('posts', [
            'title' => 'foo',
        ]);
    }
}
