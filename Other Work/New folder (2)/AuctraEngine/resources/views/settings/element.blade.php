<x-app-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <x-form-card action="{{ route('users.store') }}" method="POST" showFileInput="true">

                <x-slot name="header">
                    <h4 class="card-title">@lang('pages.personalsettings.settingproject')</h4>
                </x-slot>

                <x-slot name="description">
                    <p class="text-muted">@lang('pages.personalsettings.settingprojectdescription')</p>
                </x-slot>

                <div class="row">

                    {{-- Project Name --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.nameproject')</label>
                        <input type="text" class="form-control" name="project_name" placeholder="Project Name">
                    </div>

                    {{-- Project Image --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.imagelogo')</label>
                        <input type="file" class="form-control" name="image">
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.description')</label>
                        <textarea class="form-control" name="bio" rows="3"></textarea>
                    </div>

                    {{-- Facebook --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.facebook')</label>
                        <input type="url" class="form-control" name="facebook"
                            placeholder="https://facebook.com/...">
                    </div>

                    {{-- Instagram --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.instagram')</label>
                        <input type="url" class="form-control" name="instagram"
                            placeholder="https://instagram.com/...">
                    </div>
                     {{-- Twitter --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.twitter')</label>
                        <input type="url" class="form-control" name="twitter"
                            placeholder="https://twitter.com/...">
                    </div>
                     {{-- LinkedIn --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">@lang('pages.personalsettings.linkedin')</label>
                        <input type="url" class="form-control" name="linkedin"
                            placeholder="https://linkedin.com/...">
                    </div>

                </div>

            </x-form-card>

            <x-form-card action="{{ route('users.store') }}" method="POST">

                <x-slot name="header">
                    <h4 class="card-title">@lang('pages.nameproject')</h4>
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
