<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Filament\Notifications\Notification;
use App\Filament\Resources\Clients\ClientResource;



class ClientController extends Controller
{
    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:64'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:64'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['photo']); // validate() returns the uploaded file object, not a path

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $data['user_id'] = auth()->id();

        Client::create($data);

        Notification::make()->title('Client added')->success()->send();

        return redirect(ClientResource::getUrl('index'));

    }
}
