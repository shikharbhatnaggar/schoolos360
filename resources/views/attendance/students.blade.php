<x-layouts.app title="Student Attendance">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Student Attendance</span>
            <span class="text-xs bg-teal-100 text-teal-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $currentBranch?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Top Action Bar & Filter Form -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filter by Class, Section, Date -->
            <form method="GET" action="{{ route('attendance.students.index') }}" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Class</label>
                    <select name="class_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Section</label>
                    <select name="section_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $selectedSectionId == $s->id ? 'selected' : '' }}>Section {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="self-end">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition">
                        <i class="fa-solid fa-filter mr-1"></i>Apply Filter
                    </button>
                </div>
            </form>

            <!-- QR Code Scanner Modal Trigger Button -->
            <div class="flex items-center space-x-2">
                <button type="button" onclick="openQrModal()" class="inline-flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-md transition">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                    <span>Launch Live QR Scanner</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Attendance Stats for Selected Class/Date -->
    @php
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $halfDayCount = $attendances->where('status', 'half_day')->count();
        $totalStudentsCount = count($students);
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 text-center shadow-xs">
            <span class="text-xs font-bold text-slate-400 block uppercase">Total Enrolled</span>
            <span class="text-xl font-black text-slate-800">{{ $totalStudentsCount }}</span>
        </div>
        <div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-200 text-center shadow-xs">
            <span class="text-xs font-bold text-emerald-700 block uppercase">Present</span>
            <span class="text-xl font-black text-emerald-700">{{ $presentCount }}</span>
        </div>
        <div class="bg-rose-50 rounded-xl p-3.5 border border-rose-200 text-center shadow-xs">
            <span class="text-xs font-bold text-rose-700 block uppercase">Absent</span>
            <span class="text-xl font-black text-rose-700">{{ $absentCount }}</span>
        </div>
        <div class="bg-amber-50 rounded-xl p-3.5 border border-amber-200 text-center shadow-xs">
            <span class="text-xs font-bold text-amber-700 block uppercase">Late / Half-Day</span>
            <span class="text-xl font-black text-amber-700">{{ $lateCount + $halfDayCount }}</span>
        </div>
    </div>

    <!-- Attendance Sheet Table Form -->
    <form method="POST" action="{{ route('attendance.students.save') }}">
        @csrf
        <input type="hidden" name="date" value="{{ $selectedDate }}">
        <input type="hidden" name="school_class_id" value="{{ $selectedClassId }}">
        <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">
                        Attendance Sheet &mdash; {{ \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') }}
                    </h3>
                    <p class="text-xs text-slate-500">Radio buttons can be marked individually or all at once</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="markAll('present')" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs rounded-lg transition border border-emerald-200">
                        <i class="fa-solid fa-check-double mr-1"></i>Mark All Present
                    </button>
                    <button type="button" onclick="markAll('absent')" class="px-3 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs rounded-lg transition border border-rose-200">
                        Mark All Absent
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="p-3 w-12 text-center">Roll</th>
                            <th class="p-3">Student Name</th>
                            <th class="p-3">Admission No</th>
                            <th class="p-3 text-center">Attendance Status</th>
                            <th class="p-3">Method</th>
                            <th class="p-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $st)
                            @php
                                $record = $attendances->get($st->id);
                                $currentStatus = $record ? $record->status : 'present';
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 font-mono font-bold text-center text-slate-500">
                                    {{ $st->roll_no ?: '-' }}
                                </td>
                                <td class="p-3 font-semibold text-slate-800 flex items-center space-x-2.5">
                                    <img src="{{ $st->avatar_url }}" class="w-7 h-7 rounded-full object-cover border border-slate-200">
                                    <span>{{ $st->full_name }}</span>
                                </td>
                                <td class="p-3 font-mono text-slate-500">
                                    {{ $st->admission_no }}
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center space-x-3 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-emerald-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="present" class="status-present text-emerald-600 focus:ring-emerald-500" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                            <span>P</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-rose-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="absent" class="status-absent text-rose-600 focus:ring-rose-500" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                            <span>A</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-amber-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="late" class="status-late text-amber-600 focus:ring-amber-500" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                            <span>L</span>
                                        </label>
                                        <label class="flex items-center space-x-1 cursor-pointer font-bold text-indigo-700">
                                            <input type="radio" name="attendances[{{ $st->id }}]" value="half_day" class="status-half text-indigo-600 focus:ring-indigo-500" {{ $currentStatus === 'half_day' ? 'checked' : '' }}>
                                            <span>HD</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="p-3">
                                    @if($record && $record->method === 'qr_scan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">
                                            <i class="fa-solid fa-qrcode mr-1"></i>QR Scan
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Manual</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <input type="text" name="remarks[{{ $st->id }}]" value="{{ $record?->remarks }}" placeholder="Optional notes" class="w-full text-xs rounded-lg border border-slate-200 px-2 py-1 text-slate-700 focus:ring-1 focus:ring-teal-500">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    No active students found in this class & section.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($students) > 0)
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end space-x-3">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i>Save Attendance Sheet
                </button>
            </div>
            @endif
        </div>
    </form>

    <!-- QR Code Scan Modal -->
    <div id="qrModal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Student QR Code Scanner</h3>
                        <p class="text-xs text-slate-500">Scan Student ID Card or enter Admission Number</p>
                    </div>
                </div>
                <button onclick="closeQrModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Left: Live Camera Viewfinder -->
                <div>
                    <div id="qr-reader" class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 h-64 flex items-center justify-center text-xs text-slate-400">
                    </div>
                    
                    <!-- Manual input fallback -->
                    <div class="mt-3 flex space-x-2">
                        <input type="text" id="manualQrInput" placeholder="Enter Admission No manually..." class="flex-1 text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <button onclick="submitManualCode()" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-2 rounded-xl">
                            Verify
                        </button>
                    </div>
                </div>

                <!-- Right: Scan Result & Realtime Card -->
                <div class="flex flex-col justify-between border-l border-slate-100 pl-4">
                    <div id="scanResultArea">
                        <div class="text-center p-6 border-2 border-dashed border-slate-200 rounded-xl">
                            <i class="fa-solid fa-id-card text-3xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-bold text-slate-500">Ready to scan student ID</p>
                            <p class="text-[11px] text-slate-400 mt-1">Point camera at ID card QR code or type admission number</p>
                        </div>
                    </div>

                    <!-- Scanned Feed Log -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Recent Scans Today</h4>
                        <div id="recentScanList" class="space-y-1.5 max-h-36 overflow-y-auto text-xs">
                            <p class="text-slate-400 italic text-[11px]">No scans in this session yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAll(status) {
            document.querySelectorAll(`.status-${status}`).forEach(el => el.checked = true);
        }

        let html5QrCode = null;

        function openQrModal() {
            document.getElementById('qrModal').classList.remove('hidden');
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: 200 },
                        (decodedText) => onScanSuccess(decodedText),
                        (error) => {}
                    ).catch(err => {
                        console.log("Camera start error: ", err);
                    });
                }
            }).catch(err => {
                document.getElementById('qr-reader').innerHTML = `
                    <div class="p-4 text-center text-rose-500">
                        <i class="fa-solid fa-camera-slash text-2xl mb-1"></i>
                        <p class="text-xs">Camera access unavailable. Please use manual Admission No entry below.</p>
                    </div>`;
            });
        }

        function closeQrModal() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    document.getElementById('qrModal').classList.add('hidden');
                });
            } else {
                document.getElementById('qrModal').classList.add('hidden');
            }
        }

        function playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            } catch (e) {}
        }

        function onScanSuccess(code) {
            playBeep();
            sendAttendanceRequest(code);
        }

        function submitManualCode() {
            const val = document.getElementById('manualQrInput').value.trim();
            if (val) {
                sendAttendanceRequest(val);
                document.getElementById('manualQrInput').value = '';
            }
        }

        function sendAttendanceRequest(code) {
            fetch("{{ route('attendance.students.qr_scan') }}", {
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
                if (data.success) {
                    const st = data.student;
                    document.getElementById('scanResultArea').innerHTML = `
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center space-x-3 shadow-sm animate-fade-in">
                            <img src="${st.avatar}" class="w-14 h-14 rounded-full object-cover border-2 border-emerald-500 shrink-0">
                            <div>
                                <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Marked Present</span>
                                <h4 class="text-sm font-black text-slate-800 mt-1">${st.name}</h4>
                                <p class="text-xs text-slate-600">${st.admission_no} &bull; ${st.class}</p>
                                <p class="text-[11px] text-emerald-700 font-medium mt-0.5"><i class="fa-solid fa-clock text-[10px] mr-1"></i>${st.time}</p>
                            </div>
                        </div>
                    `;

                    // Add to session scan list
                    const list = document.getElementById('recentScanList');
                    if (list.querySelector('p.italic')) {
                        list.innerHTML = '';
                    }
                    const item = document.createElement('div');
                    item.className = "flex items-center justify-between p-1.5 bg-slate-50 rounded-lg border border-slate-100";
                    item.innerHTML = `
                        <span class="font-bold text-slate-800">${st.name} (${st.admission_no})</span>
                        <span class="text-emerald-600 font-bold text-[10px]">${st.time}</span>
                    `;
                    list.prepend(item);
                } else {
                    document.getElementById('scanResultArea').innerHTML = `
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-center">
                            <i class="fa-solid fa-circle-exclamation text-2xl text-rose-500 mb-1"></i>
                            <h4 class="text-xs font-bold text-rose-800">Scan Failed</h4>
                            <p class="text-[11px] text-rose-600 mt-0.5">${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error(err);
            });
        }
    </script>
</x-layouts.app>
