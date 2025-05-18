<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        Log::info('Listando todos los eventos...');
        $events = Event::all();
        Log::info('Eventos listados correctamente. Total: ' . $events->count());
        return response()->json(['events' => $events]);
    }

    public function indexByCity(string $city)
    {
        Log::info('Buscando eventos por ciudad: ' . $city);
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i');

        $events = Event::where('city', $city)
            ->where(function ($query) use ($today, $nowTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function ($q) use ($today, $nowTime) {
                        $q->where('date', $today)
                            ->where('start_time', '>=', $nowTime);
                    });
            })->get();

        if ($events->isEmpty()) {
            Log::warning('No hay eventos disponibles para la ciudad: ' . $city);
            return response()->json(['message' => "No hay eventos disponibles para esta ciudad"]);
        }

        Log::info('Eventos encontrados para la ciudad: ' . $city . '. Total: ' . $events->count());
        return response()->json(['events' => $events]);
    }

    public function filteredEvents(Request $request)
    {
        Log::info('Filtrando eventos por parámetros: ' . json_encode($request->all()));
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i');

        $query = Event::query()
            ->where(function ($query) use ($today, $nowTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function ($q) use ($today, $nowTime) {
                        $q->where('date', $today)
                            ->where('start_time', '>=', $nowTime);
                    });
            })
            ->when($request->city, fn($q, $v) => $q->where('city', $v))
            ->when($request->category_id, fn($q, $v) => $q->where('category_id', $v))
            ->when($request->subcategory_id, fn($q, $v) => $q->where('subcategory_id', $v));

        $events = $query->get();

        Log::info('Eventos filtrados encontrados: ' . $events->count());
        return response()->json(['events' => $events]);
    }

    public function store(Request $request)
    {
        Log::info('Creando nuevo evento...');

        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'date' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i',
                'min_participants' => 'required|integer|min:2',
                'max_participants' => 'required|integer|gte:min_participants',
                'price' => 'required',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
            ]);
            if ($validator->fails()) {
                Log::warning('Evento no creado. Error de validación.');
                return response()->json(['message' => 'Datos erróneos', 'error' => $validator->errors()], 422);
            }

            $data = $request->all();
            $data['only_women'] = $request->boolean('only_women') ? 1 : 0;
            $data['only_men'] = $request->boolean('only_men') ? 1 : 0;
            $data['owner_id'] = auth()->id();

            $event = Event::create($data);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/events_images/' . $event->id);
                $event->image_url = Storage::url($path);
                $event->save();
            }

            (new EventsUsersController)->joinEvent($event->id);

            Log::info('Evento creado correctamente. ID: ' . $event->id);
            return response()->json(['data' => ['message' => 'Evento creado correctamente', 'event' => $event]], 201);
        } catch (\Exception $e) {
            Log::error('Evento no creado. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function show($id)
    {
        Log::info('Mostrando detalles del evento con ID: ' . $id);
        $event = Event::find($id);
        if (!$event) {
            Log::warning('Evento no encontrado. ID: ' . $id);
        } else {
            Log::info('Evento encontrado: ' . $event->title);
        }
        return response()->json(['event' => $event]);
    }

    public function delete(int $id)
    {
        $userId = auth()->id();
        Log::info("Intentando eliminar el evento con ID: $id por el usuario $userId");

        $event = Event::findOrFail($id);
        if ($event->owner_id == $userId || $event->owner_id == 1) {
            $event->users()->detach();
            $event->delete();
            Log::info("Evento ID $id eliminado correctamente");
            return response()->json(['message' => "Evento borrado correctamente"]);
        }

        Log::warning("Usuario no autorizado a eliminar el evento con ID: $id");
        return response()->json(['message' => 'Solo el usuario que lo crea o un administrador puede eliminar este evento'], 401);
    }

    public function cities()
    {
        Log::info('Obteniendo ciudades con eventos disponibles...');
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i');

        $events = Event::where(function ($query) use ($today, $nowTime) {
            $query->where('date', '>', $today)
                ->orWhere(function ($q) use ($today, $nowTime) {
                    $q->where('date', $today)
                        ->where('start_time', '>=', $nowTime);
                });
        })->get();

        $cities = $events->pluck('city')->countBy()->map(function ($count, $city) {
            return ['name' => $city, 'count' => $count];
        })->values();

        Log::info('Ciudades con eventos obtenidas correctamente. Total: ' . $cities->count());
        return response()->json(['cities' => $cities]);
    }

    public function eventsByOwner()
    {
        $userId = auth()->id();
        Log::info("Obteniendo eventos creados por el usuario: $userId");
        $events = Event::where('owner_id', $userId)->get();
        Log::info("Total de eventos encontrados: " . $events->count());
        return response()->json(['events' => $events]);
    }
}

