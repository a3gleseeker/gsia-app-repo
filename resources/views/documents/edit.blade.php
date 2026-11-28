<x-layout>
    <x-slot:title>Edit Document</x-slot:title>

    @php
        // Selected ids: validation errors (old input) win over saved values
        $selectedProperties = old('properties', $document->properties->pluck('id')->all());
        $selectedClients    = old('clients', $document->clients->pluck('id')->all());

        // Work out the "Linked to" choice from what is saved
        $defaultLinkType = $document->properties->isNotEmpty() && $document->clients->isNotEmpty()
            ? 'both'
            : ($document->clients->isNotEmpty() ? 'client' : 'property');
        $linkType = old('link_type', $defaultLinkType);
    @endphp

    <div class="container py-4" style="max-width: 720px;">
        <a href="{{ url('/admin/documents') }}" class="btn btn-link px-0 text-decoration-none">&larr; Back to documents</a>
        <h1 class="h4 mb-3">Edit document</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="card shadow-sm card-body">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="file" class="form-label">Replace file <span class="text-muted small">(optional)</span></label>
                @php
                    $ext = strtolower(pathinfo($document->file_location, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                    $isPdf = $ext === 'pdf';
                @endphp

                <div class="form-text mb-2">
                    <span id="preview-label">Current file:</span>
                    <a href="{{ route('documents.download', $document) }}" id="current-file-link">{{ basename($document->file_location) }}</a>
                    <span id="new-file-name" class="d-none fw-semibold"></span>
                </div>

                {{-- Preview: shows the saved file first, then the newly picked file --}}
                <img id="preview-img"
                     src="{{ $isImage ? route('documents.view', $document) : '' }}" alt="File preview"
                     class="img-fluid rounded border mb-2 {{ $isImage ? '' : 'd-none' }}" style="max-height: 300px;">
                <iframe id="preview-pdf"
                        src="{{ $isPdf ? route('documents.view', $document) : '' }}"
                        class="w-100 border rounded mb-2 {{ $isPdf ? '' : 'd-none' }}" style="height: 400px;"></iframe>
                <input type="file" id="file" name="file"
                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/*"
                       class="form-control form-control-lg @error('file') is-invalid @enderror">
                <div class="form-text">Leave empty to keep the current file. PDF, JPG or PNG, up to 5 MB.</div>
                <div class="form-text fw-semibold d-none" id="file-size"></div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Document name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $document->name) }}" required
                       class="form-control form-control-lg @error('name') is-invalid @enderror">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label for="inventory_code" class="form-label">Inventory code</label>
                    <input type="text" id="inventory_code" name="inventory_code" value="{{ old('inventory_code', $document->inventory_code) }}" required
                           class="form-control form-control-lg @error('inventory_code') is-invalid @enderror">
                </div>
                <div class="col-12 col-sm-6">
                    <label for="document_type" class="form-label">Document type</label>
                    <select id="document_type" name="document_type" required
                            class="form-select form-select-lg @error('document_type') is-invalid @enderror">
                        @foreach (['land_title' => 'Land title', 'deed' => 'Deed', 'contract' => 'Contract', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('document_type', $document->document_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Linked to</label>
                <div class="btn-group" role="group">
                    @foreach (['property' => 'Property', 'client' => 'Client', 'both' => 'Both'] as $value => $label)
                        <input type="radio" class="btn-check" name="link_type" id="lt_{{ $value }}"
                            value="{{ $value }}" autocomplete="off"
                            {{ $linkType === $value ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="lt_{{ $value }}">{{ $label }}</label>
                    @endforeach
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6" id="properties-wrap">
                    <label for="properties" class="form-label">Properties</label>
                    <input type="search" class="form-control form-control-sm mb-2 option-search" data-target="properties"
                           placeholder="Search properties..." autocomplete="off">
                    <select id="properties" name="properties[]" multiple size="5" class="form-select">
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}"
                                {{ in_array($property->id, $selectedProperties) ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6" id="clients-wrap">
                    <label for="clients" class="form-label">Clients</label>
                    <input type="search" class="form-control form-control-sm mb-2 option-search" data-target="clients"
                           placeholder="Search clients..." autocomplete="off">
                    <select id="clients" name="clients[]" multiple size="5" class="form-select">
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ in_array($client->id, $selectedClients) ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-text" id="link-help">Hold Ctrl (or Cmd) to pick several.</div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-muted small">(optional)</span></label>
                <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $document->description) }}</textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ url('/admin/documents') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>

    <script>
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        // Preview: swap to the newly picked file, restore the saved file if cleared
        (function () {
            const input    = document.getElementById('file');
            const img      = document.getElementById('preview-img');
            const pdf      = document.getElementById('preview-pdf');
            const label    = document.getElementById('preview-label');
            const link     = document.getElementById('current-file-link');
            const newName  = document.getElementById('new-file-name');

            // Remember what the saved file looked like
            const original = {
                img: img.getAttribute('src'), imgShown: !img.classList.contains('d-none'),
                pdf: pdf.getAttribute('src'), pdfShown: !pdf.classList.contains('d-none'),
            };
            let blobUrl = null;

            function clearBlob() {
                if (blobUrl) { URL.revokeObjectURL(blobUrl); blobUrl = null; }
            }

            function restore() {
                clearBlob();
                img.src = original.img || '';
                pdf.src = original.pdf || '';
                img.classList.toggle('d-none', !original.imgShown);
                pdf.classList.toggle('d-none', !original.pdfShown);
                label.textContent = 'Current file:';
                link.classList.remove('d-none');
                newName.classList.add('d-none');
                newName.textContent = '';
            }

            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) { restore(); return; }

                clearBlob();
                blobUrl = URL.createObjectURL(file);

                const isImage = file.type.startsWith('image/');
                const isPdf   = file.type === 'application/pdf';

                img.classList.toggle('d-none', !isImage);
                pdf.classList.toggle('d-none', !isPdf);
                if (isImage) img.src = blobUrl;
                if (isPdf)   pdf.src = blobUrl;

                label.textContent = 'New file (not saved yet):';
                link.classList.add('d-none');
                newName.textContent = file.name;
                newName.classList.remove('d-none');
            });
        })();

        // Show the size of a newly picked replacement file (red if over the 5 MB limit)
        document.getElementById('file').addEventListener('change', e => {
            const sizeText = document.getElementById('file-size');
            const file = e.target.files[0];
            sizeText.classList.remove('text-danger', 'text-success');
            if (!file) {
                sizeText.classList.add('d-none');
                sizeText.textContent = '';
                return;
            }
            const MAX_MB = 5; // keep in sync with the server limit
            const tooBig = file.size > MAX_MB * 1024 * 1024;
            sizeText.textContent = 'File size: ' + formatFileSize(file.size) +
                (tooBig ? ' - too large, the limit is ' + MAX_MB + ' MB.' : '');
            sizeText.classList.add(tooBig ? 'text-danger' : 'text-success');
            sizeText.classList.remove('d-none');
        });

        // Search boxes: filter the options in the matching select.
        // Selected options always stay visible so they are still submitted.
        document.querySelectorAll('.option-search').forEach(input => {
            const select = document.getElementById(input.dataset.target);
            const all = Array.from(select.options);

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') e.preventDefault();
            });

            input.addEventListener('input', () => {
                const q = input.value.trim().toLowerCase();
                select.innerHTML = '';
                all.forEach(opt => {
                    if (opt.selected || opt.text.toLowerCase().includes(q)) {
                        select.appendChild(opt);
                    }
                });
            });
        });

        // Show the property and/or client select depending on "Linked to"
        (function () {
            const radios = document.querySelectorAll('input[name="link_type"]');
            const parts = {
                property: { wrap: document.getElementById('properties-wrap'), select: document.getElementById('properties') },
                client:   { wrap: document.getElementById('clients-wrap'),    select: document.getElementById('clients') },
            };
            const help = document.getElementById('link-help');

            function toggle(part, show) {
                part.wrap.classList.toggle('d-none', !show);
                part.select.disabled = !show; // hidden selects are not submitted
                if (!show) {
                    Array.from(part.select.options).forEach(o => o.selected = false);
                }
            }

            function update() {
                const checked = document.querySelector('input[name="link_type"]:checked');
                const type = checked ? checked.value : 'property';
                toggle(parts.property, type === 'property' || type === 'both');
                toggle(parts.client, type === 'client' || type === 'both');
                help.textContent = (type === 'both'
                    ? 'Pick at least one property and one client. '
                    : type === 'client'
                        ? 'Pick at least one client. '
                        : 'Pick at least one property. ') + 'Hold Ctrl (or Cmd) to pick several.';
            }

            radios.forEach(r => r.addEventListener('change', update));
            update();
        })();
    </script>
</x-layout>