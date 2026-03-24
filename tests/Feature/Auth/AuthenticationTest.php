<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->withSession(['math_captcha_answer' => 8])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
                'math_captcha' => 8,
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_asom_students_are_redirected_to_their_learning_workspace_after_login(): void
    {
        $user = User::factory()->create([
            'user_type' => 'asom_student',
        ]);

        $response = $this
            ->withSession(['math_captcha_answer' => 8])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
                'math_captcha' => 8,
            ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('asom.welcome'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->withSession(['math_captcha_answer' => 8])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
                'math_captcha' => 8,
            ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
