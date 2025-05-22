<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function it_lists_all_events_authenticated()
    {
        Sanctum::actingAs(User::factory()->create());
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/events/all');
        $response->assertStatus(200)->assertJsonStructure(['events']);
    }

    public function it_filters_events_by_city()
    {
        Event::factory()->create([
            'city' => 'Madrid',
            'date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/events/city/Madrid');
        $response->assertStatus(200)->assertJsonStructure(['events']);
    }

    public function it_returns_message_if_no_events_in_city()
    {
        $response = $this->getJson('/api/events/city/Nowhere');
        $response->assertStatus(200)->assertJson(['message' => 'No hay eventos disponibles para esta ciudad']);
    }

    /** @dataProvider eventStoreProvider */
    public function test_store_event_requires_authentication(array $payload, int $expectedStatus)
    {
        Storage::fake('public');
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $subcategory = Subcategory::factory()->create(['category_id' => $category->id]);

        $payload['category_id'] = $category->id;
        $payload['subcategory_id'] = $subcategory->id;

        $response = $this->postJson('/api/events/store', $payload);
        $response->assertStatus($expectedStatus);

        if ($expectedStatus === 201) {
            $this->assertDatabaseHas('events', ['title' => $payload['title']]);
        }
    }

    public static function eventStoreProvider(): array
    {
        $base = [
            'title' => 'Evento Test',
            'location' => 'Calle Falsa 123',
            'city' => 'Madrid',
            'latitude' => '40.416775',
            'longitude' => '-3.703790',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '18:00',
            'image' => null,
            'min_participants' => 2,
            'max_participants' => 5,
            'price' => '0',
            'description' => 'Descripción del evento',
        ];

        return [
            'correcto' => [$base, 201],
            'falta título' => [array_diff_key($base, ['title' => '']), 422],
            'fecha inválida' => [array_merge($base, ['date' => 'fecha']), 422],
        ];
    }

    public function it_returns_filtered_events()
    {
        Event::factory()->create(['city' => 'Valencia', 'date' => now()->addDay()->format('Y-m-d')]);

        $response = $this->getJson('/api/events/filter?city=Valencia');
        $response->assertStatus(200)->assertJsonStructure(['events']);
    }

    public function it_returns_events_by_authenticated_owner()
    {
        $user = User::factory()->create();
        Event::factory()->count(2)->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/events/userEvents');
        $response->assertStatus(200)->assertJsonStructure(['events']);
    }

    public function it_returns_cities_with_upcoming_events()
    {
        Event::factory()->create([
            'city' => 'Málaga',
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '20:00'
        ]);

        $response = $this->getJson('/api/events/cities');
        $response->assertStatus(200)->assertJsonStructure(['cities']);
    }

    public function it_deletes_event_if_user_is_owner()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['owner_id' => $user->id]);

        Sanctum::actingAs($user);
        $response = $this->deleteJson("/api/events/delete/{$event->id}");

        $response->assertStatus(200)->assertJson(['message' => "Evento borrado correctamente"]);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function it_denies_event_deletion_to_non_owner()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $event = Event::factory()->create(['owner_id' => $user2->id]);

        Sanctum::actingAs($user);
        $response = $this->deleteJson("/api/events/delete/{$event->id}");

        $response->assertStatus(401)->assertJson(['message' => 'Solo el usuario que lo crea o un administrador puede eliminar este evento']);
    }
}
