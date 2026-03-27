<?php

namespace Tests\Feature;

use App\Contracts\AiImageProvider;
use App\Jobs\GeneratePostFeaturedImage;
use App\Models\Post;
use App\Models\User;
use App\Services\AiImages\AiImageProviderManager;
use App\Services\AiImages\AiImageSettings;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratePostFeaturedImageJobTest extends TestCase
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

    public function test_job_generates_and_saves_post_image(): void
    {
        config()->set('ai-images.enabled', true);
        config()->set('ai-images.require_approval', false);
        config()->set('ai-images.storage_disk', 'public');
        config()->set('ai-images.storage_path', 'posts/generated');
        Storage::fake('public');

        $this->app->bind(AiImageProviderManager::class, function () {
            return new class(app(AiImageSettings::class)) extends AiImageProviderManager {
                public function __construct(AiImageSettings $settings)
                {
                    parent::__construct($settings);
                }

                public function provider(?string $name = null): AiImageProvider
                {
                    return new class implements AiImageProvider {
                        public function generate(array $payload): array
                        {
                            $image = imagecreatetruecolor(32, 32);
                            $background = imagecolorallocate($image, 240, 198, 120);
                            imagefill($image, 0, 0, $background);

                            ob_start();
                            imagepng($image);
                            $content = (string) ob_get_clean();
                            imagedestroy($image);

                            return [
                                'content' => $content,
                                'mime_type' => 'image/png',
                                'revised_prompt' => null,
                                'provider_image_id' => 'img_123',
                                'raw' => [],
                            ];
                        }

                        public function testConnection(): array
                        {
                            return [
                                'ok' => true,
                                'message' => 'Test provider available.',
                                'details' => [],
                            ];
                        }
                    };
                }
            };
        });

        $user = User::create([
            'name' => 'Test User',
            'email' => 'job@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $post = Post::create([
            'title' => 'Morning Mercy',
            'slug' => 'morning-mercy',
            'details' => 'A quiet reflection.',
            'user_id' => $user->getKey(),
            'published_at' => now()->subMinute(),
            'featured_image_generation_enabled' => true,
            'featured_image_generation_status' => 'pending',
            'featured_image_provider' => 'openai',
            'featured_image_preset' => 'devotional-warm',
        ]);

        app(Dispatcher::class)->dispatchSync(new GeneratePostFeaturedImage($post->getKey()));

        $post->refresh();

        $this->assertSame('ai', $post->featured_image_source);
        $this->assertSame('generated', $post->featured_image_generation_status);
        $this->assertSame('approved', $post->featured_image_approval_status);
        $this->assertNotNull($post->image);
        $this->assertTrue(Storage::disk('public')->exists($post->image));
    }

    public function test_job_stores_candidate_when_approval_is_required(): void
    {
        config()->set('ai-images.enabled', true);
        config()->set('ai-images.require_approval', true);
        config()->set('ai-images.storage_disk', 'public');
        config()->set('ai-images.storage_path', 'posts/generated');
        Storage::fake('public');

        $this->app->bind(AiImageProviderManager::class, function () {
            return new class(app(AiImageSettings::class)) extends AiImageProviderManager {
                public function __construct(AiImageSettings $settings)
                {
                    parent::__construct($settings);
                }

                public function provider(?string $name = null): AiImageProvider
                {
                    return new class implements AiImageProvider {
                        public function generate(array $payload): array
                        {
                            $image = imagecreatetruecolor(32, 32);
                            $background = imagecolorallocate($image, 120, 170, 240);
                            imagefill($image, 0, 0, $background);

                            ob_start();
                            imagepng($image);
                            $content = (string) ob_get_clean();
                            imagedestroy($image);

                            return [
                                'content' => $content,
                                'mime_type' => 'image/png',
                                'revised_prompt' => null,
                                'provider_image_id' => 'img_456',
                                'raw' => [],
                            ];
                        }

                        public function testConnection(): array
                        {
                            return [
                                'ok' => true,
                                'message' => 'Test provider available.',
                                'details' => [],
                            ];
                        }
                    };
                }
            };
        });

        $user = User::create([
            'name' => 'Approval User',
            'email' => 'approval@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $post = Post::create([
            'title' => 'Evening Hope',
            'slug' => 'evening-hope',
            'details' => 'A quiet reflection.',
            'user_id' => $user->getKey(),
            'published_at' => now()->subMinute(),
            'featured_image_generation_enabled' => true,
            'featured_image_generation_status' => 'pending',
            'featured_image_provider' => 'openai',
            'featured_image_preset' => 'devotional-warm',
        ]);

        app(Dispatcher::class)->dispatchSync(new GeneratePostFeaturedImage($post->getKey()));

        $post->refresh();

        $this->assertNull($post->image);
        $this->assertNotNull($post->featured_image_candidate_path);
        $this->assertSame('generated', $post->featured_image_generation_status);
        $this->assertSame('pending', $post->featured_image_approval_status);
        $this->assertTrue(Storage::disk('public')->exists($post->featured_image_candidate_path));
    }
}