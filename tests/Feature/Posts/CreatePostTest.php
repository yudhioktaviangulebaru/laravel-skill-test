<?php

namespace Tests\Feature\Posts;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePostTest extends TestCase
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
    public function test_allow_authenticated_user_to_view_create_post_form()
    {
        $this->actingAs($this->user)->get(route($this->route.'create'))
            ->assertOk();
    }

    public function test_prevent_unauthenticated_user_to_view_create_post_form()
    {
        $this->get(route($this->route.'create'))
            ->assertRedirect(route('login'));
    }

    public function test_allow_authenticated_user_to_create_post()
    {
        $postRequest = [
            'title' => 'foo',
            'content' => 'bar',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest)
            ->assertCreated();
    }

    public function test_prevent_unauthenticated_user_to_create_post()
    {
        $postRequest = [
            'title' => 'foo',
            'content' => 'bar',
        ];

        $this->post(route($this->route.'store'), $postRequest)
            ->assertRedirect(route('login'));
    }

    public function test_post_with_empty_title_should_fail()
    {
        $postRequest = [
            'content' => 'bar',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest)
            ->assertSessionHasErrors('title');
    }

    public function test_post_with_empty_content_should_fail()
    {
        $postRequest = [
            'title' => 'foo',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $postRequest)
            ->assertSessionHasErrors('content');
    }
}
