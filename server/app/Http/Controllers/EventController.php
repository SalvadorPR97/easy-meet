<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $events = Event::all();
        return response()->json(['events' => $events]);
    }
    /**
     * Display a listing of events filtered by city.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByCity(string $city)
    {
        $events = Event::where('city', $city)->get();
        return response()->json(['events' => $events]);
    }

    /**
     * Store a newly created event in the DB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $request->all();
        $data['owner_id'] = auth()->id();
        $event = Event::create($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/events_images/' . $event->id);
            $event->image_url = Storage::url($path);
            $event->save();
        }

        return response()->json(['data' => ['message' => 'Evento creado correctamente', 'event' => $event]]);
    }

    /**
     * Display the specified event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $event = Event::find($id);
        return response()->json(['event' => $event]);
    }
}
