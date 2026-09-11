<x-layouts.app title="ID Card Studio">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">ID Card Studio</span>
            <span class="text-xs bg-teal-100 text-teal-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $currentBranch?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Header & Controls -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 mb-6">
        <form method="GET" action="{{ route('idcards.index') }}" id="idCardForm" class="space-y-4">
            <!-- Mode Switcher -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div class="inline-flex bg-slate-100 p-1 rounded-xl">
                    <button type="submit" name="type" value="students" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $type === 'students' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-graduation-cap mr-1.5"></i>Student ID Cards
                    </button>
                    <button type="submit" name="type" value="staff" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $type === 'staff' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-tie mr-1.5"></i>Staff ID Cards
                    </button>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="{{ route('idcards.print', request()->all()) }}" target="_blank" class="inline-flex items-center space-x-2 bg-slate-900 hover:bg-black text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
                        <i class="fa-solid fa-print"></i>
                        <span>Print ID Card Sheet (A4)</span>
                    </a>
                </div>
            </div>

            <!-- Customizer Options Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                @if($type === 'students')
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Class</label>
                        <select name="class_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">-- All Classes --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Section</label>
                        <select name="section_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">-- All Sections --</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->id }}" {{ $selectedSectionId == $s->id ? 'selected' : '' }}>
                                    Section {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Staff Filter</label>
                        <div class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            All Active Faculty & Staff Members ({{ $staffMembers->count() }})
                        </div>
                    </div>
                @endif

                <!-- Orientation -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Card Orientation</label>
                    <select name="orientation" onchange="this.form.submit()" class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <option value="portrait" {{ $orientation === 'portrait' ? 'selected' : '' }}>Portrait (Standard)</option>
                        <option value="landscape" {{ $orientation === 'landscape' ? 'selected' : '' }}>Landscape (Horizontal)</option>
                    </select>
                </div>

                <!-- Theme Color -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Theme Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="primary_color" value="{{ $primaryColor }}" onchange="this.form.submit()" class="h-9 w-12 rounded-lg border border-slate-300 cursor-pointer p-0.5">
                        <span class="text-xs font-mono text-slate-600">{{ $primaryColor }}</span>
                    </div>
                </div>

                <!-- Feature Toggles -->
                <div class="flex flex-col justify-end space-y-1">
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="show_blood_group" value="1" {{ $showBloodGroup ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-teal-600 focus:ring-teal-500 text-xs">
                        <span class="text-xs font-semibold text-slate-700">Blood Group</span>
                    </label>
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="show_emergency" value="1" {{ $showEmergency ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-teal-600 focus:ring-teal-500 text-xs">
                        <span class="text-xs font-semibold text-slate-700">Emergency Contact</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Cards Grid -->
    @php
        $cardItems = $type === 'students' ? $students : $staffMembers;
    @endphp

    @if($cardItems->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
            <i class="fa-solid fa-id-badge text-4xl text-slate-300 mb-3"></i>
            <h4 class="text-base font-bold text-slate-700">No {{ $type === 'students' ? 'students' : 'staff' }} found</h4>
            <p class="text-xs text-slate-500">Adjust your class or section filters above.</p>
        </div>
    @else
        <div class="mb-4 flex items-center justify-between text-xs text-slate-500">
            <span>Showing <strong>{{ $cardItems->count() }}</strong> {{ $type }} card previews</span>
            <span class="italic"><i class="fa-solid fa-circle-info mr-1"></i>QR Codes are functional & readable by any barcode/attendance scanner</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cardItems as $item)
                @php
                    $isStudent = $type === 'students';
                    $idCode = $isStudent ? $item->admission_no : $item->employee_id;
                    $qrText = $isStudent ? "admission:{$item->admission_no}" : "employee:{$item->employee_id}";
                    $cardId = 'qr_' . ($isStudent ? 'stud_' : 'staff_') . $item->id;
                @endphp

                @if($orientation === 'portrait')
                    <!-- PORTRAIT CARD DESIGN -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition flex flex-col justify-between max-w-sm mx-auto w-full" style="border-top: 5px solid {{ $primaryColor }};">
                        <!-- Top Header -->
                        <div class="p-3 text-center text-white relative" style="background-color: {{ $primaryColor }};">
                            <h4 class="font-extrabold text-xs uppercase tracking-wider line-clamp-1">{{ $currentSchool?->name }}</h4>
                            <p class="text-[10px] text-white/80 font-medium">{{ $currentBranch?->name }}</p>
                            <span class="text-[9px] bg-black/20 px-2 py-0.5 rounded-full font-mono mt-0.5 inline-block">
                                {{ $activeSession?->name ?? '2026-2027' }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 text-center">
                            <!-- Photo -->
                            <div class="relative inline-block mb-3">
                                <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                                @if($item->house)
                                    <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full border-2 border-white shadow flex items-center justify-center text-[9px] text-white font-bold" style="background-color: {{ $item->house->color ?? '#64748b' }};" title="{{ $item->house->name }}">
                                        {{ substr($item->house->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-sm font-bold text-slate-800">{{ $item->full_name }}</h3>
                            <p class="text-xs font-semibold text-teal-700 mt-0.5">
                                @if($isStudent)
                                    {{ $item->schoolClass?->name }} ({{ $item->section?->name }}) &bull; Roll #{{ $item->roll_no ?: '-' }}
                                @else
                                    {{ $item->designation ?? ucwords(str_replace('_', ' ', $item->role_type)) }}
                                @endif
                            </p>
                            <p class="text-[11px] font-mono text-slate-500 mt-0.5">ID: {{ $idCode }}</p>

                            <!-- QR Code & Details -->
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="text-left space-y-1 text-[11px]">
                                    @if($showBloodGroup)
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold">Blood:</span>
                                            <span class="font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">
                                                {{ $item->blood_group ?: 'N/A' }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($showEmergency)
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold">Contact:</span>
                                            <span class="font-medium text-slate-700">
                                                {{ $isStudent ? ($item->parent_phone ?: '-') : ($item->phone ?: '-') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($item->house)
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold">House:</span>
                                            <span class="font-medium text-slate-700">{{ $item->house->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- QR Canvas -->
                                <div class="p-1 bg-white border border-slate-200 rounded-lg shadow-inner">
                                    <div id="{{ $cardId }}" class="w-16 h-16" data-qr="{{ $qrText }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-slate-50 p-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                            <span>Auth. Signatory</span>
                            <a href="{{ route('idcards.print', array_merge(request()->all(), [$isStudent ? 'student_id' : 'staff_id' => $item->id])) }}" target="_blank" class="text-teal-700 font-bold hover:underline">
                                <i class="fa-solid fa-print mr-1"></i>Print Card
                            </a>
                        </div>
                    </div>
                @else
                    <!-- LANDSCAPE CARD DESIGN -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition flex flex-col justify-between w-full" style="border-left: 6px solid {{ $primaryColor }};">
                        <!-- Top Stripe -->
                        <div class="px-4 py-2 text-white flex items-center justify-between" style="background-color: {{ $primaryColor }};">
                            <div>
                                <h4 class="font-extrabold text-xs uppercase tracking-wider">{{ $currentSchool?->name }}</h4>
                                <p class="text-[10px] text-white/80 font-medium">{{ $currentBranch?->name }}</p>
                            </div>
                            <span class="text-[9px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
                                {{ $activeSession?->name ?? '2026-2027' }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-3 text-center">
                                <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 shadow mx-auto">
                                <span class="text-[10px] font-mono text-slate-500 mt-1 block">{{ $idCode }}</span>
                            </div>

                            <div class="col-span-6 space-y-0.5">
                                <h3 class="text-sm font-bold text-slate-800">{{ $item->full_name }}</h3>
                                <p class="text-xs font-semibold text-teal-700">
                                    @if($isStudent)
                                        {{ $item->schoolClass?->name }} ({{ $item->section?->name }}) &bull; Roll: {{ $item->roll_no ?: '-' }}
                                    @else
                                        {{ $item->designation ?? ucwords(str_replace('_', ' ', $item->role_type)) }}
                                    @endif
                                </p>

                                <div class="text-[11px] pt-1 space-y-0.5">
                                    @if($showBloodGroup)
                                        <p class="text-slate-600">
                                            <span class="text-slate-400 font-bold">Blood Group:</span>
                                            <span class="font-bold text-red-600">{{ $item->blood_group ?: 'N/A' }}</span>
                                        </p>
                                    @endif
                                    @if($showEmergency)
                                        <p class="text-slate-600 truncate">
                                            <span class="text-slate-400 font-bold">Contact:</span>
                                            {{ $isStudent ? ($item->parent_phone ?: '-') : ($item->phone ?: '-') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-span-3 flex justify-end">
                                <div class="p-1 bg-white border border-slate-200 rounded-lg shadow-inner">
                                    <div id="{{ $cardId }}" class="w-14 h-14" data-qr="{{ $qrText }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-slate-50 px-4 py-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500">
                            <span>Principal / Authorized Signature</span>
                            <a href="{{ route('idcards.print', array_merge(request()->all(), [$isStudent ? 'student_id' : 'staff_id' => $item->id])) }}" target="_blank" class="text-teal-700 font-bold hover:underline">
                                <i class="fa-solid fa-print mr-1"></i>Print
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Render QR codes
            document.querySelectorAll('[data-qr]').forEach(function(el) {
                const text = el.getAttribute('data-qr');
                const size = el.clientWidth || 64;
                new QRCode(el, {
                    text: text,
                    width: size,
                    height: size,
                    colorDark: "#1e293b",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
        });
    </script>
</x-layouts.app>
