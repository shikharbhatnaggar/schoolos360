<x-layouts.app title="Staff Attendance">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Staff Attendance & Roster</span>
            <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $currentBranch?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="GET" action="{{ route('attendance.staff.index') }}" class="flex items-center space-x-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Roster Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>
                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition">
                        View Date
                    </button>
                </div>
            </form>

            <div class="flex items-center space-x-2">
                <button type="button" onclick="openStaffQrModal()" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>Staff QR Punch</span>
                </button>
                <a href="{{ route('attendance.biometric.index') }}" class="inline-flex items-center space-x-2 bg-cyan-600 hover:bg-cyan-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
                    <i class="fa-solid fa-fingerprint"></i>
                    <span>Biometric Device Hub</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Attendance Sheet Form -->
    <form method="POST" action="{{ route('attendance.staff.save') }}">
        @csrf
        <input type="hidden" name="date" value="{{ $selectedDate }}">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">
                        Staff Roster &mdash; {{ \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') }}
                    </h3>
                    <p class="text-xs text-slate-500">Manual attendance and punch timings for faculty and administrative staff</p>
                </div>
                <button type="button" onclick="markAllStaff('present')" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs rounded-lg transition border border-emerald-200">
                    <i class="fa-solid fa-check-double mr-1"></i>Mark All Present
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="p-3">Employee</th>
                            <th class="p-3">Designation & Role</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">In Time</th>
                            <th class="p-3 text-center">Out Time</th>
                            <th class="p-3">Method</th>
                            <th class="p-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($staffMembers as $st)
                            @php
                                $record = $attendances->get($st->id);
                                $currentStatus = $record ? $record->status : 'present';
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 font-semibold text-slate-800 flex items-center space-x-2.5">
                                    <img src="{{ $st->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $st->full_name }}</p>
                                        <span class="text-[10px] font-mono text-slate-400">{{ $st->employee_id }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-slate-600">
                                    <p class="font-semibold text-slate-700">{{ $st->designation }}</p>
                                    <span class="text-[10px] text-teal-600 uppercase font-bold">{{ str_replace('_', ' ', $st->role_type) }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center space-x-2 bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-200">
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="present" class="staff-present text-emerald-600" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                            <span>P</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="absent" class="staff-absent text-rose-600" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                            <span>A</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-amber-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="late" class="staff-late text-amber-600" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                            <span>L</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-blue-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="on_leave" class="staff-leave text-blue-600" {{ $currentStatus === 'on_leave' ? 'checked' : '' }}>
                                            <span>LV</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="p-3 text-center font-mono font-semibold text-slate-700">
                                    {{ $record?->punch_in_time ? date('h:i A', strtotime($record->punch_in_time)) : '-' }}
                                </td>
                                <td class="p-3 text-center font-mono font-semibold text-slate-700">
                                    {{ $record?->punch_out_time ? date('h:i A', strtotime($record->punch_out_time)) : '-' }}
                                </td>
                                <td class="p-3">
                                    @if($record && $record->method === 'qr_scan')
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                            QR Scan
                                        </span>
                                    @elseif($record && $record->method === 'biometric')
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">
                                            Biometric
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Manual</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <input type="text" name="remarks[{{ $st->id }}]" value="{{ $record?->remarks }}" placeholder="Remarks" class="w-full text-xs rounded-lg border border-slate-200 px-2 py-1 text-slate-700">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    No active staff members found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($staffMembers) > 0)
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end space-x-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i>Save Staff Attendance
                </button>
            </div>
            @endif
        </div>
    </form>

    <!-- Staff QR Code Modal -->
    <div id="staffQrModal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Staff QR Punch Station</h3>
                        <p class="text-xs text-slate-500">Scan Staff ID Card for IN / OUT punch</p>
                    </div>
                </div>
                <button onclick="closeStaffQrModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div id="staff-qr-reader" class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-64 flex items-center justify-center text-xs text-slate-400">
            </div>

            <div class="mt-3 flex space-x-2">
                <input type="text" id="manualStaffCode" placeholder="Enter Employee ID (e.g. EMP-101)..." class="flex-1 text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <button onclick="submitStaffManualCode()" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-xl">
                    Punch
                </button>
            </div>

            <div id="staffScanResult" class="mt-4"></div>
        </div>
    </div>

    <script>
        function markAllStaff(status) {
            document.querySelectorAll(`.staff-${status}`).forEach(el => el.checked = true);
        }

        let staffQrScanner = null;

        function openStaffQrModal() {
            document.getElementById('staffQrModal').classList.remove('hidden');
            if (!staffQrScanner) {
                staffQrScanner = new Html5Qrcode("staff-qr-reader");
            }
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    staffQrScanner.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: 200 },
                        (decoded) => onStaffScan(decoded),
                        (error) => {}
                    ).catch(err => {
                        console.log("Camera error: ", err);
                    });
                }
            }).catch(err => {
                document.getElementById('staff-qr-reader').innerHTML = `
                    <div class="p-4 text-center text-rose-500 text-xs">
                        Camera unavailable. Use Employee ID input below.
                    </div>`;
            });
        }

        function closeStaffQrModal() {
            if (staffQrScanner && staffQrScanner.isScanning) {
                staffQrScanner.stop().then(() => {
                    document.getElementById('staffQrModal').classList.add('hidden');
                });
            } else {
                document.getElementById('staffQrModal').classList.add('hidden');
            }
        }

        function onStaffScan(code) {
            sendStaffPunch(code);
        }

        function submitStaffManualCode() {
            const val = document.getElementById('manualStaffCode').value.trim();
            if (val) {
                sendStaffPunch(val);
                document.getElementById('manualStaffCode').value = '';
            }
        }

        function sendStaffPunch(code) {
            fetch("{{ route('attendance.staff.qr_scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                const resEl = document.getElementById('staffScanResult');
                if (data.success) {
                    const s = data.staff;
                    resEl.innerHTML = `
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center space-x-3">
                            <img src="${s.avatar}" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-500">
                            <div>
                                <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">${s.action}</span>
                                <h4 class="text-sm font-black text-slate-800 mt-1">${s.name}</h4>
                                <p class="text-xs text-slate-500">${s.employee_id} &bull; ${s.designation}</p>
                            </div>
                        </div>
                    `;
                } else {
                    resEl.innerHTML = `
                        <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center text-xs text-rose-700 font-bold">
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(err => console.error(err));
        }
    </script>
</x-layouts.app>
