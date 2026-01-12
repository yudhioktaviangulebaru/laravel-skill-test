<?php

namespace Tests\Feature\Posts;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    private string $route = 'posts.';

    private User $user;

    /** @test */
    protected function set_up(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_render_post_create_page()
    {
        $response = $this->actingAs($this->user)->get(route($this->route.'create'));
        $response->assertStatus(200);
    }

    public function test_allow_authenticated_user_to_create_post()
    {
        $response = $this->actingAs($this->user)->post(route($this->route.'store'), [
            'title' => 'foo',
            'content' => 'bar',
        ]);
        $response->assertRedirect(route($this->route.'index'));
        $this->assertDatabaseCount('posts', 1);
    }

    public function test_prevent_unauthenticated_user_to_create_post()
    {
        $response = $this->post(route($this->route.'store'), [
            'title' => 'foo',
            'content' => 'Bar',
        ]);
        $response->assertRedirect(route('login', absolute: false));
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_post_should_have_title()
    {
        $response = $this->actingAs($this->user)->post(route($this->route.'store'), [
            'title' => '',
            'content' => 'bar',
        ]);
        $response->assertRedirect(url('/'));
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_post_must_have_content()
    {
        $user = User::factory()->create();
        Auth::setUser($user);
        $response = $this->post(route($this->route.'store'), [
            'title' => 'foo',
            'content' => '',
        ]);
        $response->assertRedirect(url('/'));
        $this->assertDatabaseCount('posts', 0);
    }
}
