<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiEventTest extends TestCase
{
    /**
     * @dataProvider indexByCity_provider
     */
    public function test_indexByCity($request, $httpCode, $JsonStructure)
    {
        $response = $this->json('GET', '/api/events/' . $request['city']);

        $response->assertStatus($httpCode);
        $response->assertJsonStructure($JsonStructure);
    }

    public function indexByCity_provider()
    {
        return [
            "ok" => [
                ['city' => 'Marbella'], 200, ['events']],
            "ko" => [
                ['city' => 'Mijas'], 200, ['message']]
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

    /**
     * @dataProvider eventStore_provider
     */
    public function test_eventStore($request, $httpCode, $JsonStructure)
    {
        $token = $this->login();
        $response = $this->json('POST', '/api/events/store', [
            'title' => $request['title'],
            'location' => $request['location'],
            'city' => $request['city'],
            'date' => $request['date'],
            'start_time' => $request['start_time'],
            'end_time' => $request['end_time'],
            'description' => $request['description'],
            'category_id' => $request['category_id'],
            'subcategory_id' => $request['category_id']
        ], ['Authorization' => "Bearer $token"]);

        $response->assertStatus($httpCode);
        $response->assertJsonStructure($JsonStructure);
    }

    public function eventStore_provider()
    {
        return [
            "ok" => [
                ['title' => 'Concierto de rock', 'location' => 'lugar del concierto', 'city' => 'Marbella',
                    'date' => '2025-08-04', 'start_time' => '12:00', 'end_time' => '15:00',
                    'description' => 'un concierto', 'category_id' => '1', 'subcategory_id' => '1'], 201, ['data' => ['message', 'event']]],
            "ko" => [
                ['title' => '', 'location' => 'lugar del concierto', 'city' => 'Marbella',
                    'date' => '2025-08-04', 'start_time' => '12:00', 'end_time' => '15:00',
                    'description' => 'un concierto', 'category_id' => '1', 'subcategory_id' => '1'], 422, ['message', 'error']],
        ];
    }
}
