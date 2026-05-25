<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[WithoutErrorHandler]
    public function test_comprador_can_register_from_the_public_form(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Cliente Nuevo',
            'email' => 'cliente.nuevo@example.com',
            'phone' => '9617017722',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/entrar');

        $this->assertDatabaseHas('users', [
            'name' => 'Cliente Nuevo',
            'email' => 'cliente.nuevo@example.com',
            'phone' => '9617017722',
            'role' => User::ROLE_COMPRADOR,
        ]);
    }
}
