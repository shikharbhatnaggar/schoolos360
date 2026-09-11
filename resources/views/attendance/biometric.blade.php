<x-layouts.app title="Biometric Attendance Hub">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Biometric Attendance Hub</span>
            <span class="text-xs bg-cyan-100 text-cyan-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $currentBranch?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Navigation Tabs -->
    <div class="flex items-center space-x-2 mb-6 border-b border-slate-200 pb-3">
        <a href="{{ route('attendance.students.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
            <i class="fa-solid fa-graduation-cap mr-1.5"></i>Student Attendance
        </a>
        <a href="{{ route('attendance.staff.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
            <i class="fa-solid fa-user-tie mr-1.5"></i>Staff Roster & QR
        </a>
        <a href="{{ route('attendance.biometric.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-cyan-600 text-white shadow-sm transition">
            <i class="fa-solid fa-fingerprint mr-1.5"></i>Biometric Device Hub
        </a>
    </div>

    <!-- Status & Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Device Simulator</p>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-sm font-bold text-slate-800">BIO-GATE-01</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Registered Staff</p>
                <h4 class="text-lg font-black text-slate-800">{{ $staffMembers->count() }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Pending Logs</p>
                <h4 class="text-lg font-black text-slate-800">{{ $pendingCount }}</h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase">Attendance Sync</p>
                <span class="text-xs text-slate-600 font-medium">Auto-sync enabled</span>
            </div>
            <form method="POST" action="{{ route('attendance.biometric.sync') }}">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Sync Now</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Simulator & CSV Upload Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Punch Simulator Card -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-fingerprint text-cyan-400"></i>
                        <span>Live Biometric Machine Simulator</span>
                    </h3>
                    <p class="text-xs text-slate-300">Simulate staff fingerprint / face recognition punch</p>
                </div>
                <span class="text-[10px] bg-cyan-500/20 text-cyan-300 font-mono px-2.5 py-1 rounded-lg border border-cyan-500/30">
                    STATUS: READY
                </span>
            </div>

            <form method="POST" action="{{ route('attendance.biometric.simulate') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Staff Member</label>
                    <select name="employee_id" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <option value="">-- Choose Employee / Teacher --</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->employee_id }}">
                                [{{ $staff->employee_id }}] {{ $staff->full_name }} &mdash; {{ $staff->designation ?? ucwords(str_replace('_', ' ', $staff->role_type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Punch Direction</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center space-x-2 p-3 border-2 border-emerald-300 bg-emerald-50 rounded-xl cursor-pointer hover:bg-emerald-100 transition text-emerald-800 font-bold text-xs">
                                <input type="radio" name="punch_type" value="in" checked class="text-emerald-600 focus:ring-emerald-500">
                                <span><i class="fa-solid fa-arrow-right-to-bracket mr-1"></i>PUNCH IN</span>
                            </label>
                            <label class="flex items-center justify-center space-x-2 p-3 border-2 border-amber-300 bg-amber-50 rounded-xl cursor-pointer hover:bg-amber-100 transition text-amber-800 font-bold text-xs">
                                <input type="radio" name="punch_type" value="out" class="text-amber-600 focus:ring-amber-500">
                                <span><i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>PUNCH OUT</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Machine / Terminal ID</label>
                        <select name="device_id" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            <option value="BIO-GATE-01">BIO-GATE-01 (Main Gate Turnstile)</option>
                            <option value="BIO-STAFF-02">BIO-STAFF-02 (Staff Room Optical)</option>
                            <option value="FACE-ADMIN-03">FACE-ADMIN-03 (Admin Block AI Cam)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-fingerprint text-base"></i>
                        <span>Transmit Device Punch & Sync Attendance</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- CSV Device Punch Import Card -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 flex items-center space-x-2">
                            <i class="fa-solid fa-file-csv text-teal-600"></i>
                            <span>Biometric Log CSV Import</span>
                        </h3>
                        <p class="text-xs text-slate-500">Upload raw logs from ZKTeco, eSSL, Realtime devices</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('attendance.biometric.upload_csv') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Machine CSV File</label>
                        <input type="file" name="csv_file" accept=".csv,.txt" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 border border-slate-300 rounded-xl p-1 focus:outline-none">
                        <p class="text-[11px] text-slate-500 mt-1.5">
                            Expected CSV format: <code class="font-mono text-cyan-800 bg-cyan-50 px-1 py-0.5 rounded">device_id, employee_id, timestamp, punch_type</code>
                        </p>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-upload"></i>
                        <span>Upload and Process Logs</span>
                    </button>
                </form>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 font-medium">Need CSV structure?</span>
                    <button type="button" onclick="alert('Sample Format:\nBIO-GATE-01,EMP-101,2026-09-11 08:45:00,in\nBIO-GATE-01,EMP-102,2026-09-11 08:52:10,in\nBIO-STAFF-02,EMP-101,2026-09-11 16:30:00,out')" class="text-cyan-700 font-bold hover:underline">
                        <i class="fa-solid fa-circle-info mr-1"></i>View Sample Format
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-list-check text-slate-500 mr-1.5"></i>Recent Biometric Machine Punch Stream
                </h3>
                <p class="text-xs text-slate-500">Live feed of device punches recorded across all branch terminals</p>
            </div>
            <span class="text-xs text-slate-600 font-bold bg-white px-3 py-1 rounded-lg border border-slate-200">
                Showing Last 30 Logs
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="p-3 border-b border-slate-200">Log ID</th>
                        <th class="p-3 border-b border-slate-200">Timestamp</th>
                        <th class="p-3 border-b border-slate-200">Terminal ID</th>
                        <th class="p-3 border-b border-slate-200">Employee ID</th>
                        <th class="p-3 border-b border-slate-200">Direction</th>
                        <th class="p-3 border-b border-slate-200">Sync Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono text-slate-500">#{{ $log->id }}</td>
                            <td class="p-3 font-medium text-slate-800">
                                {{ $log->punch_timestamp ? \Carbon\Carbon::parse($log->punch_timestamp)->format('d M Y, h:i:s A') : '-' }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $log->device_id }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">
                                {{ $log->employee_id }}
                            </td>
                            <td class="p-3">
                                @if(strtolower($log->punch_type) === 'in')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-arrow-down mr-1"></i>PUNCH IN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-arrow-up mr-1"></i>PUNCH OUT
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($log->processed)
                                    <span class="inline-flex items-center space-x-1 text-emerald-700 font-bold">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>Synced</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 text-amber-600 font-bold">
                                        <i class="fa-solid fa-hourglass-half animate-spin"></i>
                                        <span>Pending Sync</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-fingerprint text-3xl mb-2"></i>
                                <p>No biometric device logs recorded yet. Use the simulator above or upload a CSV file.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
