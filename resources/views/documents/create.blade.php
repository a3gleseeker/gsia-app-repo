<x-layout>
    <x-slot:title>Add Document</x-slot:title>

    <div class="container py-4" style="max-width: 720px;">
        <a href="{{ url('/admin/documents') }}" class="btn btn-link px-0 text-decoration-none">&larr; Back to documents</a>
        <h1 class="h4 mb-3">Add a document</h1>

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

        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="card shadow-sm card-body">
            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">File</label>
                <input type="file" id="file" name="file" required
                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/*"
                       class="form-control form-control-lg @error('file') is-invalid @enderror">
                <div class="form-text">PDF, JPG or PNG, up to 5 MB. On a phone you can take a photo of the document.</div>
                <div class="form-text fw-semibold d-none" id="file-size"></div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Document name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="form-control form-control-lg @error('name') is-invalid @enderror">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label for="inventory_code" class="form-label">Inventory code</label>
                    <input type="text" id="inventory_code" name="inventory_code" value="{{ old('inventory_code') }}" required
                           class="form-control form-control-lg @error('inventory_code') is-invalid @enderror">
                </div>
                <div class="col-12 col-sm-6">
                    <label for="document_type" class="form-label">Document type</label>
                    <select id="document_type" name="document_type" required
                            class="form-select form-select-lg @error('document_type') is-invalid @enderror">
                        <option value="" disabled {{ old('document_type') ? '' : 'selected' }}>Select a type</option>
                        @foreach (['land_title' => 'Land title', 'deed' => 'Deed', 'contract' => 'Contract', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('document_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                            {{ old('link_type', 'property') === $value ? 'checked' : '' }}>
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
                                {{ in_array($property->id, old('properties', [])) ? 'selected' : '' }}>
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
                                {{ in_array($client->id, old('clients', [])) ? 'selected' : '' }}>
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
                <textarea id="description" name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ url('/admin/documents') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="after" value="another" class="btn btn-outline-primary">Save &amp; add another</button>
                <button type="submit" name="after" value="finish" class="btn btn-primary">Save document</button>
            </div>
        </form>
    </div>

    <script>
        // Turn a byte count into a readable size, e.g. "2.4 MB"
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        // Show the file size after a file is picked (red if over the 5 MB limit)
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

        // Fill the name from the file name, only if the name box is still empty
        document.getElementById('file').addEventListener('change', e => {
            const nameBox = document.getElementById('name');
            const file = e.target.files[0];
            if (file && nameBox.value.trim() === '') {
                nameBox.value = file.name.replace(/\.[^.]+$/, '');
            }
        });

        // Search boxes: filter the options in the matching select.
        // Selected options always stay visible so they are still submitted.
        document.querySelectorAll('.option-search').forEach(input => {
            const select = document.getElementById(input.dataset.target);
            const all = Array.from(select.options); // original order

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') e.preventDefault(); // don't submit the form
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