<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $searching = $q !== '' || filled($type);

        $data = [
            'q'         => $q,
            'type'      => $type,
            'searching' => $searching,
            'types'     => Property::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type'),
            'results'   => null,
            'newProperties'    => collect(),
            'randomProperties' => collect(),
        ];

        if ($searching) {
            $data['results'] = Property::with('images')
                ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('postal', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")))
                ->when(filled($type), fn ($query) => $query->where('type', $type))
                ->latest()
                ->paginate(9)
                ->withQueryString();  

            return view('index', $data);
        }

        $data['newProperties'] = Property::with('images')
            ->where('is_sold', false)->latest()->take(6)->get();

        $data['randomProperties'] = Property::with('images')
            ->where('is_sold', false)
            ->whereNotIn('id', $data['newProperties']->pluck('id'))
            ->inRandomOrder()->take(6)->get();

        return view('index', $data);
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
            'latitude'    => ['nullable', 'numeric','between:-90,90', 'required_with:longitude'],
            'longitude'   => ['nullable', 'numeric','between:-180,180', 'required_with:latitude'],
            
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

        return view('showproperty', ['property' => $property, 'view_count' => $property->views()->count()]);
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

        unset($validated['images']);
        $property->update($validated);

        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'Property updated.');
    }

    public function destroy(Property $property)
    {
        $paths = $property->images->pluck('path');

        $property->images()->delete();
        $property->delete();

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }   

        return redirect()->route('home')->with('success', 'Listing deleted.');
    }

}
