<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - {{ $currentSchool?->name ?? config('app.name', 'SchoolOS360') }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- QRCode.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- HTML5 QRCode Scanner CDN -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; font-size: 12pt; margin: 0; padding: 0; }
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen flex">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col no-print z-20 shadow-xl">
        <!-- Brand / School Header -->
        <div class="h-16 flex items-center px-5 bg-slate-950/80 border-b border-slate-800 space-x-3">
            <div class="w-9 h-9 rounded-lg bg-teal-500 text-white flex items-center justify-center font-bold text-lg shadow shrink-0">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="overflow-hidden">
                <span class="text-white font-bold text-sm tracking-tight truncate block">{{ $currentSchool?->name ?? 'SchoolOS360' }}</span>
                <span class="text-teal-400 text-[11px] block font-medium truncate">
                    <i class="fa-solid fa-code-branch text-[9px] mr-1"></i>{{ $currentBranch?->name ?? 'Main Branch' }}
                </span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-1 text-sm">
            <div class="px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Main</div>
            
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('dashboard') || request()->is('/') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('branches.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('branches*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-building-columns w-5 text-center text-amber-400"></i>
                <span class="flex-1">Branches & Tenants</span>
                @if(isset($availableBranches))
                    <span class="text-[10px] bg-slate-800 text-amber-300 px-1.5 py-0.5 rounded-full">{{ count($availableBranches) }}</span>
                @endif
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Students & Staff</div>

            <a href="{{ route('students.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('students*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-user-graduate w-5 text-center"></i>
                <span>Students</span>
            </a>

            <a href="{{ route('staff.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ (request()->is('staff') || request()->is('staff/create') || (request()->is('staff/*') && !request()->is('staff/assignments'))) ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-chalkboard-user w-5 text-center"></i>
                <span>Staff Directory</span>
            </a>

            <a href="{{ route('staff.assignments') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('staff/assignments') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-user-tag w-5 text-center"></i>
                <span>Staff Role Assign</span>
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Attendance & Biometrics</div>

            <a href="{{ route('attendance.students.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('attendance/students*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-user w-5 text-center text-teal-400"></i>
                <span>Student Attendance</span>
            </a>

            <a href="{{ route('attendance.staff.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('attendance/staff*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-user-clock w-5 text-center text-emerald-400"></i>
                <span>Staff Attendance</span>
            </a>

            <a href="{{ route('attendance.biometric.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('attendance/biometric*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-fingerprint w-5 text-center text-cyan-400"></i>
                <span>Biometric Device Hub</span>
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">ID Cards & Academics</div>

            <a href="{{ route('idcards.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('id-cards*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-id-card w-5 text-center text-rose-400"></i>
                <span>ID Card Studio</span>
            </a>

            <a href="{{ route('sessions.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('sessions*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                <span>Academic Sessions</span>
            </a>

            <a href="{{ route('academics.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('academics*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-sitemap w-5 text-center"></i>
                <span>Classes & Houses</span>
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Fees & Invoices</div>

            <a href="{{ route('fees.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ (request()->is('fees') || request()->is('fees/invoices*') || request()->is('fees/receipts*')) ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-emerald-400"></i>
                <span>Fee Invoices & Pay</span>
            </a>

            <a href="{{ route('fees.structures') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('fees/structures') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-calculator w-5 text-center"></i>
                <span>Fee Structures</span>
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Exams & Marks</div>

            <a href="{{ route('exams.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('exams*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-check w-5 text-center text-violet-400"></i>
                <span>Exams & Timetables</span>
            </a>

            <a href="{{ route('marks.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('marks*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-pen-to-square w-5 text-center"></i>
                <span>Marks Entry (Manual/CSV)</span>
            </a>

            <div class="pt-3 px-2 py-1 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Utilities & Settings</div>

            <a href="{{ route('import_export.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('import-export*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-file-import w-5 text-center text-blue-400"></i>
                <span>Import & Export</span>
            </a>

            <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('settings*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-sliders w-5 text-center text-amber-400"></i>
                <span>School & Branch Settings</span>
            </a>

            @if(Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('users*') ? 'bg-teal-600 text-white shadow' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fa-solid fa-users-gear w-5 text-center"></i>
                <span>User Accounts</span>
            </a>
            @endif
        </div>

        <!-- Current User Info -->
        <div class="p-3 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-slate-700 text-teal-400 flex items-center justify-center font-bold uppercase text-xs border border-slate-600 shrink-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Guest' }}</p>
                        <span class="inline-block px-1.5 py-0.2 text-[9px] font-semibold uppercase tracking-wider rounded bg-teal-900/60 text-teal-300 border border-teal-700/50">
                            {{ Auth::user()->role ?? 'Operator' }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-rose-400 transition text-sm">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 no-print shadow-sm">
            <div class="flex items-center space-x-4">
                <h1 class="text-lg font-bold text-slate-800 truncate">
                    {{ $header ?? 'School Management Dashboard' }}
                </h1>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Branch Switcher Dropdown Form -->
                @if(isset($availableBranches) && count($availableBranches) > 0)
                <form action="{{ route('branches.switch') }}" method="POST" class="flex items-center">
                    @csrf
                    <div class="flex items-center bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg px-2.5 py-1 transition text-xs">
                        <i class="fa-solid fa-school text-teal-600 mr-2"></i>
                        <span class="font-semibold text-slate-600 mr-1.5">Branch:</span>
                        <select name="branch_id" onchange="this.form.submit()" class="bg-transparent font-bold text-slate-800 focus:outline-none cursor-pointer">
                            @foreach($availableBranches as $b)
                                <option value="{{ $b->id }}" {{ ($currentBranch && $currentBranch->id == $b->id) ? 'selected' : '' }}>
                                    {{ $b->name }} {{ $b->is_main ? '(Main)' : '' }} {{ !$b->isActive() ? '[Disabled]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @endif

                <!-- Active Session Badge -->
                @php
                    $activeSession = \App\Models\AcademicSession::current();
                @endphp
                <div class="hidden md:flex items-center space-x-1.5 bg-teal-50 border border-teal-200 text-teal-800 px-3 py-1 rounded-full text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Session: <strong>{{ $activeSession ? $activeSession->name : '2026-2027' }}</strong></span>
                </div>

                <!-- Quick Action Buttons -->
                <a href="{{ route('students.create') }}" class="inline-flex items-center space-x-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                    <i class="fa-solid fa-user-plus"></i>
                    <span class="hidden sm:inline">New Admission</span>
                </a>
            </div>
        </header>

        <!-- Disabled Branch Warning Banner -->
        @if($currentBranch && !$currentBranch->isActive())
        <div class="bg-amber-500 text-slate-950 px-6 py-2 flex items-center justify-between text-xs font-bold shadow no-print">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <span>NOTICE: The current branch '{{ $currentBranch->name }}' is currently marked as DISABLED. Operations may be restricted.</span>
            </div>
            <a href="{{ route('branches.index') }}" class="underline hover:text-white">Manage Branches &rarr;</a>
        </div>
        @endif

        <!-- Flash Messages -->
        <div class="px-6 pt-4 no-print">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-4 py-3 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 px-4 py-3 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-amber-50 border-l-4 border-amber-500 text-amber-800 px-4 py-3 rounded-r-lg shadow-sm">
                    <div class="flex items-center space-x-2 mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                        <span class="text-sm font-bold">Please check the form inputs:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Body Content -->
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>

</body>
</html>

