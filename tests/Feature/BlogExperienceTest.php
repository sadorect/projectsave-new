<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_filters_by_category_and_tag(): void
    {
        $author = User::factory()->create();
        $faith = Category::create(['name' => 'Faith', 'slug' => 'faith']);
        $prayer = Category::create(['name' => 'Prayer', 'slug' => 'prayer']);
        $featured = Tag::create(['name' => 'Featured', 'slug' => 'featured']);
        $mission = Tag::create(['name' => 'Mission', 'slug' => 'mission']);

        $faithPost = $this->createPublishedPost($author, 'Growing in Faith');
        $faithPost->categories()->sync([$faith->id]);
        $faithPost->tags()->sync([$featured->id]);

        $prayerPost = $this->createPublishedPost($author, 'A Prayerful Life', now()->subDay());
        $prayerPost->categories()->sync([$prayer->id]);
        $prayerPost->tags()->sync([$mission->id]);

        $this->get(route('blog.index', ['category' => 'faith']))
            ->assertOk()
            ->assertSeeText('Growing in Faith')
            ->assertDontSeeText('A Prayerful Life');

        $this->get(route('blog.index', ['tag' => 'mission']))
            ->assertOk()
            ->assertSeeText('A Prayerful Life')
            ->assertDontSeeText('Growing in Faith');
    }

    public function test_show_tracks_post_views_and_site_visits_once_per_session_day(): void
    {
        $author = User::factory()->create();
        $post = $this->createPublishedPost($author, 'Tracked Devotional');

        $this->get(route('posts.show', $post->slug))
            ->assertOk()
            ->assertSeeText('1 views')
            ->assertSeeText('1 site visits')
            ->assertSeeText('1 article views');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'view_count' => 1,
        ]);

        $this->assertDatabaseHas('site_visit_stats', [
            'visit_date' => now()->toDateString(),
            'visits' => 1,
        ]);

        $this->get(route('posts.show', $post->slug))
            ->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'view_count' => 1,
        ]);

        $this->assertDatabaseHas('site_visit_stats', [
            'visit_date' => now()->toDateString(),
            'visits' => 1,
        ]);
    }

    public function test_calendar_endpoint_returns_post_links_for_requested_month(): void
    {
        $author = User::factory()->create();
        $post = $this->createPublishedPost($author, 'Calendar Post', now()->setDate(2026, 3, 10));

        $response = $this->getJson(route('blog.calendar', [
            'year' => 2026,
            'month' => 3,
        ]));

        $response->assertOk()
            ->assertJsonPath('month', 3)
            ->assertJsonPath('year', 2026)
            ->assertJsonPath('postCalendarDays.2026-03-10.count', 1)
            ->assertJsonPath('postCalendarDays.2026-03-10.url', route('posts.show', $post->slug));
    }

    private function createPublishedPost(User $author, string $title, $publishedAt = null): Post
    {
        return Post::create([
            'title' => $title,
            'scripture' => 'John 3:16',
            'subtitle' => 'Subtitle',
            'details' => '<p>Body copy</p>',
            'action_point' => '<p>Act on the word.</p>',
            'author' => $author->name,
            'user_id' => $author->id,
            'status' => 'published',
            'published_at' => $publishedAt ?? now(),
        ]);
    }
}
