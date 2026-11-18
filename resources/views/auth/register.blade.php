<x-layout>
    <x-slot:title>
        Register Employee
    </x-slot:title>

    <div class="container w-50 mt-5">
        <div class="heading text-center mb-5">
            <h1>Create an Employee Account</h1>
        </div>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class='mb-0'>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method='POST' action='/register'>
            @csrf

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="text" id="name" class="form-control" name='name' required autofocus/>
                <label class="form-label" for="name">Username</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" id="form2Example1" class="form-control" name='email' required/>
                <label class="form-label" for="form2Example1">Email</label>
            </div>
            
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="form2Example2" class="form-control" name='password' required/>
                <label class="form-label" for="form2Example2">Password</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required />
                <label class="form-label" for="password_confirmation">Confirm Password</label>
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