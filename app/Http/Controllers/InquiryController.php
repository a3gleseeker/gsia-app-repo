<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Inquiry;



class InquiryController extends Controller
{
    public function store(Request $req, Property $property){

        $val = $req->validate([
            'name' => ['required', 'string', 'max: 64'],
            'email' => ['nullable', 'string', 'max: 64'],
            'phone' => ['required', 'string', 'max: 25'],
            'message' => ['nullable', 'string'],
        ]);

        Inquiry::create([
            'property_id' => $property->id,
            'property_name' => $property->name,
            'name' =>  $val['name'],
            'email' =>  $val['email'] ?? null,
            'phone' => $val['phone'],
            'message' => $val['message'] ?? null,
            'status' => 'pending',
        ]);

        
        return redirect()
            ->route('properties.show', $property)
            ->with('success', 'successfully sent, please wait for a response');
        
    }
}
