<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PostController::class)]
class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_a_published_post_body_as_markdown(): void
    {
        $post = Post::factory()->create(['body' => "## Use AI, stay human\n\nA **short** note."]);

        $response = $this->get($post->url());

        $response->assertOk()->assertSee('<h2>Use AI, stay human</h2>', false)->assertSee('<strong>short</strong>', false);
    }

    #[Test]
    public function it_hides_a_draft_post_from_visitors(): void
    {
        $post = Post::factory()->draft()->create();

        $response = $this->get($post->url());

        $response->assertNotFound();
    }

    #[Test]
    public function it_hides_a_scheduled_post_from_visitors(): void
    {
        $post = Post::factory()->scheduled()->create();

        $response = $this->get($post->url());

        $response->assertNotFound();
    }

    #[Test]
    public function it_shows_a_draft_post_to_a_signed_in_user(): void
    {
        $post = Post::factory()->draft()->create(['title' => 'Work in progress']);

        $response = $this->actingAs(User::factory()->create())->get($post->url());

        $response->assertOk()->assertSee('Work in progress');
    }
}
