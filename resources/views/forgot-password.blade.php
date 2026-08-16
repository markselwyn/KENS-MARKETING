<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Ken's Marketing DSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-md p-6">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Ken's Marketing</h1>
            <p class="text-sm text-gray-500 mt-1">Account Recovery</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Forgot your password?</h2>
            <p class="text-sm text-gray-500 mb-6">No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>

            <form action="/forgot-password" method="POST" class="space-y-4">
                @csrf
                
                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Success Alert -->
                @if (session('status'))
                    <div class="p-3 mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg flex items-start gap-2">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-navy-700 focus:bg-white transition-colors" placeholder="your@email.com" required autofocus>
                    </div>
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 mt-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-navy-900 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition-colors">
                    Email Password Reset Link
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-600 mt-6 font-medium">
            Remember your password? <a href="{{ route('login') }}" class="text-navy-700 hover:text-navy-900 hover:underline">Back to login</a>
        </p>
    </div>
</body>
</html>