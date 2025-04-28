<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventsUsersController extends Controller
{
    public function joinEvent(int $eventId){
        $user = auth()->user();
        $event = Event::findOrFail($eventId);

        $result = $user->events()->syncWithoutDetaching([$event->id]);

        if (!empty($result['attached'])) {
            return response()->json(['message' => 'Te has unido al evento correctamente']);
        } else {
            return response()->json(['message' => 'Ya estás unido a este evento'], 409);
        }
    }
    public function leaveEvent(int $eventId){
        $user = auth()->user();
        $event = Event::findOrFail($eventId);

        $user->events()->detach($event);

        return response()->json(['message' => 'Has abandonado el evento correctamente']);
    }
    public function joinedEvents(){
        $user = auth()->user();

        $events = $user->events;

        return response()->json(['events' => $events]);
    }
    public function usersInEvent(int $eventId){
        $user = auth()->user();
        $event = Event::findOrFail($eventId);

        $users = $event->users;

        return response()->json(['users' => $users]);
    }
}
