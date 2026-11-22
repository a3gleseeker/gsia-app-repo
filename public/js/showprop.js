/* public/js/showprop.js
   Scripts for the property show page. Every part checks that its elements exist,
   so it is safe to load for users who can't see the edit modal. */
(function () {
    'use strict';

    const MAP_STYLE = 'https://tiles.openfreemap.org/styles/liberty';
    const DEFAULT_CENTER = [123.8854, 10.3157]; // [lng, lat], Cebu City

    function mapFail(container, message) {
        container.innerHTML =
            '<div class="d-flex h-100 align-items-center justify-content-center p-3 text-center text-danger small">'
            + message + '</div>';
    }

    /* ---------- Edit modal: reopen after a validation error or image removal ---------- */
    function initReopen(modalEl) {
        if (modalEl.dataset.reopen !== '1') return;
        window.addEventListener('load', function () {
            new bootstrap.Modal(modalEl).show();
        });
    }

    /* ---------- Edit modal: multi-pick image selector with 1 MB check ---------- */
    function initImagePicker() {
        const imagesInput = document.getElementById('images');
        const list = document.getElementById('selectedImages');
        if (!imagesInput || !list) return;

        const selected = new DataTransfer();
        const MAX_FILES = 10;
        const MAX_BYTES = 1024 * 1024;

        imagesInput.addEventListener('change', function () {
            const tooBig = [];

            for (const f of this.files) {
                if (f.size > MAX_BYTES) { tooBig.push(f.name); continue; }
                if (selected.files.length >= MAX_FILES) {
                    alert('Maximum of ' + MAX_FILES + ' images at a time.');
                    break;
                }

                const duplicate = Array.from(selected.files).some(x =>
                    x.name === f.name && x.size === f.size && x.lastModified === f.lastModified);
                if (!duplicate) selected.items.add(f);
            }

            if (tooBig.length) alert('Skipped (over 1 MB):\n' + tooBig.join('\n'));

            this.files = selected.files;
            renderSelected();
        });

        function renderSelected() {
            list.innerHTML = '';

            Array.from(selected.files).forEach((f, i) => {
                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center small border rounded px-2 py-1 mb-1';
                row.innerHTML = '<span></span><button type="button" class="btn-close btn-sm" aria-label="Remove"></button>';
                row.querySelector('span').textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
                row.querySelector('button').addEventListener('click', () => {
                    const keep = Array.from(selected.files).filter((_, j) => j !== i);
                    selected.items.clear();
                    keep.forEach(x => selected.items.add(x));
                    imagesInput.files = selected.files;
                    renderSelected();
                });
                list.appendChild(row);
            });
        }
    }

    /* ---------- Edit modal: map pin picker (built when the modal first opens) ---------- */
    function initEditMap(modalEl) {
        const box = document.getElementById('picker');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const clearBtn = document.getElementById('clearPin');
        if (!box || !latInput || !lngInput) return;

        let picker = null;
        let pin = null;

        modalEl.addEventListener('shown.bs.modal', function () {
            if (picker) { picker.resize(); return; }

            if (typeof maplibregl === 'undefined') {
                mapFail(box, 'The map library could not be loaded. Check your internet connection and reload.');
                return;
            }

            const hasPoint = latInput.value !== '' && lngInput.value !== '';
            const start = hasPoint
                ? [parseFloat(lngInput.value), parseFloat(latInput.value)]
                : DEFAULT_CENTER;

            try {
                picker = new maplibregl.Map({
                    container: 'picker',
                    style: MAP_STYLE,
                    center: start,
                    zoom: hasPoint ? 15 : 11
                });
            } catch (e) {
                console.error(e);
                mapFail(box, 'The map could not start. Your browser may have WebGL turned off.');
                return;
            }

            picker.addControl(new maplibregl.NavigationControl());
            if (hasPoint) {
                pin = new maplibregl.Marker({ color: '#14213d' }).setLngLat(start).addTo(picker);
            }

            picker.on('click', function (e) {
                if (pin) pin.setLngLat(e.lngLat);
                else pin = new maplibregl.Marker({ color: '#14213d' }).setLngLat(e.lngLat).addTo(picker);
                latInput.value = e.lngLat.lat.toFixed(7);
                lngInput.value = e.lngLat.lng.toFixed(7);
            });
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (pin) { pin.remove(); pin = null; }
                latInput.value = '';
                lngInput.value = '';
            });
        }
    }

    /* ---------- Edit modal: Delete Listing button ---------- */
    function initDelete() {
        const btn = document.getElementById('deleteBtn');
        const form = document.getElementById('deleteForm');
        if (!btn || !form) return;

        btn.addEventListener('click', function () {
            if (confirm('Delete this listing? This cannot be undone.')) form.submit();
        });
    }

    /* ---------- Gallery: counter and thumbnails follow the carousel ---------- */
    function initCarousel() {
        const carousel = document.getElementById('propertyCarousel');
        const counter = document.getElementById('galleryCounter');
        if (!carousel || !counter) return;

        const thumbs = document.querySelectorAll('#galleryThumbs .pd-thumb');
        const total = carousel.querySelectorAll('.carousel-item').length;
        if (total < 2) return;

        carousel.addEventListener('slide.bs.carousel', function (e) {
            counter.textContent = (e.to + 1) + ' / ' + total;
            thumbs.forEach((t, i) => t.classList.toggle('is-active', i === e.to));
            if (thumbs[e.to]) {
                thumbs[e.to].scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
            }
        });
    }

    /* ---------- Public location map (reads data-lat / data-lng / data-name) ---------- */
    function initPublicMap() {
        const box = document.getElementById('map');
        if (!box || !box.dataset.lat || !box.dataset.lng) return;

        if (typeof maplibregl === 'undefined') {
            mapFail(box, 'The map library could not be loaded.');
            return;
        }

        const pos = [parseFloat(box.dataset.lng), parseFloat(box.dataset.lat)]; 
        let map;

        try {
            map = new maplibregl.Map({
                container: 'map',
                style: MAP_STYLE,
                center: pos,
                zoom: 16.5,
                pitch: 50,
                bearing: -15,
                cooperativeGestures: true 
            });
        } catch (e) {
            console.error(e);
            mapFail(box, 'The map could not start. Your browser may have WebGL turned off.');
            return;
        }

        map.addControl(new maplibregl.NavigationControl({ visualizePitch: true }));
        map.addControl(new maplibregl.FullscreenControl());

        new maplibregl.Marker({ color: '#14213d' })
            .setLngLat(pos)
            .setPopup(new maplibregl.Popup({ offset: 25 }).setText(box.dataset.name || ''))
            .addTo(map);
    }

    /* ---------- Start everything ---------- */
    const modalEl = document.getElementById('editPropertyModal');
    if (modalEl) {
        initReopen(modalEl);
        initImagePicker();
        initEditMap(modalEl);
        initDelete();
    }
    initCarousel();
    initPublicMap();
})();