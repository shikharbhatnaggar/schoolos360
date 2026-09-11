<x-layouts.app title="Branch Management">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">Multi-Branch Architecture</span>
            <span class="text-xs bg-slate-200 text-slate-700 font-semibold px-2.5 py-0.5 rounded-full">
                Tenant: {{ $school?->name }}
            </span>
        </div>
    </x-slot>

    <!-- Branch Management Overview -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">School Branches & Campuses</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Each branch operates as an independent school with its own classes, students, staff, attendance, and fee receipt numbering.
            </p>
        </div>
        <button onclick="document.getElementById('newBranchModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
            <i class="fa-solid fa-plus"></i>
            <span>Add New Branch</span>
        </button>
    </div>

    <!-- Branches Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($branches as $b)
        <div class="bg-white rounded-2xl p-6 shadow-sm border {{ ($currentBranch && $currentBranch->id == $b->id) ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-200' }} flex flex-col justify-between hover:shadow-md transition">
            <div>
                <!-- Top Badge & Status -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        @if($b->is_main)
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full border border-amber-200">
                                <i class="fa-solid fa-star text-[9px] mr-1"></i>Main Campus
                            </span>
                        @else
                            <span class="bg-slate-100 text-slate-600 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full">
                                Branch Campus
                            </span>
                        @endif

                        @if($currentBranch && $currentBranch->id == $b->id)
                            <span class="bg-teal-100 text-teal-800 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full">
                                Active Context
                            </span>
                        @endif
                    </div>

                    <!-- Status Toggle -->
                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $b->isActive() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $b->isActive() ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        <span>{{ ucfirst($b->status) }}</span>
                    </span>
                </div>

                <!-- Branch Name & Code -->
                <h3 class="text-lg font-black text-slate-800">{{ $b->name }}</h3>
                <p class="text-xs font-mono text-slate-400 mt-0.5">Code: {{ $b->code }}</p>

                <!-- Details -->
                <div class="mt-4 space-y-1.5 text-xs text-slate-600">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-phone text-slate-400 w-4"></i>
                        <span>{{ $b->phone ?: 'No phone configured' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-envelope text-slate-400 w-4"></i>
                        <span class="truncate">{{ $b->email ?: 'No email configured' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-location-dot text-slate-400 w-4"></i>
                        <span class="truncate">{{ $b->address ?: 'No address specified' }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-receipt text-slate-400 w-4"></i>
                        <span>Receipt Prefix: <strong>{{ $b->fee_receipt_prefix }}</strong> (Next: #{{ $b->fee_receipt_next_no }})</span>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-3 gap-2 my-4 pt-4 border-t border-slate-100 text-center">
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-semibold">Students</span>
                        <span class="text-base font-black text-slate-800">{{ $b->students_count }}</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-semibold">Staff</span>
                        <span class="text-base font-black text-slate-800">{{ $b->staff_count }}</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-400 block font-semibold">Classes</span>
                        <span class="text-base font-black text-slate-800">{{ $b->classes_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                @if($currentBranch && $currentBranch->id == $b->id)
                    <button disabled class="flex-1 bg-teal-50 text-teal-700 font-bold py-2 px-3 rounded-xl text-xs text-center cursor-default border border-teal-200">
                        <i class="fa-solid fa-check mr-1"></i>Current Branch
                    </button>
                @else
                    <form action="{{ route('branches.switch') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $b->id }}">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-3 rounded-xl text-xs text-center transition shadow-xs flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-arrow-right-to-bracket text-[11px]"></i>
                            <span>Switch & Navigate</span>
                        </button>
                    </form>
                @endif

                <!-- Enable / Disable Form -->
                <form action="{{ route('branches.toggle', $b->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl text-xs font-bold transition border {{ $b->isActive() ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}" title="{{ $b->isActive() ? 'Disable Branch' : 'Enable Branch' }}">
                        <i class="fa-solid {{ $b->isActive() ? 'fa-ban' : 'fa-check' }}"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Create New Branch Modal -->
    <div id="newBranchModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-800">Add New School Branch</h3>
                <button onclick="document.getElementById('newBranchModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('branches.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Branch Name *</label>
                    <input type="text" name="name" required placeholder="e.g. North Delhi Campus" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Branch Code *</label>
                        <input type="text" name="code" required placeholder="e.g. north-delhi" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Fee Receipt Prefix *</label>
                        <input type="text" name="fee_receipt_prefix" required value="DPA-ND-" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" placeholder="+91 98765 43210" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="branch@school.com" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Campus Address</label>
                    <textarea name="address" rows="2" placeholder="Street, Sector, City, State..." class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ID Card Orientation</label>
                        <select name="id_card_orientation" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ID Card Theme Color</label>
                        <input type="color" name="id_card_primary_color" value="#0d9488" class="w-full h-10 rounded-xl border border-slate-300 cursor-pointer">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('newBranchModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white shadow">
                        Create Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
