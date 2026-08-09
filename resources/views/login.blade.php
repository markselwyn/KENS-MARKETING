<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ken's Marketing DSS</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-6">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="Ken's Marketing Logo" class="h-24 w-auto mx-auto mb-4 object-contain">
            
            <h1 class="text-2xl font-bold text-gray-900">Ken's Marketing</h1>
            <p class="text-sm text-gray-500 mt-1">Decision Support System Portal</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Sign in to your account</h2>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Global error alert box -->
                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" 
                            value="{{ old('email') }}" 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('email') ? 'border-red-400 focus:ring-red-500' : 'border-gray-200 focus:ring-navy-700' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:bg-white transition-colors" 
                            placeholder="admin@kensmarketing.com" required autofocus>
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <a href="#" class="text-xs font-medium text-navy-700 hover:text-navy-900 hover:underline transition-colors">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <!-- Extra right padding 'pr-10' added to keep text clear of the eye icon -->
                        <input type="password" id="password" name="password" 
                            class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border {{ $errors->has('password') ? 'border-red-400 focus:ring-red-500' : 'border-gray-200 focus:ring-navy-700' }} rounded-lg text-sm focus:outline-none focus:ring-2 focus:bg-white transition-colors" 
                            placeholder="••••••••" required>
                        
                        <!-- Advanced UX Feature: Interactive Show/Hide Password Toggle -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i id="password-icon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-navy-700 focus:ring-navy-900 border-gray-300 rounded cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-navy-900 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition-colors">
                    Sign In
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            &copy; {{ date('Y') }} Ken's Marketing. All rights reserved.
        </p>
    </div>

    <!-- Client-side script for the password eye toggle -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-regular', 'fa-eye');
                passwordIcon.classList.add('fa-solid', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-solid', 'fa-eye-slash');
                passwordIcon.classList.add('fa-regular', 'fa-eye');
            }
        }
    </script>
</body>
</html>