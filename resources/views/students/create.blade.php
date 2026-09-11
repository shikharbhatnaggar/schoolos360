<x-layouts.app title="Student Registration & Admission">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('students.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Student Registration & Admission</span>
        </div>
    </x-slot>

    @php
        // Prepare classes with sections map for interactive Alpine filtering
        $classSectionMap = [];
        foreach($classes as $c) {
            $classSectionMap[$c->id] = $c->sections->map(function($s) {
                return ['id' => $s->id, 'name' => $s->name];
            });
        }
    @endphp

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-8"
        x-data="{
            classMap: {{ json_encode($classSectionMap) }},
            selectedClass: '{{ old('school_class_id', $classes->first()?->id) }}',
            get currentSections() {
                return this.classMap[this.selectedClass] || [];
            }
        }">
        
        <div class="mb-6 pb-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">New Student Admission Form</h3>
                <p class="text-xs text-slate-500">Enter personal, academic, and guardian information</p>
            </div>
            <span class="px-3 py-1 bg-teal-50 text-teal-700 border border-teal-200 rounded-full text-xs font-semibold">
                Admission Year: {{ $currentSession ? $currentSession->name : 'Active' }}
            </span>
        </div>

        <form action="{{ route('students.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- 1. Academic & Admission Details -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50/70 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-school"></i>
                    <span>1. Academic Enrollment</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Admission No *</label>
                        <input type="text" name="admission_no" value="{{ old('admission_no', $nextAdmissionNo) }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Roll No</label>
                        <input type="text" name="roll_no" value="{{ old('roll_no') }}" placeholder="e.g. 101"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Academic Session *</label>
                        <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->id }}" {{ old('academic_session_id', $currentSession?->id) == $sess->id ? 'selected' : '' }}>
                                    {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Admission Date *</label>
                        <input type="date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Class *</label>
                        <select name="school_class_id" x-model="selectedClass" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ old('school_class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Section *</label>
                        <select name="section_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <template x-for="sec in currentSections" :key="sec.id">
                                <option :value="sec.id" x-text="'Section ' + sec.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">House</label>
                        <select name="house_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="">Select House (Optional)</option>
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
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Personal Information -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50/70 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-user"></i>
                    <span>2. Student Personal Information</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Rahul" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Sharma" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Date of Birth *</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Blood Group</label>
                        <input type="text" name="blood_group" value="{{ old('blood_group') }}" placeholder="e.g. O+, B+"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>
            </div>

            <!-- 3. Guardian & Contact Information -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50/70 px-3 py-1.5 rounded mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-users"></i>
                    <span>3. Guardian & Contact Details</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Parent / Guardian Name *</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="e.g. Rajesh Sharma" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Phone Number *</label>
                        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="e.g. +1 555 123 4567" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Guardian Email</label>
                        <input type="email" name="parent_email" value="{{ old('parent_email') }}" placeholder="e.g. parent@example.com"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Residential Address</label>
                    <textarea name="address" rows="2" placeholder="Full residential street address..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Form Submit Actions -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-4">
                <a href="{{ route('students.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Complete Admission</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>

