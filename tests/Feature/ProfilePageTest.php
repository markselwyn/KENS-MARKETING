<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_the_profile_page(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
    }

    public function test_staff_can_view_their_profile_and_account_settings(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_approved' => true,
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
        ]);

        $this->actingAs($staff)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('My Profile')
            ->assertSee('Staff Member')
            ->assertSee('staff@example.com')
            ->assertSee('Security Settings');
    }

    public function test_admin_can_view_the_profile_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
            'name' => 'System Admin',
        ]);

        $this->actingAs($admin)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('System Admin')
            ->assertSee('admin account');
    }

    public function test_user_can_update_profile_and_store_an_encrypted_private_photo_path(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff', 'is_approved' => true]);

        $this->actingAs($staff)
            ->patch(route('profile.update'), [
                'name' => 'Updated Staff',
                'email' => 'updated@example.com',
                'profile_photo' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $staff->refresh();
        $this->assertSame('Updated Staff', $staff->name);
        $this->assertSame('updated@example.com', $staff->email);
        Storage::disk('local')->assertExists($staff->profile_photo_path);

        $rawPhotoPath = DB::table('users')->where('id', $staff->id)->value('profile_photo_path');
        $this->assertNotSame($staff->profile_photo_path, $rawPhotoPath);

        $this->actingAs($staff)->get(route('profile.photo'))->assertOk();
    }

    public function test_password_update_requires_current_password_and_is_hashed(): void
    {
        $staff = User::factory()->create(['role' => 'staff', 'is_approved' => true]);

        $this->actingAs($staff)
            ->patch(route('profile.update'), [
                'name' => $staff->name,
                'email' => $staff->email,
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($staff)
            ->patch(route('profile.update'), [
                'name' => $staff->name,
                'email' => $staff->email,
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password-123', $staff->fresh()->password));
    }
}
