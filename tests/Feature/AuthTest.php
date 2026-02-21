<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_page()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_role_based_redirect_after_login()
    {
        $manager = User::factory()->create(['role' => 'manager', 'password' => bcrypt('password')]);
        $dispatcher = User::factory()->create(['role' => 'dispatcher', 'password' => bcrypt('password')]);
        $safety = User::factory()->create(['role' => 'safety', 'password' => bcrypt('password')]);
        $finance = User::factory()->create(['role' => 'finance', 'password' => bcrypt('password')]);

        // Test manager redirect
        $response = $this->post('/login', [
            'email' => $manager->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');

        // Test dispatcher redirect
        $this->post('/logout');
        $response = $this->post('/login', [
            'email' => $dispatcher->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/trips');

        // Test safety redirect
        $this->post('/logout');
        $response = $this->post('/login', [
            'email' => $safety->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/drivers');

        // Test finance redirect
        $this->post('/logout');
        $response = $this->post('/login', [
            'email' => $finance->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/analytics');
    }
}
