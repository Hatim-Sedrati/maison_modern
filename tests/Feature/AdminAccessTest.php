<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_visitors_cannot_access_filament_admin(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');

        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_customer_registration_and_login_routes_do_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/login')->assertNotFound();
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('register'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('login'));
    }

    public function test_authenticated_admin_can_open_the_filament_dashboard(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_admin_can_log_out(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('filament.admin.auth.logout'))
            ->assertRedirect();

        $this->assertGuest();

        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }
}
