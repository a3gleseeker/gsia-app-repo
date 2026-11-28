<div>
    @forelse ($property->documents as $doc)
        <div style="padding:.6rem 0; border-bottom:1px solid rgba(128,128,128,.25);">
            <div style="font-weight:600;">{{ $doc->name }}</div>
            <div style="font-size:.8rem; opacity:.7; margin-bottom:.35rem;">
                {{ $doc->inventory_code }}
                · {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                · {{ $doc->file_size }} MB
                · {{ $doc->created_at->format('M d, Y') }}
            </div>
            <div style="display:flex; gap:1rem;">
                <x-filament::link :href="route('documents.view', $doc)" target="_blank" icon="heroicon-o-eye">View</x-filament::link>
                <x-filament::link :href="route('documents.download', $doc)" icon="heroicon-o-arrow-down-tray">Download</x-filament::link>
                <x-filament::link :href="route('documents.edit', $doc)" icon="heroicon-o-pencil-square">Edit</x-filament::link>
            </div>
        </div>
    @empty
        <p style="opacity:.7;">No documents linked to this property yet.</p>
    @endforelse

    <div style="margin-top:1rem;">
        <x-filament::button tag="a"
            :href="route('documents.create', ['property' => $property->id])"
            icon="heroicon-o-plus">
            Add document
        </x-filament::button>
    </div>
</div>