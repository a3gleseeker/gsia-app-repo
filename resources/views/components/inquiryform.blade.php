@props(['property'])

{{-- resources/views/components/inquiry-modal.blade.php
     Usage: <x-inquiry-modal :property="$property" /> --}}
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryLabel" aria-hidden="true"
     data-reopen="{{ $errors->inquiry->any() ? '1' : '0' }}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST"
              action="{{ route('properties.inquiries.store', $property) }}">
            @csrf

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="inquiryLabel">Inquire about this property</h5>
                    <div class="text-muted small">{{ $property->name }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Honeypot: real visitors never see or fill this. Bots often do. --}}
                <div class="d-none" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label for="inq-name" class="form-label">Full name</label>
                        <input type="text" id="inq-name" name="name" autocomplete="name"
                               class="form-control @error('name', 'inquiry') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name', 'inquiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="inq-email" class="form-label">Email</label>
                        <input type="email" id="inq-email" name="email" autocomplete="email"
                               class="form-control @error('email', 'inquiry') is-invalid @enderror"
                               value="{{ old('email') }}">
                        @error('email', 'inquiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="inq-phone" class="form-label">
                            Phone <span class="text-muted small">(optional)</span>
                        </label>
                        <input type="tel" id="inq-phone" name="phone" inputmode="tel" autocomplete="tel"
                               class="form-control @error('phone', 'inquiry') is-invalid @enderror"
                               value="{{ old('phone') }}">
                        @error('phone', 'inquiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label for="inq-message" class="form-label">Message</label>
                        <textarea id="inq-message" name="message" rows="4"
                                  class="form-control @error('message', 'inquiry') is-invalid @enderror"
                                  placeholder="Tell us when you'd like to visit or what you'd like to know."
                                  required>{{ old('message') }}</textarea>
                        @error('message', 'inquiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark px-4">Send inquiry</button>
            </div>
        </form>
    </div>
</div>