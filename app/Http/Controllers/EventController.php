<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $events = Event::all();
        return response()->json(['events' => $events]);
    }

    /**
     * Display a listing of events filtered by city.
     *
     * @return JsonResponse
     */
    public function indexByCity(string $city)
    {
        $events = Event::where('city', $city)->get();
        if ($events->isEmpty()) {
            return response()->json(['message' => "No hay eventos disponibles para esta ciudad"]);
        }
        return response()->json(['events' => $events]);
    }

    /**
     * Display a listing of events filtered by city.
     *
     * @return JsonResponse
     */
    public function filteredEvents(Request $request)
    {
        $query = Event::query()
            ->when($request->city, fn($q, $v) => $q->where('city', $v))
            ->when($request->category_id, fn($q, $v) => $q->where('category_id', $v))
            ->when($request->subcategory_id, fn($q, $v) => $q->where('subcategory_id', $v));

        $events = $query->get();

        return response()->json(['events' => $events]);
    }

    /**
     * Store a newly created event in the DB.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

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

        return response()->json(['data' => ['message' => 'Evento creado correctamente', 'event' => $event]], 201);
    }

    /**
     * Display the specified event.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $event = Event::find($id);
        return response()->json(['event' => $event]);
    }

    /**
     * Display the specified event.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $userId = auth()->id();

        $event = Event::findOrFail($request['event_id']);
        if ($event->owner_id == $userId || $event->owner_id == 1) {
            $event->delete();
            return response()->json(['event' => $event]);
        }
        return response()->json(['message' => 'Solo el usuario que lo crea o un administrador puede eliminar este evento'], 401);
    }

    public function cities()
    {
        $events = Event::all();
        $cities = $events->pluck('city')->countBy()->map(function ($count, $city) {
            return ['name' => $city, 'count' => $count];
        })->values();
        return response()->json(['cities' => $cities]);
    }
}
