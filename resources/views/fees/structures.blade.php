<x-layouts.app title="Fee Structure Management">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('fees.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Fee Structure Management</span>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 1. Add Fee Head Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                <h4 class="font-bold text-slate-800 mb-1 flex items-center space-x-2">
                    <i class="fa-solid fa-tags text-teal-600"></i>
                    <span>Add Fee Category / Head</span>
                </h4>
                <p class="text-xs text-slate-500 mb-4">Define billing categories (e.g. Tuition, Lab, Sports)</p>

                <form action="{{ route('fees.heads.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Fee Head Name *</label>
                        <input type="text" name="name" placeholder="e.g. Tuition Fee, Sports Fee" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                        <input type="text" name="description" placeholder="Brief note on fee purpose"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                        Save Fee Head
                    </button>
                </form>

                <!-- Existing Fee Heads List -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Existing Fee Heads</h5>
                    <div class="space-y-2">
                        @forelse($feeHeads as $head)
                            <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-xs">
                                <span class="font-bold text-slate-800">{{ $head->name }}</span>
                                <span class="text-slate-400">{{ $head->description ?: '-' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No fee heads created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2. Configure Class Fee Structure -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="mb-4">
                    <h4 class="font-bold text-slate-800 text-base flex items-center space-x-2">
                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                        <span>Configure Class Fee Schedule</span>
                    </h4>
                    <p class="text-xs text-slate-500">Assign fee head amounts to specific classes for the active session</p>
                </div>

                <form action="{{ route('fees.structures.save') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Academic Session</label>
                            <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                @foreach($sessions as $sess)
                                    <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
                                        {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Class</label>
                            <select name="school_class_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Billing Frequency</label>
                            <select name="frequency" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                <option value="monthly">Monthly</option>
                                <option value="term" selected>Per Term</option>
                                <option value="annual">Annual</option>
                                <option value="one_time">One-Time Admission</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fee Heads Amount Inputs -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Fee Head Amounts ($)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($feeHeads as $head)
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <span class="block text-xs font-bold text-slate-800 mb-1">{{ $head->name }}</span>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs font-bold">{{ $currencySymbol }}</span>
                                        <input type="number" step="0.01" min="0" name="rates[{{ $head->id }}]" placeholder="0.00"
                                            class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded text-xs font-medium text-slate-800">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs transition shadow flex items-center space-x-2">
                        <i class="fa-solid fa-save"></i>
                        <span>Save Class Fee Structure</span>
                    </button>
                </form>

                <!-- Current Defined Class Structures Summary -->
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Current Active Fee Structures By Class</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($classes as $cls)
                            @php
                                $clsStructures = $structures->get($cls->id, collect());
                                $clsTotal = $clsStructures->sum('amount');
                            @endphp
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <h6 class="font-bold text-slate-800 text-sm">{{ $cls->name }}</h6>
                                    <span class="text-xs font-extrabold text-teal-600">{{ $currencySymbol }}{{ number_format($clsTotal, 2) }}</span>
                                </div>
                                <div class="space-y-1 text-xs text-slate-500">
                                    @forelse($clsStructures as $struct)
                                        <div class="flex items-center justify-between">
                                            <span>{{ $struct->feeHead?->name }}:</span>
                                            <span class="font-semibold text-slate-700">{{ $currencySymbol }}{{ number_format($struct->amount, 2) }} ({{ $struct->frequency }})</span>
                                        </div>
                                    @empty
                                        <span class="text-slate-400 italic">No fee structure saved yet.</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No classes found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

