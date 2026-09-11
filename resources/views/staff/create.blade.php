<x-layouts.app title="Register Staff Member">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('staff.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Register Faculty & Staff</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-8" x-data="{
        roleType: '{{ old('role_type', 'teacher') }}',
        createAccount: {{ old('create_user_account') ? 'true' : 'false' }}
    }">
        <div class="mb-6 pb-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Faculty & Staff Registration</h3>
            <p class="text-xs text-slate-500">Record employee profile and configure system access</p>
        </div>

        <form action="{{ route('staff.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- 1. Employment Details -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>1. Employment & Role</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Employee ID *</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $suggestedEmpId) }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Role Type *</label>
                        <select name="role_type" x-model="roleType" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="teacher">Teacher</option>
                            <option value="class_teacher">Class Teacher</option>
                            <option value="subject_teacher">Subject Teacher</option>
                            <option value="house_teacher">House Teacher / Master</option>
                            <option value="operator">Operator / Office Staff</option>
                            <option value="admin">School Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Designation *</label>
                        <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Senior Math Teacher" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Joining Date *</label>
                        <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <!-- House Master selection if house_teacher role -->
                    <div x-show="roleType === 'house_teacher'" class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">House Assigned (Master)</label>
                        <select name="house_id" class="w-full px-3 py-2 bg-amber-50 border border-amber-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="">Select House</option>
                            @foreach($houses as $h)
                                <option value="{{ $h->id }}" {{ old('house_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Personal Information -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-user"></i>
                    <span>2. Personal Information</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification') }}" placeholder="e.g. M.Sc, B.Ed"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>
            </div>

            <!-- 3. Contact & Address -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-address-book"></i>
                    <span>3. Contact Information</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="staff@school.com" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 555 234 5678"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Address</label>
                    <textarea name="address" rows="2" placeholder="Full residential street address..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- 4. Portal Account Setup -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="create_user_account" id="create_user_account" value="1" x-model="createAccount" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <label for="create_user_account" class="text-sm font-bold text-slate-800 cursor-pointer">
                        Create Login Account for this Staff Member
                    </label>
                </div>
                <div x-show="createAccount" class="pt-2 pl-6 space-y-2">
                    <p class="text-xs text-slate-500">A portal user account will be created with the staff's email address.</p>
                    <div class="max-w-xs">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Initial Password</label>
                        <input type="text" name="password" value="password" placeholder="Defaults to: password"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-4">
                <a href="{{ route('staff.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Register Staff</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>

