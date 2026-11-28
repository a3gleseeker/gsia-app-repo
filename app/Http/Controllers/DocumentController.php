<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Documents\DocumentResource; // check the namespace of your generated resource
use App\Models\Client;
use App\Models\Document;
use App\Models\Property;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{   
    # 5120 KB = 5 MB
    # 10240 KB = 10 MB

    private const MAX_FILE_KB = 5120;

    public function create(){
        return view('documents.create', [
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'clients'    => Client::orderBy('name')->get(['id', 'name']),
            'preselectedProperty' => request('property'),
            'preselectedClient'   => request('client'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'inventory_code' => ['required', 'string', 'max:255', 'unique:documents,inventory_code'],
            'document_type'  => ['required', Rule::in(['land_title', 'deed', 'contract', 'other'])],
            'description'    => ['nullable', 'string'],
            'properties'     => ['required_without:clients', 'nullable', 'array'],
            'properties.*'   => ['exists:properties,id'],
            'clients'        => ['required_without:properties', 'nullable', 'array'],
            'clients.*'      => ['exists:clients,id'],
            'file'           => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . self::MAX_FILE_KB],
        ], [
            'inventory_code.unique'       => 'This inventory code is already in use.',
            'properties.required_without' => 'Link the document to at least one property or client.',
            'clients.required_without'    => 'Link the document to at least one property or client.',
            'file.mimes'                  => 'Only PDF, JPG, or PNG files are allowed.',
            'file.max' => 'The file must be ' . (self::MAX_FILE_KB / 1024) . ' MB or smaller.',
        ]);

        $file   = $request->file('file');
        $sizeMb = round($file->getSize() / 1024 / 1024, 2);
        $path   = $file->store('documents', 'local');

        $document = Document::create([
            'name'           => $data['name'],
            'file_location'  => $path,
            'inventory_code' => $data['inventory_code'],
            'document_type'  => $data['document_type'],
            'description'    => $data['description'] ?? null,
            'user_id'        => auth()->id(),
            'file_size'      => $sizeMb,
        ]);

        $document->properties()->sync($data['properties'] ?? []);
        $document->clients()->sync($data['clients'] ?? []);

        if ($request->input('after') === 'another') {
            return redirect()
                ->route('documents.create') 
                ->with('success', "“{$document->name}” saved. Add another below.");
        }

        // Filament page: uses Filament's own notification
        Notification::make()->title('Document saved')->success()->send();

        return redirect(DocumentResource::getUrl('index'));
    }

    public function edit(Document $document){
        $document->load(['properties:id', 'clients:id']);

        return view('documents.edit', [
            'document'   => $document,
            'properties' => Property::orderBy('name')->get(['id', 'name']),
            'clients'    => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'inventory_code' => ['required', 'string', 'max:255', Rule::unique('documents', 'inventory_code')->ignore($document->id)],
            'document_type'  => ['required', Rule::in(['land_title', 'deed', 'contract', 'other'])],
            'description'    => ['nullable', 'string'],
            'properties'     => ['required_without:clients', 'nullable', 'array'],
            'properties.*'   => ['exists:properties,id'],
            'clients'        => ['required_without:properties', 'nullable', 'array'],
            'clients.*'      => ['exists:clients,id'],
            'file'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:' . self::MAX_FILE_KB],
        ], [
            'inventory_code.unique'       => 'This inventory code is already in use.',
            'properties.required_without' => 'Link the document to at least one property or client.',
            'clients.required_without'    => 'Link the document to at least one property or client.',
            'file.mimes'                  => 'Only PDF, JPG, or PNG files are allowed.',
            'file.max'                    => 'The file must be ' . (self::MAX_FILE_KB / 1024) . ' MB or smaller.',
        ]);

        $attributes = [
            'name'           => $data['name'],
            'inventory_code' => $data['inventory_code'],
            'document_type'  => $data['document_type'],
            'description'    => $data['description'] ?? null,
        ];

        // Only touch the file if a new one was uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $attributes['file_location'] = $file->store('documents', 'local');
            $attributes['file_size']     = round($file->getSize() / 1024 / 1024, 2);

            $oldPath = $document->file_location;
        }

        $document->update($attributes);

        // Delete the old file only after the new one is saved
        if (isset($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        $document->properties()->sync($data['properties'] ?? []);
        $document->clients()->sync($data['clients'] ?? []);

        Notification::make()->title('Document updated')->success()->send();

        return redirect(DocumentResource::getUrl('index'));
    }

    
}
