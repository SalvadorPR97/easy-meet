<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCode;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider registerUserProvider
     */
    public function test_register_user(array $payload, int $expectedStatus)
    {
        Mail::fake();
        Storage::fake('public');

        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus($expectedStatus);

        if ($expectedStatus === 201) {
            $this->assertDatabaseHas('users', ['email' => $payload['email']]);
            Mail::assertSent(VerifyEmail::class);
        } else {
            Mail::assertNothingSent();
        }
    }

    public static function registerUserProvider(): array
    {
        return [
            'registro correcto' => [[
                'name' => 'John',
                'surname' => 'Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'birthdate' => '2000-01-01',
                'city' => 'Madrid',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ], 201],
            'email inválido' => [[
                'name' => 'John',
                'surname' => 'Doe',
                'username' => 'johndoe2',
                'email' => 'not-an-email',
                'birthdate' => '2000-01-01',
                'city' => 'Madrid',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ], 422],
            'faltan campos' => [[
                'email' => 'incompleto@example.com',
            ], 422],
        ];
    }

    /**
     * @dataProvider loginUserProvider
     */
    public function test_login_user(array $credentials, int $expectedStatus)
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus($expectedStatus);

        if ($expectedStatus === 200) {
            $response->assertJsonStructure(['data' => ['accessToken', 'toke_type', 'user']]);
        }
    }

    public static function loginUserProvider(): array
    {
        return [
            'login correcto' => [[
                'email' => 'john@example.com',
                'password' => 'password123',
            ], 200],
            'contraseña incorrecta' => [[
                'email' => 'john@example.com',
                'password' => 'wrongpassword',
            ], 422],
            'email vacío' => [[
                'email' => '',
                'password' => 'password123',
            ], 422],
        ];
    }

    public function test_logout_deletes_tokens()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');
        $response->assertStatus(200)->assertJson(['message' => 'Usuario deslogado']);
    }

    public function test_user_returns_authenticated_user_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');
        $response->assertStatus(200)->assertJsonStructure(['data' => ['user']]);
    }

    public function test_regenerate_code_sends_email_if_user_exists()
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/regenerate-code', ['email' => $user->email]);;
        $response->assertStatus(200);
        Mail::assertSent(PasswordResetCode::class);
    }

    public function test_verify_email_marks_user_as_verified()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'verified' => false,
            'email_verified_at' => null
        ]);

        $response = $this->getJson("/api/verify-email/{$user->email}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verificado correctamente']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'verified' => true
        ]);
    }

    /**
     * @dataProvider regeneratePasswordProvider
     */
    public function test_regenerate_password(array $data, int $expectedStatus, string $email)
    {
        DB::table('password_resets')->delete();
        $user = User::factory()->create(['email' => $email]);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => '123456',
            'created_at' => Carbon::now()
        ]);

        $response = $this->postJson('/api/regenerate-password', $data);
        $response->assertStatus($expectedStatus);

        if ($expectedStatus === 200) {
            $this->assertTrue(Hash::check($data['password'], $user->fresh()->password));
        }
    }

    public static function regeneratePasswordProvider(): array
    {
        return [
            'código correcto y password válido' => [[
                'email' => 'john@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'token' => '123456'
            ], 200, 'john@example.com'],

            'código incorrecto' => [[
                'email' => 'john@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'token' => 'wrongtoken'
            ], 422, 'john@example.com'],

            'password no coincide' => [[
                'email' => 'john@example.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'different',
                'token' => '123456'
            ], 422, 'john@example.com']
        ];
    }
}
