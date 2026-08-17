<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_remember_me_creates_a_persistent_login_token_and_cookie(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_approved' => true,
            'remember_token' => null,
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('inventory.index'))
            ->assertCookie(Auth::guard('web')->getRecallerName());

        $this->assertNotNull($user->fresh()->remember_token);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_without_remember_me_does_not_create_a_remember_token(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_approved' => true,
            'remember_token' => null,
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('inventory.index'));

        $this->assertNull($user->fresh()->remember_token);
        $this->assertAuthenticatedAs($user);
    }
}
