<x-layouts.app title="Executive Dashboard">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Operational Dashboard</span>
            <span class="text-xs bg-teal-100 text-teal-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $branch?->name ?? 'Main Campus' }}
            </span>
        </div>
    </x-slot>

    <!-- Top Branch Overview & Welcome Banner -->
    <div class="mb-6 bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-6 -bottom-8 opacity-10 text-9xl">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-teal-400 text-xs font-semibold uppercase tracking-wider mb-1">
                    <span>{{ $school?->name ?? 'SchoolOS360' }}</span>
                    <span>&bull;</span>
                    <span>{{ $branch?->name ?? 'Main Branch' }}</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Live branch performance metrics, attendance tracking, fee receipts, and examination schedule for academic session <strong>{{ $currentSession?->name ?? '2026-2027' }}</strong>.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('attendance.students.index') }}" class="inline-flex items-center space-x-2 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-3.5 py-2 rounded-xl text-xs shadow transition">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>QR Attendance</span>
                </a>
                <a href="{{ route('fees.index') }}" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-3.5 py-2 rounded-xl text-xs border border-slate-700 transition">
                    <i class="fa-solid fa-receipt text-emerald-400"></i>
                    <span>Collect Fee</span>
                </a>
                <a href="{{ route('idcards.index') }}" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-3.5 py-2 rounded-xl text-xs border border-slate-700 transition">
                    <i class="fa-solid fa-id-card text-rose-400"></i>
                    <span>ID Cards</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Interlinked Metric Tiles (8 interactive cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        
        <!-- Tile 1: Active Students (Interlinked) -->
        <a href="{{ route('students.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-teal-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Students</span>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 group-hover:bg-teal-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-user-graduate text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-3xl font-black text-slate-900">{{ number_format($totalStudents) }}</div>
                <p class="text-xs text-slate-500 mt-1">
                    <span class="text-emerald-600 font-bold">{{ $totalStudents }} active</span> / {{ $allStudentsCount }} total enrolled
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-teal-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>View Directory &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 2: Faculty & Staff (Interlinked) -->
        <a href="{{ route('staff.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-indigo-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Staff & Teachers</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-chalkboard-user text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-3xl font-black text-slate-900">{{ number_format($totalStaff) }}</div>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $totalTeachers }} Teachers &bull; {{ $totalOperators }} Operators
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-indigo-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Manage Staff &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 3: Student Attendance Rate (Interlinked) -->
        <a href="{{ route('attendance.students.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-emerald-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Students Attn</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-clipboard-check text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="flex items-baseline space-x-2">
                    <div class="text-3xl font-black text-slate-900">{{ $studentAttendanceRate }}%</div>
                    <span class="text-xs text-emerald-600 font-bold">present</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ min(100, max(5, $studentAttendanceRate)) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">
                    {{ $todayStudentPresent }} present &bull; {{ $todayStudentAbsent }} absent
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Mark Attendance &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 4: Staff Attendance (Interlinked) -->
        <a href="{{ route('attendance.staff.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-cyan-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Staff Attn</span>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-user-clock text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="flex items-baseline space-x-2">
                    <div class="text-3xl font-black text-slate-900">{{ $staffAttendanceRate }}%</div>
                    <span class="text-xs text-cyan-600 font-bold">present</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-cyan-500 h-2 rounded-full transition-all" style="width: {{ min(100, max(5, $staffAttendanceRate)) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">
                    {{ $todayStaffPresent }} of {{ $totalStaff }} checked in today
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-cyan-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Staff Roster & Biometrics &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 5: Monthly Collections (Interlinked) -->
        <a href="{{ route('fees.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-emerald-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Collected This Month</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-hand-holding-dollar text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-3xl font-black text-emerald-600">
                    {{ $currencySymbol }}{{ number_format($monthCollections, 0) }}
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Total Lifetime: {{ $currencySymbol }}{{ number_format($totalCollected, 0) }}
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>View Invoices &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 6: Pending Fee Dues (Interlinked) -->
        <a href="{{ route('fees.index', ['status' => 'unpaid']) }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-rose-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Fee Dues</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-file-invoice text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-3xl font-black text-rose-600">
                    {{ $currencySymbol }}{{ number_format($totalPending, 0) }}
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    <span class="font-bold text-rose-500">{{ $unpaidInvoicesCount }}</span> unpaid or partial invoices
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Collect Pending Fees &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 7: Active Exams (Interlinked) -->
        <a href="{{ route('exams.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-violet-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Exams Scheduled</span>
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 group-hover:bg-violet-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-calendar-check text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-3xl font-black text-slate-900">{{ count($upcomingExams) }}</div>
                <p class="text-xs text-slate-500 mt-1 truncate">
                    @if(count($upcomingExams) > 0)
                        Next: {{ $upcomingExams->first()->name }}
                    @else
                        No pending examinations
                    @endif
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-violet-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Timetables & Print &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

        <!-- Tile 8: Biometric Hub (Interlinked) -->
        <a href="{{ route('attendance.biometric.index') }}" class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-200 hover:border-amber-500 hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Biometric Devices</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-fingerprint text-base"></i>
                </div>
            </div>
            <div class="my-3">
                <div class="text-xl font-black text-slate-900 flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Device Sync Active</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Simulate punches, view raw device logs, or upload CSV.
                </p>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-amber-600 font-semibold group-hover:translate-x-1 transition-transform">
                <span>Open Biometric Hub &rarr;</span>
                <i class="fa-solid fa-circle-chevron-right text-[11px]"></i>
            </div>
        </a>

    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 mb-6">
        <div class="flex items-center justify-between mb-3 px-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Quick Shortcuts</h3>
            <span class="text-xs text-slate-400">Common administrative workflows</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-center">
            <a href="{{ route('students.create') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-teal-50 border border-slate-100 hover:border-teal-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-user-plus text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">New Student</span>
            </a>

            <a href="{{ route('attendance.students.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-calendar-check text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Mark Attendance</span>
            </a>

            <a href="{{ route('fees.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-100 hover:border-blue-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Fee Invoices</span>
            </a>

            <a href="{{ route('idcards.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-rose-50 border border-slate-100 hover:border-rose-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-id-card text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Print ID Cards</span>
            </a>

            <a href="{{ route('marks.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-violet-50 border border-slate-100 hover:border-violet-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Enter Marks</span>
            </a>

            <a href="{{ route('import_export.index') }}" class="p-3 rounded-xl bg-slate-50 hover:bg-amber-50 border border-slate-100 hover:border-amber-200 transition flex flex-col items-center">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center mb-1.5">
                    <i class="fa-solid fa-file-csv text-sm"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">CSV Hub</span>
            </a>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Monthly Collections Trend (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Fee Collections Trend</h3>
                    <p class="text-xs text-slate-500">Monthly receipt totals (in {{ $currencySymbol }})</p>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                    Active Session
                </span>
            </div>
            <div class="h-64">
                <canvas id="feeChart"></canvas>
            </div>
        </div>

        <!-- Student Distribution by House (1 column) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800">House Distribution</h3>
                    <p class="text-xs text-slate-500">Student enrollment by house</p>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="houseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Admissions & Recent Payments (2 Column Tables) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Recent Student Admissions</h3>
                    <p class="text-xs text-slate-500">Latest students registered in this branch</p>
                </div>
                <a href="{{ route('students.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-bold">
                    View All &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentStudents as $s)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $s->avatar_url }}" class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="Avatar">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $s->full_name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $s->admission_no }} &bull; {{ $s->schoolClass?->name }} ({{ $s->section?->name }})
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($s->house)
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-xs" style="background-color: {{ $s->house->color }};">
                                {{ $s->house->name }}
                            </span>
                        @endif
                        <span class="block text-[11px] text-slate-400 mt-1">
                            {{ $s->blood_group ?: 'N/A' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    No student registrations recorded yet.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Fee Receipts -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Recent Fee Receipts</h3>
                    <p class="text-xs text-slate-500">Payments recorded with branch receipt numbers</p>
                </div>
                <a href="{{ route('fees.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold">
                    View All &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentPayments as $p)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                {{ $p->receipt_no }}
                            </span>
                            <span class="text-[11px] font-semibold text-teal-700 uppercase bg-teal-50 px-1.5 py-0.5 rounded">
                                {{ $p->payment_method }}
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-800 mt-1">
                            {{ $p->student?->full_name }} ({{ $p->student?->schoolClass?->name }})
                        </p>
                        <p class="text-[11px] text-slate-400">{{ $p->payment_date->format('d M, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-black text-emerald-600">
                            {{ $currencySymbol }}{{ number_format($p->amount_paid, 2) }}
                        </span>
                        <a href="{{ route('fees.receipt', $p->receipt_no) }}" class="block text-xs text-teal-600 hover:underline font-semibold mt-1">
                            <i class="fa-solid fa-print text-[10px] mr-1"></i>Print Receipt
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    No fee payments recorded yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js initialization script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Fee Trend Chart
            const feeCtx = document.getElementById('feeChart');
            if (feeCtx) {
                new Chart(feeCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($monthlyFeeLabels),
                        datasets: [{
                            label: 'Fee Collection ({{ $currencySymbol }})',
                            data: @json($monthlyFeeData),
                            backgroundColor: 'rgba(13, 148, 136, 0.75)',
                            borderColor: 'rgb(13, 148, 136)',
                            borderWidth: 2,
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { size: 11 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // 2. House Distribution Chart
            const houseCtx = document.getElementById('houseChart');
            if (houseCtx) {
                const houses = @json($housesWithCount);
                new Chart(houseCtx, {
                    type: 'doughnut',
                    data: {
                        labels: houses.map(h => h.name),
                        datasets: [{
                            data: houses.map(h => h.students_count),
                            backgroundColor: houses.map(h => h.color || '#0d9488'),
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>
