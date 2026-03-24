<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['math_captcha_answer' => 8])
            ->post('/confirm-password', [
                'password' => 'password',
                'math_captcha' => 8,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['math_captcha_answer' => 8])
            ->post('/confirm-password', [
                'password' => 'wrong-password',
                'math_captcha' => 8,
            ]);

        $response->assertSessionHasErrors();
    }
}
