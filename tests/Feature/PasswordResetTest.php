<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_form_is_available(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertViewIs('forgot-password');
    }

    public function test_reset_link_is_sent_to_an_existing_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status', __(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_staff_is_redirected_to_staff_login_after_reset(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('staff.login'))
            ->assertSessionHas('success', 'Your password has been updated. You can now sign in.');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_admin_is_redirected_to_admin_login_after_reset(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('admin.login'))
            ->assertSessionHas('success', 'Your password has been updated. You can now sign in.');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_role_specific_login_pages_are_labeled(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Admin Portal');
        $this->get(route('staff.login'))->assertOk()->assertSee('Staff Portal');
    }

    public function test_reset_form_explains_password_requirements(): void
    {
        $this->get('/reset-password/example-token?email=user@example.com')
            ->assertOk()
            ->assertSee('At least 8 characters')
            ->assertSee('Both password fields match')
            ->assertSee('Show new password');

        $this->from('/reset-password/example-token?email=user@example.com')
            ->post(route('password.update'), [
                'token' => 'example-token',
                'email' => 'user@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect('/reset-password/example-token?email=user@example.com')
            ->assertSessionHasErrors([
                'password' => 'Your new password must contain at least 8 characters.',
            ]);
    }

    public function test_expired_or_used_token_has_an_actionable_message(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => 'expired-or-used-token',
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHasErrors([
            'email' => 'This password reset link has expired or has already been used. Request a new link.',
            ]);
    }

    public function test_current_password_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
        ]);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'current-password',
            'password_confirmation' => 'current-password',
        ])->assertSessionHasErrors([
            'password' => 'Your new password must be different from your current password.',
        ]);

        $this->assertTrue(Hash::check('current-password', $user->fresh()->password));
    }
}
