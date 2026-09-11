<x-layouts.guest title="Sign In">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border border-slate-100" x-data="{
        fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    }">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-teal-600 text-white rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-lg shadow-teal-500/30 mb-3">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">SchoolOS360</h2>
            <p class="text-slate-500 text-sm mt-1">ERP To Manage School Operations</p>
        </div>

        @if($errors->any())
            <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-3 rounded text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email', 'admin@school.com') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" value="password" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-600 text-xs cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                    <span class="ml-2">Remember this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-md hover:shadow-teal-500/20 transition flex items-center justify-center space-x-2">
                <span>Sign In to Portal</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-8 pt-6 border-t border-slate-200">
            <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Quick Fill Demo Accounts</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="fillDemo('admin@school.com', 'password')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium border border-slate-200 text-center transition">
                    <span class="block font-bold">Admin</span>
                    <span class="text-[10px] text-slate-500">Full Access</span>
                </button>
                <button type="button" @click="fillDemo('operator@school.com', 'password')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium border border-slate-200 text-center transition">
                    <span class="block font-bold">Operator</span>
                    <span class="text-[10px] text-slate-500">Admissions/Fee</span>
                </button>
                <button type="button" @click="fillDemo('teacher@school.com', 'password')" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium border border-slate-200 text-center transition">
                    <span class="block font-bold">Teacher</span>
                    <span class="text-[10px] text-slate-500">Marks/Exams</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.guest>

