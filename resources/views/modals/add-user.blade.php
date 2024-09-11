<div class="modal fade" id="modal-center" tabindex="-1" role="dialog" aria-labelledby="modal-centerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 text-center text-xl" id="add-user-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal">
                </button>
            </div>
            <div class>
                <x-guest-layout>
                    <form id="add-user-form"> {{-- method="POST" action="{{ route('register.new') }}">--}}
                        @csrf
                        <input type="text" name="id" id="id" value="" class="d-none">
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />

                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" minlength="8" required autocomplete="new-password" />
                            <button type="button" class="btn togglePassword absolute" style="right: 5.80rem; top:24.8rem;">
                                Show
                            </button>
                            
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <label for="projects">Select Projects</label>
                            <select name="project_ids[]" id="projects" multiple>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id='role' name="role" class="form-select" aria-label="Default select example" required>
                                <option value="" selected>Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
{{--                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">--}}
{{--                                {{ __('Already registered?') }}--}}
{{--                            </a>--}}

                            <x-primary-button class="ms-4" id="add-user-submit">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
                </x-guest-layout>
            </div>
        </div>
    </div>
</div>
