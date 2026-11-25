<x-layout>
    <x-slot:title>
        Sign In
    </x-slot:title>

    <div class="container w-50 mt-5">
        <div class="heading text-center mb-5">
            <h1>Hello GSIA Employee</h1>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
        @endif

        <form method='POST' action='/login'>
            @csrf

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="name" class="form-control" name='name' required autofocus/>
                <label class="form-label" for="name">Username</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="password" class="form-control" name='password' required/>
                <label class="form-label" for="password">Password</label>
            </div>


            <div class="input-group">
                <!-- Submit button -->
                <button 
                    type="submit" 
                    data-mdb-button-init data-mdb-ripple-init
                    class="btn btn-primary btn-block mb-4 form-control">Sign in
                </button>
            </div>
        </form>
    </div>
</x-layout>