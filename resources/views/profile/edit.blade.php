<x-app-layout>

    <x-slot name="header">
        <h2 class="h4 fw-semibold mb-0 text-dark">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4 bg-light min-vh-100">
        <div class="container">

            <div class="d-flex flex-column gap-4">

                <!-- Profile Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <div class="row justify-content-start">
                            <div class="col-lg-6">

                                @include('profile.partials.update-profile-information-form')

                            </div>
                        </div>

                    </div>
                </div>

                <!-- Password -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <div class="row">
                            <div class="col-lg-6">

                                @include('profile.partials.update-password-form')

                            </div>
                        </div>

                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card border-0 shadow-sm border border-danger-subtle">
                    <div class="card-body p-4 p-md-5">

                        <div class="row">
                            <div class="col-lg-6">

                                @include('profile.partials.delete-user-form')

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>