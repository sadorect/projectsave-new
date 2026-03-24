<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withSession(['math_captcha_answer' => 8])
            ->post('/forgot-password', [
                'email' => $user->email,
                'math_captcha' => 8,
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withSession(['math_captcha_answer' => 8])
            ->post('/forgot-password', [
                'email' => $user->email,
                'math_captcha' => 8,
            ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->withSession(['math_captcha_answer' => 8])
            ->post('/forgot-password', [
                'email' => $user->email,
                'math_captcha' => 8,
            ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->withSession(['math_captcha_answer' => 8])
                ->post('/reset-password', [
                    'token' => $notification->token,
                    'email' => $user->email,
                    'password' => 'password',
                    'password_confirmation' => 'password',
                    'math_captcha' => 8,
                ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_reset_password_requests_are_rate_limited(): void
    {
        Notification::fake();

        $request = fn (string $email) => $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.200'])
            ->withSession(['math_captcha_answer' => 8])
            ->post('/forgot-password', [
                'email' => $email,
                'math_captcha' => 8,
            ]);

        foreach (range(1, 6) as $attempt) {
            $request("attempt{$attempt}@example.com");
        }

        $request('blocked@example.com')
            ->assertStatus(429);
    }
}
