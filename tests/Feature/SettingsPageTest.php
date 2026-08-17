<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_or_update_settings(): void
    {
        $this->get(route('settings'))->assertRedirect(route('login'));
        $this->patch(route('settings.update'))->assertRedirect(route('login'));
        $this->patchJson(route('settings.sidebar.update'), ['sidebar_state' => 'collapsed'])->assertUnauthorized();
    }

    public function test_landing_page_options_are_limited_by_role(): void
    {
        $staff = $this->user('staff');
        $this->actingAs($staff)
            ->get(route('settings'))
            ->assertOk()
            ->assertDontSee('value="admin.security"', false);

        $admin = $this->user('admin');
        $this->actingAs($admin)
            ->get(route('settings'))
            ->assertOk()
            ->assertSee('value="admin.security"', false);
    }

    public function test_preferences_are_validated_and_encrypted_in_the_database(): void
    {
        $staff = $this->user('staff');
        $preferences = [
            'theme' => 'dark',
            'landing_page' => 'sales.index',
            'sidebar_state' => 'collapsed',
            'reduced_motion' => true,
        ];

        $this->actingAs($staff)
            ->patch(route('settings.update'), $preferences)
            ->assertRedirect()
            ->assertSessionHas('success');

        $staff->refresh();
        $this->assertSame($preferences, $staff->preferences);
        $rawPreferences = DB::table('users')->where('id', $staff->id)->value('preferences');
        $this->assertNotSame(json_encode($preferences), $rawPreferences);
        $this->assertStringNotContainsString('sales.index', $rawPreferences);

        $this->actingAs($staff)
            ->patch(route('settings.update'), array_merge($preferences, ['landing_page' => 'admin.security']))
            ->assertSessionHasErrors('landing_page');
    }

    public function test_sidebar_endpoint_updates_only_sidebar_preference(): void
    {
        $staff = $this->user('staff', ['preferences' => [
            'theme' => 'light',
            'landing_page' => 'inventory.index',
            'sidebar_state' => 'expanded',
            'reduced_motion' => false,
        ]]);

        $this->actingAs($staff)
            ->patchJson(route('settings.sidebar.update'), ['sidebar_state' => 'collapsed'])
            ->assertOk()
            ->assertJson(['sidebar_state' => 'collapsed']);

        $preferences = $staff->fresh()->preferences;
        $this->assertSame('collapsed', $preferences['sidebar_state']);
        $this->assertSame('light', $preferences['theme']);
    }

    public function test_saved_preferences_control_layout_classes_and_login_redirect(): void
    {
        $staff = $this->user('staff', ['preferences' => [
            'theme' => 'dark',
            'landing_page' => 'reports',
            'sidebar_state' => 'collapsed',
            'reduced_motion' => true,
        ]]);

        $this->actingAs($staff)
            ->get(route('settings'))
            ->assertOk()
            ->assertSee('data-theme-preference="dark"', false)
            ->assertSee('reduce-motion', false)
            ->assertSee('id="sidebar" class="bg-navy-900 text-white w-20', false);

        auth()->logout();

        $this->post(route('login.post'), ['email' => $staff->email, 'password' => 'password'])
            ->assertRedirect(route('reports'));
    }

    public function test_reduced_motion_strictly_disables_animations_and_transitions(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('animation: none !important;', $css);
        $this->assertStringContainsString('transition: none !important;', $css);
        $this->assertStringContainsString('scroll-behavior: auto !important;', $css);
        $this->assertStringContainsString('.reduce-motion .animate-fade-in', $css);
        $this->assertStringContainsString('opacity: 1 !important;', $css);
        $this->assertStringNotContainsString('animation-duration: 0.001ms', $css);
        $this->assertStringNotContainsString('transition-duration: 0.001ms', $css);
    }

    public function test_dark_mode_defines_consistent_surfaces_and_hover_states(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('.dark .hover\\:bg-gray-50:hover', $css);
        $this->assertStringContainsString('.dark .hover\\:bg-blue-50:hover', $css);
        $this->assertStringContainsString('.dark .hover\\:bg-green-50:hover', $css);
        $this->assertStringContainsString('.dark .hover\\:bg-red-50:hover', $css);
        $this->assertStringContainsString('.dark .hover\\:bg-orange-50:hover', $css);
        $this->assertStringContainsString('.dark .peer:checked ~ .peer-checked\\:bg-blue-50', $css);
        $this->assertStringContainsString('color-scheme: dark;', $css);
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'is_approved' => true,
        ], $attributes));
    }
}
