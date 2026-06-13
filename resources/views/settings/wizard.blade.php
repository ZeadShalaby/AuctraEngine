<x-app-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <x-form-card action="{{ route('users.store') }}" method="POST" showFileInput="true">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

                
            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>


            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">Create User</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">Add new user</p>
                </x-slot>

                <div class="row">
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea1">Example textarea</label>
                        <textarea class="form-control" name="bio"></textarea>
                    </div>
                    <div class="col">
                        <label class="form-label" for="exampleFormControlTextarea2">Example textarea</label>
                        <textarea class="form-control" name="notes"></textarea>
                    </div>
                </div>

            </x-form-card>
            <button type="submit" class="btn btn-primary">Submit</button>

        </div>
    </div>
</x-app-layout>
