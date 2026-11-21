<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::all();

        return view('index',['properties' => $properties]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'size'     => ['required', 'numeric', 'min:0'],
            'city'     => ['required', 'string', 'max:255'],
            'postal'   => ['required', 'string', 'max:20'],
            'type'     => ['required', 'string', 'max:100'],
            'price'    => ['required', 'numeric', 'min:0'],
            'images'   => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'images.required' => 'Please add at least one image.',
            'images.*.max'    => 'Each image must be 1 MB or smaller.',
            'images.*.image'  => 'Each file must be an image.',
        ]);

        $files = $validated['images'];
        unset($validated['images']);

        $property = DB::transaction(function () use ($request, $validated, $files) {
            $property = $request->user()->properties()->create($validated);

            foreach ($files as $position => $file) {
                $property->images()->create([
                    'path'     => $file->store('properties', 'public'),
                    'position' => $position,
                ]);
            }

            return $property;
        });

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Property posted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Property $property)
    {   
    
        $key = "viewed_property_{$property->id}";

        if (! $request->session()->has($key)) {
            $visitorId = $request->cookie('visitor_id');

            if (! $visitorId) {
                $visitorId = (string) Str::uuid();
                Cookie::queue('visitor_id', $visitorId, 60 * 24 * 365);
            }

            $agent = new Agent();
            $agent->setUserAgent($request->userAgent() ?? '');

            $position = Location::get($request->ip());

            $property->views()->create([
                'user_id'     => $request->user()?->id,
                'visitor_id'  => $visitorId,
                'ip_address'  => $request->ip(),
                'country'     => $position ? $position->countryName : null,
                'region'      => $position ? $position->regionName : null,
                'city'        => $position ? $position->cityName : null,
                'device_type' => $agent->isTablet() ? 'tablet'
                                : ($agent->isMobile() ? 'mobile' : 'desktop'),
                'platform'    => $agent->platform() ?: 'Unknown',
                'browser'     => $agent->browser() ?: 'Unknown',
            ]);

            $request->session()->put($key, true);
        }

        return view('showproperty', ['property' => $property]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        abort_unless($request->user(),403);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'postal'      => ['required', 'string', 'max:20'],
            'price'       => ['required', 'numeric', 'min:0'],
            'type'        => ['required', 'string', 'max:100'],
            'size'        => ['required', 'numeric', 'min:0'],
            'link'        => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string'],
            'is_sold'     => ['required', 'boolean'],
            'images'      => ['nullable', 'array', 'max:10'],
            'images.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:1024'], // KB = 1 MB
            'latitude'    => ['nullable', 'numeric','between:-90,90', 'required_with:longitude'],
            'longitude'   => ['nullable', 'numeric','between:-180,180', 'required_with:latitude'],
        ], [
        'images.*.max'   => 'Each image must be 1 MB or smaller.',
        'images.*.image' => 'Each file must be an image.',
        ]);


        if ($request->hasFile('images')) {
        $nextPosition = ($property->images()->max('position') ?? -1) + 1;

        foreach ($request->file('images') as $file) {
            $path = $file->store('properties', 'public');

            $property->images()->create([
                'path'     => $path,
                'position' => $nextPosition++,
            ]);
            }
        }

        unset($validated['images']); // not a column on properties
        $property->update($validated);

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Property updated.');
    }

    public function destroy(Property $property)
    {
        foreach ($property->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $property->delete();

        return redirect()->route('home');
    }

}
