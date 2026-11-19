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
            'link' => 'nullable|url|max:512',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:64',
            'size' => 'required|numeric|min:0',
            'postal' => 'required|string|max:16',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|max:24',
        ]);

        $request->user()->properties()->create($validated);

        return redirect()->route('home')->with('success', 'Property Listed');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        //
    }

}
