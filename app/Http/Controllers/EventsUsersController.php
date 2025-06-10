<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Log;

class EventsUsersController extends Controller
{
    public function joinEvent(int $eventId)
    {
        $user = auth()->user();
        Log::info("Intentando unir al usuario ID {$user->id} al evento ID $eventId");

        try {
            $event = Event::findOrFail($eventId);
            $result = $user->events()->syncWithoutDetaching([$event->id]);

            if (!empty($result['attached'])) {
                Log::info("Usuario ID {$user->id} unido correctamente al evento ID $eventId");
                return response()->json(['message' => 'Te has unido al evento correctamente']);
            } else {
                Log::warning("El usuario ID {$user->id} ya estaba unido al evento ID $eventId");
                return response()->json(['message' => 'Ya estás unido a este evento'], 409);
            }
        } catch (\Exception $e) {
            Log::error("Error al unir usuario al evento. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function leaveEvent(int $eventId)
    {
        $user = auth()->user();
        Log::info("Intentando abandonar el evento ID $eventId por el usuario ID {$user->id}");

        try {
            $event = Event::findOrFail($eventId);
            $user->events()->detach($event);

            Log::info("Usuario ID {$user->id} abandonó el evento ID $eventId correctamente");
            return response()->json(['message' => 'Has abandonado el evento correctamente']);
        } catch (\Exception $e) {
            Log::error("Error al abandonar el evento. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function joinedEvents()
    {
        $user = auth()->user();
        Log::info("Recuperando eventos en los que está inscrito el usuario ID {$user->id}");

        try {
            $events = $user->events;
            Log::info("Eventos recuperados correctamente. Total: " . $events->count());
            return response()->json(['events' => $events]);
        } catch (\Exception $e) {
            Log::error("Error al recuperar eventos unidos. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function usersInEvent(int $eventId)
    {
        Log::info("Obteniendo usuarios del evento ID $eventId");

        try {
            $event = Event::findOrFail($eventId);
            $users = $event->users;

            Log::info("Usuarios del evento ID $eventId obtenidos correctamente. Total: " . $users->count());
            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error("Error al obtener usuarios del evento. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}

