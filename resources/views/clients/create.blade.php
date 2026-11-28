<x-layout>
    <x-slot:title>Add Client</x-slot:title>

    <div class="container py-4" style="max-width: 640px;">
        <a href="{{ url('/admin/clients') }}" class="btn btn-link px-0 text-decoration-none">&larr; Back to clients</a>
        <h1 class="h4 mb-3">Add a client</h1>

        <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" class="card shadow-sm card-body">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="form-control form-control-lg @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required inputmode="tel"
                       class="form-control form-control-lg @error('phone') is-invalid @enderror">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-muted small">(optional)</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-control form-control-lg @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="photo" class="form-label">Photo <span class="text-muted small">(optional)</span></label>

                <div class="d-flex align-items-center gap-3 mb-2">
                    <img id="photoPreview" src="" alt="Photo preview"
                        class="rounded-circle border d-none"
                        style="width: 96px; height: 96px; object-fit: cover;">
                    <button type="button" id="photoRemove" class="btn btn-outline-danger btn-sm d-none">Remove</button>
                </div>

                <input type="file" id="photo" name="photo" accept="image/*"
                    class="form-control @error('photo') is-invalid @enderror">
                @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">JPG, PNG or WebP, up to 2 MB.</div>
                <div id="photoError" class="text-danger small d-none"></div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ url('/admin/clients') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save client</button>
            </div>
        </form>
    </div>


    <script>
        const photoInput   = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoRemove  = document.getElementById('photoRemove');
        const photoError   = document.getElementById('photoError');
        const MAX_BYTES    = 2 * 1024 * 1024;

        function clearPhoto() {
            if (photoPreview.src) URL.revokeObjectURL(photoPreview.src);
            photoInput.value = '';
            photoPreview.removeAttribute('src');
            photoPreview.classList.add('d-none');
            photoRemove.classList.add('d-none');
        }

        photoInput.addEventListener('change', () => {
            photoError.classList.add('d-none');
            const file = photoInput.files[0];
            if (!file) return clearPhoto();

            if (!file.type.startsWith('image/')) {
                clearPhoto();
                photoError.textContent = 'Please choose an image file.';
                return photoError.classList.remove('d-none');
            }
            if (file.size > MAX_BYTES) {
                clearPhoto();
                photoError.textContent = 'That image is over 2 MB. Please choose a smaller one.';
                return photoError.classList.remove('d-none');
            }

            if (photoPreview.src) URL.revokeObjectURL(photoPreview.src);
            photoPreview.src = URL.createObjectURL(file);
            photoPreview.classList.remove('d-none');
            photoRemove.classList.remove('d-none');
        });

        photoRemove.addEventListener('click', clearPhoto);
    </script>
</x-layout>