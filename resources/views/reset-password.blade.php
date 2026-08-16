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
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly required>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" class="w-full h-[42px] pl-10 pr-11 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-navy-700 focus:bg-white transition-colors" placeholder="••••••••" required autofocus>
                        <button type="button" class="password-toggle absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" data-target="password" aria-label="Show new password" aria-pressed="false">
                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="mt-2 space-y-1 text-xs" aria-live="polite">
                        <p id="length-requirement" class="text-gray-500"><i class="hidden" aria-hidden="true"></i>At least 8 characters (<span id="character-count">0</span>/8)</p>
                        <p id="match-requirement" class="text-gray-500"><i class="hidden" aria-hidden="true"></i>Both password fields match</p>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-shield-check text-gray-400"></i>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full h-[42px] pl-10 pr-11 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-navy-700 focus:bg-white transition-colors" placeholder="••••••••" required>
                        <button type="button" class="password-toggle absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" data-target="password_confirmation" aria-label="Show password confirmation" aria-pressed="false">
                            <i class="fa-regular fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 mt-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-navy-900 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-900 transition-colors">
                    Update Password & Login
                    <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const characterCount = document.getElementById('character-count');
        const lengthRequirement = document.getElementById('length-requirement');
        const matchRequirement = document.getElementById('match-requirement');

        function setRequirementState(element, satisfied) {
            element.classList.toggle('text-gray-500', !satisfied);
            element.classList.toggle('text-green-700', satisfied);
            element.querySelector('i').className = satisfied
                ? 'fa-solid fa-circle-check mr-1'
                : 'hidden';
        }

        function updatePasswordPreview() {
            characterCount.textContent = Math.min(password.value.length, 8);
            setRequirementState(lengthRequirement, password.value.length >= 8);
            setRequirementState(matchRequirement, confirmation.value.length > 0 && password.value === confirmation.value);
        }

        document.querySelectorAll('.password-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                button.setAttribute('aria-pressed', String(!isVisible));
                button.setAttribute('aria-label', `${isVisible ? 'Show' : 'Hide'} ${button.dataset.target === 'password' ? 'new password' : 'password confirmation'}`);
                button.querySelector('i').className = isVisible ? 'fa-regular fa-eye' : 'fa-solid fa-eye-slash';
            });
        });

        password.addEventListener('input', updatePasswordPreview);
        confirmation.addEventListener('input', updatePasswordPreview);
    </script>
</body>
</html>
