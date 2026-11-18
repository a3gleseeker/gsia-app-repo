<x-layout>
    <x-slot:title>
        POST PROPERTY PAGE
    </x-slot:title>
    <div class="container w-50 mt-5">
        <div class="heading text-center mb-5">
            <h1>Hello GSIA Employee</h1>
        </div>
        <form method='POST' action='#' enctype="multipart/form-data">
            @csrf

            <div class="mb-5 d-flex justify-content-center align-items-center">
                <img id="preview" style="max-width:200px">
            </div>

            <!-- 
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="file" id="photo" class="form-control" name='photo' accept="image/*"  required autofocus/>
                <label class="form-label" for="name">Image of Property</label>
            </div>
            -->

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="link" class="form-control" name='link' required autofocus/>
                <label class="form-label" for="link">Image Link</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="name" class="form-control" name='name' required autofocus/>
                <label class="form-label" for="name">Name</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="size" class="form-control" name='size' required/>
                <label class="form-label" for="size">Size</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="city" class="form-control" name='city' required/>
                <label class="form-label" for="city">City</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="postal" class="form-control" name='postal' required/>
                <label class="form-label" for="postal">Postal Code</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="type" class="form-control" name='type' required/>
                <label class="form-label" for="type">Type</label>
            </div>


            <div data-mdb-input-init class="form-outline mb-4">
                <input type="number" id="price" class="form-control" name='price' required/>
                <label class="form-label" for="price">Price</label>
            </div>

            <div class="input-group">
                <!-- Submit button -->
                <button 
                    type="submit" 
                    data-mdb-button-init data-mdb-ripple-init
                    class="btn btn-primary btn-block mb-4 form-control">Post Property
                </button>
            </div>

        </form>
    </div>
</x-layout>
<script>
  document.getElementById('photo').addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) {
      document.getElementById('preview').src = URL.createObjectURL(file);
    }
  });
</script>