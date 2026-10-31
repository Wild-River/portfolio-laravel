<x-admin-layout title="プロフィール">
    <h1 class="page-title">プロフィール</h1>

    <div class="card-stack">
        <div class="card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-admin-layout>
