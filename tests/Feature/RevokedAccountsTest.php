<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RevokedAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_hub_separates_pending_revoked_and_active_staff(): void
    {
        $admin = $this->admin();
        $pending = $this->staff(['is_approved' => false, 'revoked_at' => null]);
        $revoked = $this->staff(['is_approved' => false, 'revoked_at' => now()]);
        $active = $this->staff(['is_approved' => true, 'revoked_at' => null]);

        $response = $this->actingAs($admin)->get(route('admin.security'));

        $response->assertOk()
            ->assertViewHas('pendingUsers', fn ($users) => $users->modelKeys() === [$pending->id])
            ->assertViewHas('revokedUsers', fn ($users) => $users->modelKeys() === [$revoked->id])
            ->assertViewHas('approvedStaff', fn ($users) => $users->modelKeys() === [$active->id]);
    }

    public function test_admin_can_revoke_and_restore_active_staff(): void
    {
        $admin = $this->admin();
        $staff = $this->staff(['is_approved' => true]);

        $this->actingAs($admin)
            ->post(route('admin.revoke', $staff))
            ->assertRedirect()
            ->assertSessionHas('success');

        $staff->refresh();
        $this->assertFalse($staff->is_approved);
        $this->assertNotNull($staff->revoked_at);

        $this->actingAs($admin)
            ->post(route('admin.restore', $staff))
            ->assertRedirect()
            ->assertSessionHas('success');

        $staff->refresh();
        $this->assertTrue($staff->is_approved);
        $this->assertNull($staff->revoked_at);
    }

    public function test_only_revoked_staff_can_be_restored_or_deleted(): void
    {
        $admin = $this->admin();
        $pending = $this->staff(['is_approved' => false, 'revoked_at' => null]);
        $active = $this->staff(['is_approved' => true, 'revoked_at' => null]);
        $revoked = $this->staff(['is_approved' => false, 'revoked_at' => now()]);

        $this->actingAs($admin)->post(route('admin.restore', $pending))->assertNotFound();
        $this->actingAs($admin)->delete(route('admin.revoked.delete', $active))->assertNotFound();
        $this->actingAs($admin)->delete(route('admin.revoked.delete', $admin))->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('admin.revoked.delete', $revoked))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $revoked->id]);
    }

    public function test_pending_and_revoked_staff_receive_distinct_login_messages(): void
    {
        $pending = $this->staff(['email' => 'pending@example.com', 'is_approved' => false, 'revoked_at' => null]);
        $revoked = $this->staff(['email' => 'revoked@example.com', 'is_approved' => false, 'revoked_at' => now()]);

        $this->post(route('login.post'), ['email' => $pending->email, 'password' => 'password'])
            ->assertSessionHas('success', 'Your account is still pending. Please wait for Admin confirmation to access the system.');

        $this->post(route('login.post'), ['email' => $revoked->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Your system access has been revoked by the Administrator.']);
    }

    private function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'is_approved' => true,
        ], $attributes));
    }

    private function staff(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'staff',
            'is_approved' => false,
            'revoked_at' => null,
        ], $attributes));
    }
}
