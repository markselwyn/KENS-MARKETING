<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Ken's Marketing DSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-md p-6">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Ken's Marketing</h1>
            <p class="text-sm text-gray-500 mt-1">Secure Password Reset</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Create New Password</h2>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Hidden Token Required by Laravel -->
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Email Field (Read-Only) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly required>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-navy-700 focus:bg-white transition-colors" placeholder="••••••••" required autofocus>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1 ml-1">Must be at least 8 characters long.</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-shield-check text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-navy-700 focus:bg-white transition-colors" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 mt-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-navy-900 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition-colors">
                    Update Password & Login
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>