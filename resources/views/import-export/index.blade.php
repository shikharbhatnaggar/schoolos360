<x-layouts.app title="Bulk Import & Export">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Bulk Data Hub & Migration</span>
            <span class="text-xs bg-teal-100 text-teal-800 font-semibold px-2.5 py-0.5 rounded-full">
                {{ $currentBranch?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Overview Banner -->
    <div class="bg-gradient-to-r from-teal-700 via-teal-800 to-slate-900 rounded-2xl p-6 text-white mb-8 shadow-sm">
        <div class="max-w-3xl">
            <h2 class="text-lg font-bold mb-1 flex items-center space-x-2">
                <i class="fa-solid fa-file-excel text-teal-300"></i>
                <span>Fast CSV Data Synchronization</span>
            </h2>
            <p class="text-xs text-slate-200 leading-relaxed">
                Seamlessly export existing branch records or batch onboard new admissions and faculty rosters via standard CSV templates. Missing classes or sections are automatically provisioned during student import.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- 1. Student Bulk Management Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Student Directory Data</h3>
                            <p class="text-xs text-slate-500">Export student rolls or bulk import new admissions</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Export Section -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center space-x-1.5">
                            <i class="fa-solid fa-file-export text-teal-600"></i>
                            <span>Export Current Students</span>
                        </h4>

                        <form method="GET" action="{{ route('export.students') }}" class="flex items-center space-x-3">
                            <select name="class_id" class="flex-1 text-xs rounded-xl border border-slate-300 px-3 py-2 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option value="">-- All Classes & Sections --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                                <i class="fa-solid fa-download"></i>
                                <span>Export CSV</span>
                            </button>
                        </form>
                    </div>

                    <div class="border-t border-slate-100 pt-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center space-x-1.5">
                                <i class="fa-solid fa-file-import text-teal-600"></i>
                                <span>Bulk Import Students</span>
                            </h4>
                            <a href="{{ route('export.students.sample') }}" class="text-xs text-teal-700 font-bold hover:underline flex items-center space-x-1">
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                <span>Sample Template</span>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('import.students') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <input type="file" name="csv_file" accept=".csv,.txt" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-300 rounded-xl p-1.5 focus:outline-none">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-upload"></i>
                                <span>Upload & Import Student Roster</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500">
                <strong>Columns:</strong> Admission No, Roll No, First Name, Last Name, Gender, DOB, Blood Group, Class, Section, House, Parent Name, Parent Phone, Parent Email, Address
            </div>
        </div>

        <!-- 2. Staff Bulk Management Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Faculty & Staff Data</h3>
                            <p class="text-xs text-slate-500">Export employee directory or bulk import teachers</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Export Section -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center space-x-1.5">
                            <i class="fa-solid fa-file-export text-cyan-600"></i>
                            <span>Export All Staff Records</span>
                        </h4>

                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-xs font-medium text-slate-600">Download full faculty & administrative roster</span>
                            <a href="{{ route('export.staff') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center space-x-1.5">
                                <i class="fa-solid fa-download"></i>
                                <span>Export CSV</span>
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center space-x-1.5">
                                <i class="fa-solid fa-file-import text-cyan-600"></i>
                                <span>Bulk Import Staff</span>
                            </h4>
                            <a href="{{ route('export.staff.sample') }}" class="text-xs text-cyan-700 font-bold hover:underline flex items-center space-x-1">
                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                <span>Sample Template</span>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('import.staff') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <input type="file" name="csv_file" accept=".csv,.txt" required class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 border border-slate-300 rounded-xl p-1.5 focus:outline-none">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-upload"></i>
                                <span>Upload & Import Staff Directory</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500">
                <strong>Columns:</strong> Employee ID, First Name, Last Name, Email, Phone, Gender, Blood Group, Role Type, Designation, Qualification, Joining Date, Address
            </div>
        </div>
    </div>
</x-layouts.app>
