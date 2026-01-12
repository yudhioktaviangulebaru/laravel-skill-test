<?php

namespace Tests\Feature\Posts;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPostTest extends TestCase
{
    use RefreshDatabase;

    private string $route = 'posts.';

    private User $user;

    /** @test */
    protected function set_up(): void
    {
        $this->user = User::factory()->create();
        parent::setUp();
    }

    public function test_can_render_post_index_page()
    {
        $response = $this->actingAs($this->user)->post(route($this->route.'store'), [
            'title' => 'foo',
            'content' => 'bar',
        ]);

        $response = $this->actingAs($this->user)->get(route($this->route.'index'));
        $response->assertSee('foo');
    }
}
