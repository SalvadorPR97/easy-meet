<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    /**
     * @dataProvider login_provider
     */
    public function test_login($request, $httpCode, $JsonStructure)
    {
        $response = $this->json('POST', '/api/login', [
            'email' => $request['email'],
            'password' => $request['password'],
        ]);

        $response->assertStatus($httpCode);
        $response->assertJsonStructure($JsonStructure);
    }

    public function login_provider()
    {
        return [
            "ok" => [
                ['email' => 's.perezranchal@gmail.com', 'password' => 'No12345.'], 200, ['data']],
            "ko" => [
                ['email' => 's.perezranchal@gmail.com', 'password' => 'test.'], 422, ['message']]
        ];
    }

    /**
     * @dataProvider register_provider
     */
    public function test_register($request, $httpCode, $JsonStructure)
    {
        $response = $this->json('POST', '/api/register', [
            'name' => $request['name'],
            'surname' => $request['surname'],
            'username' => $request['username'],
            'email' => $request['email'],
            'age' => $request['age'],
            'password' => $request['password'],
        ]);

        $response->assertStatus($httpCode);
        $response->assertJsonStructure($JsonStructure);
    }

    public function register_provider()
    {
        return [
            "ok" => [
                ['name' => 'Mariano', 'surname' => 'Delgado', 'username' => 'MetrosexualPensador',
                    'email' => 'ignorantedelavida@gmail.com', 'age' => '68', 'password' => 'No12345.'],
                201, ['data' => ['user']]],
            "ko" => [
                ['name' => 'Mariano', 'surname' => 'Delgado', 'username' => 'MetrosexualPensador',
                    'email' => 'ignorantedelavida@gmail.com', 'age' => '68', 'password' => 'No12345.'],
                422, ['message']]
        ];
    }

    private function login()
    {
        $responseLogin = $this->json('POST', '/api/login', [
            'email' => 's.perezranchal@gmail.com',
            'password' => 'No12345.'
        ]);
        return $responseLogin['data']['accessToken'];
    }

    private function logout($token)
    {
        $this->withHeaders(["Authorization" => "Bearer $token"])->json('GET', '/api/logout');
    }

}
