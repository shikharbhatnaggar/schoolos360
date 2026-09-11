<x-layouts.app title="School & Branch Settings">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold text-slate-800">System & Branch Settings</span>
            <span class="text-xs bg-slate-100 text-slate-700 font-semibold px-2.5 py-0.5 rounded-full border border-slate-200">
                Tenant: {{ $school->name }} &bull; {{ $branch?->name }}
            </span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Navigation Sidebar / Overview -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-teal-50 border-2 border-teal-200 flex items-center justify-center text-teal-700 text-3xl font-black mb-3">
                    <i class="fa-solid fa-school"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-800">{{ $school->name }}</h3>
                <p class="text-xs font-mono text-slate-500">Tenant Code: {{ $school->code }}</p>
                <div class="mt-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $school->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                    <span class="w-2 h-2 rounded-full {{ $school->status === 'active' ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                    Tenant {{ ucfirst($school->status) }}
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100 text-left space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Active Branch:</span>
                        <span class="font-bold text-slate-800">{{ $branch?->name ?? 'Default Branch' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Default Currency:</span>
                        <span class="font-bold text-teal-700">{{ $school->currency_symbol }} {{ $school->currency }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Receipt Prefix:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $branch?->fee_receipt_prefix ?? 'REC-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Branch Quick Switcher Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-sm">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Switch Active Branch</h4>
                <p class="text-xs text-slate-300 mb-4">Each branch operates with independent numbering, fees, and staff rosters.</p>
                <a href="{{ route('branches.index') }}" class="w-full inline-flex items-center justify-center space-x-2 py-2.5 px-4 bg-teal-500 hover:bg-teal-600 text-white text-xs font-bold rounded-xl shadow transition">
                    <i class="fa-solid fa-code-branch"></i>
                    <span>Manage All Branches</span>
                </a>
            </div>
        </div>

        <!-- Right: Forms -->
        <div class="lg:col-span-8 space-y-8">
            <!-- 1. School Tenant Configuration -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
                            <i class="fa-solid fa-building-columns text-teal-600"></i>
                            <span>School Tenant Configuration</span>
                        </h3>
                        <p class="text-xs text-slate-500">Global settings for the parent educational institution</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.school') }}" class="p-6 space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">School Legal Name</label>
                            <input type="text" name="name" value="{{ old('name', $school->name) }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">School Code / Slug</label>
                            <input type="text" name="code" value="{{ old('code', $school->code) }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Currency Code</label>
                            <input type="text" name="currency" value="{{ old('currency', $school->currency ?? 'INR') }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Currency Symbol</label>
                            <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $school->currency_symbol ?? '₹') }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contact Email</label>
                            <input type="email" name="email" value="{{ old('email', $school->email) }}" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campus Headquarter Address</label>
                            <textarea name="address" rows="2" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('address', $school->address) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tenant Status</label>
                            <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option value="active" {{ $school->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="disabled" {{ $school->status === 'disabled' ? 'selected' : '' }}>Disabled / Suspended</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Logo URL</label>
                            <input type="text" name="logo_url" value="{{ old('logo_url', $school->logo_url) }}" placeholder="https://example.com/logo.png" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow transition">
                            Save School Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Active Branch Configuration -->
            @if($branch)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 flex items-center space-x-2">
                                <i class="fa-solid fa-code-branch text-teal-600"></i>
                                <span>Branch Configuration &mdash; {{ $branch->name }}</span>
                            </h3>
                            <p class="text-xs text-slate-500">Settings specific to this branch location, fee receipt prefix, and ID cards</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.branch') }}" class="p-6 space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Name</label>
                                <input type="text" name="name" value="{{ old('name', $branch->name) }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Code</label>
                                <input type="text" name="code" value="{{ old('code', $branch->code) }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Email</label>
                                <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Address</label>
                                <textarea name="address" rows="2" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('address', $branch->address) }}</textarea>
                            </div>

                            <!-- Fee Receipt Customization -->
                            <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-700 uppercase mb-3 flex items-center space-x-1.5">
                                    <i class="fa-solid fa-receipt text-teal-600"></i>
                                    <span>Branch Fee Receipt Numbering</span>
                                </h4>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Receipt Prefix</label>
                                <input type="text" name="fee_receipt_prefix" value="{{ old('fee_receipt_prefix', $branch->fee_receipt_prefix ?? 'REC-') }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <p class="text-[11px] text-slate-500 mt-1">E.g., <code class="bg-slate-100 px-1 py-0.5 rounded">DPA-DEL-2026/</code></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Next Receipt Sequence #</label>
                                <input type="number" name="fee_receipt_next_no" value="{{ old('fee_receipt_next_no', $branch->fee_receipt_next_no ?? 1001) }}" min="1" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-mono font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <p class="text-[11px] text-slate-500 mt-1">Auto-increments with each paid invoice receipt</p>
                            </div>

                            <!-- ID Card Preferences -->
                            <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-700 uppercase mb-3 flex items-center space-x-1.5">
                                    <i class="fa-solid fa-id-badge text-teal-600"></i>
                                    <span>Branch ID Card Defaults</span>
                                </h4>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">ID Card Title</label>
                                <input type="text" name="id_card_title" value="{{ old('id_card_title', $branch->id_card_title ?? 'Identity Card') }}" required class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-medium text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Default Orientation</label>
                                <select name="id_card_orientation" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                    <option value="portrait" {{ ($branch->id_card_orientation ?? 'portrait') === 'portrait' ? 'selected' : '' }}>Portrait</option>
                                    <option value="landscape" {{ ($branch->id_card_orientation ?? 'portrait') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Default Theme Color</label>
                                <div class="flex items-center space-x-2">
                                    <input type="color" name="id_card_primary_color" value="{{ old('id_card_primary_color', $branch->id_card_primary_color ?? '#0d9488') }}" class="h-10 w-14 rounded-lg border border-slate-300 cursor-pointer p-0.5">
                                    <span class="text-xs font-mono font-bold text-slate-700">{{ $branch->id_card_primary_color ?? '#0d9488' }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Operating Status</label>
                                <select name="status" class="w-full text-xs rounded-xl border border-slate-300 px-3.5 py-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                    <option value="active" {{ $branch->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="disabled" {{ $branch->status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2 flex flex-wrap gap-6 pt-2">
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="id_card_show_blood_group" value="1" {{ ($branch->id_card_show_blood_group ?? true) ? 'checked' : '' }} class="rounded text-teal-600 focus:ring-teal-500">
                                    <span class="text-xs font-semibold text-slate-700">Display Blood Group on Card</span>
                                </label>
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="id_card_show_emergency_contact" value="1" {{ ($branch->id_card_show_emergency_contact ?? true) ? 'checked' : '' }} class="rounded text-teal-600 focus:ring-teal-500">
                                    <span class="text-xs font-semibold text-slate-700">Display Emergency Contact</span>
                                </label>
                                <label class="inline-flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="id_card_show_address" value="1" {{ ($branch->id_card_show_address ?? false) ? 'checked' : '' }} class="rounded text-teal-600 focus:ring-teal-500">
                                    <span class="text-xs font-semibold text-slate-700">Display Residential Address</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow transition">
                                Save Branch Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
