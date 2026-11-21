<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
    public function destroy(Request $request, Property $property, PropertyImage $image)
    {
        abort_unless($request->user()->level >= 1, 403);
        abort_unless($image->property_id === $property->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }
}