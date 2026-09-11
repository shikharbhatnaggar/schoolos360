<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print ID Cards &mdash; {{ $currentSchool?->name }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-after: always;
            }
            .id-card-portrait {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .id-card-landscape {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased p-6">

    <!-- Non-printable Top Control Toolbar -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">ID Card Sheet Print Preview</h1>
            <p class="text-xs text-slate-500">Ready to print {{ $items->count() }} {{ $type }} cards &bull; Recommended paper: A4 Cardstock</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Document</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition">
                Close
            </button>
        </div>
    </div>

    <!-- Cards Container -->
    <div class="max-w-5xl mx-auto">
        @if($orientation === 'portrait')
            <!-- Portrait 3-Column Grid for A4 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($items as $item)
                    @php
                        $isStudent = $type === 'students';
                        $idCode = $isStudent ? $item->admission_no : $item->employee_id;
                        $qrText = $isStudent ? "admission:{$item->admission_no}" : "employee:{$item->employee_id}";
                        $cardId = 'qr_print_' . ($isStudent ? 'stud_' : 'staff_') . $item->id;
                    @endphp

                    <div class="id-card-portrait bg-white rounded-2xl border-2 border-dashed border-slate-300 overflow-hidden shadow-sm flex flex-col justify-between" style="border-top: 5px solid {{ $primaryColor }}; width: 100%; max-width: 320px; margin: 0 auto;">
                        <!-- Header -->
                        <div class="p-3 text-center text-white" style="background-color: {{ $primaryColor }};">
                            <h3 class="font-black text-xs uppercase tracking-wider line-clamp-1">{{ $currentSchool?->name }}</h3>
                            <p class="text-[10px] text-white/90 font-medium">{{ $currentBranch?->name }}</p>
                            <span class="text-[9px] bg-black/20 px-2 py-0.5 rounded-full font-mono mt-0.5 inline-block">
                                Academic Session: {{ $activeSession?->name ?? '2026-2027' }}
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-4 text-center">
                            <!-- Photo -->
                            <div class="relative inline-block mb-2">
                                <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200 mx-auto">
                                @if($item->house)
                                    <span class="absolute bottom-0 right-0 w-5 h-5 rounded-full border border-white shadow flex items-center justify-center text-[9px] text-white font-bold" style="background-color: {{ $item->house->color ?? '#64748b' }};">
                                        {{ substr($item->house->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-sm font-extrabold text-slate-900">{{ $item->full_name }}</h4>
                            <p class="text-xs font-bold text-teal-700">
                                @if($isStudent)
                                    {{ $item->schoolClass?->name }} ({{ $item->section?->name }})
                                @else
                                    {{ $item->designation ?? ucwords(str_replace('_', ' ', $item->role_type)) }}
                                @endif
                            </p>
                            <p class="text-[11px] font-mono text-slate-600 mt-0.5 font-semibold">ID: {{ $idCode }}</p>

                            <!-- Details & QR -->
                            <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-left">
                                <div class="text-[10px] space-y-1">
                                    @if($isStudent && $item->roll_no)
                                        <div>
                                            <span class="text-slate-400 uppercase font-bold">Roll No:</span>
                                            <span class="font-bold text-slate-800">{{ $item->roll_no }}</span>
                                        </div>
                                    @endif
                                    @if($showBloodGroup)
                                        <div>
                                            <span class="text-slate-400 uppercase font-bold">Blood:</span>
                                            <span class="font-black text-red-600">{{ $item->blood_group ?: 'N/A' }}</span>
                                        </div>
                                    @endif
                                    @if($showEmergency)
                                        <div>
                                            <span class="text-slate-400 uppercase font-bold">Phone:</span>
                                            <span class="font-semibold text-slate-800">
                                                {{ $isStudent ? ($item->parent_phone ?: '-') : ($item->phone ?: '-') }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($item->house)
                                        <div>
                                            <span class="text-slate-400 uppercase font-bold">House:</span>
                                            <span class="font-semibold text-slate-800">{{ $item->house->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-1 bg-white border border-slate-200 rounded">
                                    <div id="{{ $cardId }}" class="w-14 h-14" data-qr="{{ $qrText }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-3 py-2 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-500">
                            <span>Valid Till: Mar 2027</span>
                            <span class="font-bold uppercase tracking-wider text-slate-700">Principal Signature</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Landscape 2-Column Grid for A4 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($items as $item)
                    @php
                        $isStudent = $type === 'students';
                        $idCode = $isStudent ? $item->admission_no : $item->employee_id;
                        $qrText = $isStudent ? "admission:{$item->admission_no}" : "employee:{$item->employee_id}";
                        $cardId = 'qr_print_' . ($isStudent ? 'stud_' : 'staff_') . $item->id;
                    @endphp

                    <div class="id-card-landscape bg-white rounded-2xl border-2 border-dashed border-slate-300 overflow-hidden shadow-sm flex flex-col justify-between" style="border-left: 6px solid {{ $primaryColor }};">
                        <!-- Top Header -->
                        <div class="px-4 py-2 text-white flex items-center justify-between" style="background-color: {{ $primaryColor }};">
                            <div>
                                <h3 class="font-black text-xs uppercase tracking-wider">{{ $currentSchool?->name }}</h3>
                                <p class="text-[10px] text-white/90 font-medium">{{ $currentBranch?->name }}</p>
                            </div>
                            <span class="text-[9px] bg-black/20 px-2 py-0.5 rounded-full font-mono">
                                {{ $activeSession?->name ?? '2026-2027' }}
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-4 grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-3 text-center">
                                <img src="{{ $item->avatar_url }}" alt="{{ $item->full_name }}" class="w-16 h-16 rounded-full object-cover border border-slate-200 shadow mx-auto">
                                <span class="text-[10px] font-mono font-bold text-slate-700 mt-1 block">{{ $idCode }}</span>
                            </div>

                            <div class="col-span-6 space-y-0.5 text-left">
                                <h4 class="text-sm font-extrabold text-slate-900">{{ $item->full_name }}</h4>
                                <p class="text-xs font-bold text-teal-700">
                                    @if($isStudent)
                                        {{ $item->schoolClass?->name }} ({{ $item->section?->name }}) &bull; Roll: {{ $item->roll_no ?: '-' }}
                                    @else
                                        {{ $item->designation ?? ucwords(str_replace('_', ' ', $item->role_type)) }}
                                    @endif
                                </p>

                                <div class="text-[10px] pt-1 space-y-0.5 text-slate-600">
                                    @if($showBloodGroup)
                                        <p>
                                            <span class="font-bold text-slate-400">Blood:</span>
                                            <span class="font-black text-red-600">{{ $item->blood_group ?: 'N/A' }}</span>
                                        </p>
                                    @endif
                                    @if($showEmergency)
                                        <p class="truncate">
                                            <span class="font-bold text-slate-400">Contact:</span>
                                            <span class="font-semibold text-slate-800">
                                                {{ $isStudent ? ($item->parent_phone ?: '-') : ($item->phone ?: '-') }}
                                            </span>
                                        </p>
                                    @endif
                                    @if($item->house)
                                        <p>
                                            <span class="font-bold text-slate-400">House:</span>
                                            <span class="font-semibold text-slate-800">{{ $item->house->name }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-span-3 flex justify-end">
                                <div class="p-1 bg-white border border-slate-200 rounded">
                                    <div id="{{ $cardId }}" class="w-14 h-14" data-qr="{{ $qrText }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-4 py-2 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-500">
                            <span>Valid Academic Session 2026-27</span>
                            <span class="font-bold uppercase tracking-wider text-slate-700">Principal Signature</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-qr]').forEach(function(el) {
                const text = el.getAttribute('data-qr');
                new QRCode(el, {
                    text: text,
                    width: 56,
                    height: 56,
                    colorDark: "#1e293b",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
        });
    </script>
</body>
</html>
