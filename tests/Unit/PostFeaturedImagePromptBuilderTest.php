<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\AiImages\PostFeaturedImagePromptBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PostFeaturedImagePromptBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken()->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('details');
            $table->foreignId('user_id');
            $table->string('image')->nullable();
            $table->string('featured_image_candidate_path')->nullable();
            $table->string('featured_image_source')->nullable();
            $table->boolean('featured_image_generation_enabled')->default(false);
            $table->string('featured_image_generation_status')->nullable();
            $table->string('featured_image_approval_status')->nullable();
            $table->string('featured_image_provider')->nullable();
            $table->string('featured_image_preset')->nullable();
            $table->text('featured_image_prompt')->nullable();
            $table->json('featured_image_options')->nullable();
            $table->timestamp('featured_image_generated_at')->nullable();
            $table->text('featured_image_generation_error')->nullable();
            $table->foreignId('featured_image_reviewed_by')->nullable();
            $table->timestamp('featured_image_reviewed_at')->nullable();
            $table->text('featured_image_review_notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('category_id');
            $table->foreignId('post_id');
        });
    }

    public function test_it_builds_prompt_with_preset_and_overrides(): void
    {
        config()->set('ai-images.default_preset', 'devotional-warm');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'prompt@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $post = Post::create([
            'title' => 'Walking In Peace',
            'slug' => 'walking-in-peace',
            'details' => '<p>Trusting God in uncertain times brings calm.</p>',
            'user_id' => $user->getKey(),
            'published_at' => now(),
            'featured_image_preset' => 'scripture-cinematic',
            'featured_image_prompt' => 'Use olive groves and dawn light.',
            'featured_image_options' => ['quality' => 'high'],
        ]);

        $category = Category::create([
            'name' => 'Faith',
            'slug' => 'faith',
        ]);

        $post->categories()->attach($category);

        $result = app(PostFeaturedImagePromptBuilder::class)->build($post->fresh('categories'));

        $this->assertSame('scripture-cinematic', $result['preset']);
        $this->assertStringContainsString('Walking In Peace', $result['prompt']);
        $this->assertStringContainsString('Use olive groves and dawn light.', $result['prompt']);
        $this->assertStringContainsString('Categories: Faith', $result['prompt']);
        $this->assertSame('high', $result['options']['quality']);
    }
}