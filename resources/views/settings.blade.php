@extends('layouts.app')

@section('title', 'Settings - Ken\'s Marketing')
@section('header_title', 'Application Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    @if(session('success'))
        <div class="p-4 bg-white border border-green-100 rounded-xl shadow-sm flex items-center gap-3">
            <div class="w-8 h-8 text-green-600 bg-green-50 rounded-lg flex items-center justify-center"><i class="fa-solid fa-check"></i></div>
            <span class="text-sm font-medium text-gray-700">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-white border border-red-100 rounded-xl shadow-sm text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PATCH')

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-palette"></i></div>
                <div><h2 class="font-bold text-gray-800">Appearance</h2><p class="text-xs text-gray-500">Choose how the application looks on this account.</p></div>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach([
                    'light' => ['Light', 'fa-sun', 'Always use the light interface'],
                    'dark' => ['Dark', 'fa-moon', 'Always use the dark interface'],
                    'system' => ['System', 'fa-desktop', 'Follow your device appearance'],
                ] as $value => [$label, $icon, $description])
                    <label class="cursor-pointer">
                        <input type="radio" name="theme" value="{{ $value }}" class="peer sr-only" @checked(old('theme', $preferences['theme']) === $value)>
                        <span class="block h-full rounded-xl border border-gray-200 p-4 peer-checked:border-navy-700 peer-checked:ring-2 peer-checked:ring-navy-700/20 hover:bg-gray-50 transition-colors">
                            <i class="fa-solid {{ $icon }} text-navy-700"></i>
                            <span class="block text-sm font-bold text-gray-800 mt-3">{{ $label }}</span>
                            <span class="block text-xs text-gray-500 mt-1">{{ $description }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-compass"></i></div>
                <div><h2 class="font-bold text-gray-800">Navigation</h2><p class="text-xs text-gray-500">Control where you start and how navigation opens.</p></div>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="landing_page" class="block text-xs font-semibold text-gray-700 mb-1.5">Page shown after sign in</label>
                    <select id="landing_page" name="landing_page" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-700">
                        @foreach($landingPages as $routeName => $label)
                            <option value="{{ $routeName }}" @selected(old('landing_page', $preferences['landing_page']) === $routeName)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <fieldset>
                    <legend class="block text-xs font-semibold text-gray-700 mb-1.5">Default sidebar</legend>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['expanded' => 'Expanded', 'collapsed' => 'Collapsed'] as $value => $label)
                            <label class="cursor-pointer"><input type="radio" name="sidebar_state" value="{{ $value }}" class="peer sr-only" @checked(old('sidebar_state', $preferences['sidebar_state']) === $value)><span class="block text-center px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-600 peer-checked:border-navy-700 peer-checked:bg-blue-50 peer-checked:text-navy-700">{{ $label }}</span></label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center"><i class="fa-solid fa-universal-access"></i></div>
                <div><h2 class="font-bold text-gray-800">Accessibility and Density</h2><p class="text-xs text-gray-500">Adjust motion and spacing in navigation and search.</p></div>
            </div>
            <div class="p-5 space-y-5">
                <label class="flex items-center justify-between gap-4 cursor-pointer">
                    <span><span class="block text-sm font-semibold text-gray-800">Reduce motion</span><span class="block text-xs text-gray-500 mt-1">Disable decorative animations and transitions.</span></span>
                    <input type="hidden" name="reduced_motion" value="0">
                    <input type="checkbox" name="reduced_motion" value="1" class="w-5 h-5 rounded border-gray-300 text-navy-700 focus:ring-navy-700" @checked((bool) old('reduced_motion', $preferences['reduced_motion']))>
                </label>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-navy-900 text-white text-sm font-semibold rounded-lg hover:bg-navy-700 transition-colors flex items-center gap-2 shadow-sm"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
        </div>
    </form>
</div>
@endsection
