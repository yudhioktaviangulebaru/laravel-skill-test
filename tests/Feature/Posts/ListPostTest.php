<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Metadata\Test;
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

    #[Test]
    public function test_can_view_list_posts()
    {
        $request1 = [
            'title' => 'foo1',
            'content' => 'bar1',
        ];
        $request2 = [
            'title' => 'foo2',
            'content' => 'bar2',
        ];

        $this->actingAs($this->user)->post(route($this->route.'store'), $request1);
        $this->actingAs($this->user)->post(route($this->route.'store'), $request2);

        $post = Post::firstWhere(['title' => 'foo1']);
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
        $request = [
            'title' => 'foo',
            'content' => 'bar',
        ];
        $this->actingAs($this->user)->post(route($this->route.'store'), $request);

        $response = $this->get(route($this->route.'index'));
        $response->assertOk();

        $data = $response->json('data');
        $this->assertCount(0, $data);
    }
}
