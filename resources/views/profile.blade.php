@extends('layouts.app')

@section('title', 'My Profile - Ken\'s Marketing')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 animate-fade-in">
    @if(session('success'))
        <div class="p-4 bg-white border border-green-100 rounded-xl shadow-sm flex items-center gap-3"><div class="w-8 h-8 text-green-600 bg-green-50 rounded-lg flex items-center justify-center"><i class="fa-solid fa-check"></i></div><span class="text-sm font-medium text-gray-700">{{ session('success') }}</span></div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-white border border-red-100 rounded-xl shadow-sm flex items-start gap-3">
            <div class="w-8 h-8 text-red-600 bg-red-50 rounded-lg flex items-center justify-center shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div><p class="text-sm font-bold text-gray-800">Please correct the following:</p><ul class="mt-1 text-xs text-red-600 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

        <div class="bg-gradient-to-r from-navy-900 to-navy-700 rounded-2xl p-6 md:p-8 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="relative w-24 h-24 shrink-0">
                    <div class="w-24 h-24 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-2xl font-bold uppercase overflow-hidden">
                        @if($user->profile_photo_path)
                            <img id="profile-photo-preview" src="{{ route('profile.photo') }}" alt="Profile photo" class="w-full h-full object-cover"><span id="profile-photo-initials" class="hidden">{{ substr($user->name, 0, 2) }}</span>
                        @else
                            <img id="profile-photo-preview" alt="Profile photo preview" class="hidden w-full h-full object-cover"><span id="profile-photo-initials">{{ substr($user->name, 0, 2) }}</span>
                        @endif
                    </div>
                    <label for="profile_photo" class="absolute -bottom-2 -right-2 w-9 h-9 rounded-full bg-white text-navy-900 shadow-lg flex items-center justify-center cursor-pointer hover:bg-blue-50" title="Choose profile photo"><i class="fa-solid fa-camera text-sm"></i></label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewProfilePhoto(event)">
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2"><h2 class="text-2xl font-bold truncate">{{ $user->name }}</h2><span class="px-2.5 py-1 rounded-full bg-white/15 border border-white/20 text-[10px] font-bold uppercase tracking-wider">{{ strtolower(trim($user->role)) }}</span></div>
                    <p class="text-sm text-blue-100 mt-1 break-all">{{ $user->email }}</p>
                    <p class="text-xs text-blue-200 mt-3"><i class="fa-regular fa-calendar mr-1"></i> Member since {{ \Carbon\Carbon::parse($user->created_at)->format('F Y') }}</p>
                    <p id="selected-photo-name" class="text-xs text-white mt-2 hidden"></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <section class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-blue-50 text-navy-700 flex items-center justify-center"><i class="fa-solid fa-address-card"></i></div><div><h3 class="text-base font-bold text-gray-800">Profile Information</h3><p class="text-xs text-gray-500">Update your name, email, and picture.</p></div></div>
                <div class="p-5 space-y-5">
                    <div><label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">Full name</label><input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="255" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white"></div>
                    <div><label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Email address</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white"><p class="text-[11px] text-gray-400 mt-1.5">This email is used when signing in.</p></div>
                </div>
            </section>

            <aside id="account-settings" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-24">
                <div class="p-5 border-b border-gray-100 flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center"><i class="fa-solid fa-lock"></i></div><div><h3 class="text-base font-bold text-gray-800">Security Settings</h3><p class="text-xs text-gray-500">Optionally change your password.</p></div></div>
                <div class="p-5 space-y-4">
                    <div><label for="current_password" class="block text-xs font-semibold text-gray-700 mb-1.5">Current password</label><input type="password" id="current_password" name="current_password" autocomplete="current-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white"></div>
                    <div><label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">New password</label><input type="password" id="password" name="password" minlength="8" autocomplete="new-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white"></div>
                    <div><label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">Confirm new password</label><input type="password" id="password_confirmation" name="password_confirmation" minlength="8" autocomplete="new-password" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white"></div>
                    <p class="text-[11px] text-gray-400">Leave password fields blank to keep your current password.</p>
                    <button type="submit" class="w-full px-4 py-2.5 bg-navy-900 text-white text-sm font-semibold rounded-lg hover:bg-navy-700 transition-colors flex items-center justify-center gap-2 shadow-sm"><i class="fa-solid fa-floppy-disk"></i> Save Profile</button>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
    function previewProfilePhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        const preview = document.getElementById('profile-photo-preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        document.getElementById('profile-photo-initials').classList.add('hidden');
        const fileName = document.getElementById('selected-photo-name');
        fileName.textContent = `Selected: ${file.name}`;
        fileName.classList.remove('hidden');
    }
</script>
@endsection
