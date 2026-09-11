<x-layouts.app title="Classes, Sections, Houses & Subjects">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span>Academic Structure Setup</span>
        </div>
    </x-slot>

    <div class="space-y-8" x-data="{ tab: 'classes' }">
        <!-- Tab Navigation -->
        <div class="flex space-x-2 border-b border-slate-200">
            <button @click="tab = 'classes'" :class="tab === 'classes' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Classes & Sections</span>
            </button>
            <button @click="tab = 'houses'" :class="tab === 'houses' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-flag"></i>
                <span>School Houses</span>
            </button>
            <button @click="tab = 'subjects'" :class="tab === 'subjects' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-book"></i>
                <span>Subjects</span>
            </button>
        </div>

        <!-- 1. Classes & Sections Tab -->
        <div x-show="tab === 'classes'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Create Class Form -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                    <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-plus-circle text-teal-600"></i>
                        <span>Add New Class</span>
                    </h4>
                    <form action="{{ route('academics.classes.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Class Name</label>
                            <input type="text" name="name" placeholder="e.g. Grade 1" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Numeric Level</label>
                            <input type="number" name="numeric_level" min="1" max="20" placeholder="e.g. 1" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                            Save Class
                        </button>
                    </form>

                    <!-- Add Section Form -->
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                            <i class="fa-solid fa-layer-group text-indigo-600"></i>
                            <span>Add Section to Class</span>
                        </h4>
                        <form action="{{ route('academics.sections.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Class</label>
                                <select name="school_class_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                                    <option value="">Choose Class</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Section Name</label>
                                <input type="text" name="name" placeholder="e.g. Section A" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Capacity</label>
                                <input type="number" name="capacity" value="40" min="1" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm transition shadow">
                                Add Section
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Classes & Sections Listing -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h4 class="font-bold text-slate-800 mb-4">Configured Classes & Sections</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($classes as $c)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-bold text-slate-800 text-base">{{ $c->name }}</h5>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">Level {{ $c->numeric_level }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mb-3">Sections:</p>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($c->sections as $sec)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-white border border-slate-300 text-xs font-medium text-slate-700 shadow-sm">
                                            <span>Section {{ $sec->name }}</span>
                                            <span class="text-[10px] text-slate-400 ml-1.5">(Cap: {{ $sec->capacity }})</span>
                                        </span>
                                    @empty
                                        <span class="text-xs text-amber-600 italic">No sections created yet.</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 col-span-2">No classes created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. School Houses Tab -->
        <div x-show="tab === 'houses'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add House Form -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                    <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-flag text-amber-500"></i>
                        <span>Add School House</span>
                    </h4>
                    <form action="{{ route('academics.houses.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">House Name</label>
                            <input type="text" name="name" placeholder="e.g. Phoenix" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">House Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="color" value="#3B82F6" class="h-10 w-16 p-0 border border-slate-300 rounded cursor-pointer">
                                <span class="text-xs text-slate-500">Select badge/theme color</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Description / Motto</label>
                            <textarea name="description" rows="3" placeholder="House motto or details..."
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                            Save House
                        </button>
                    </form>
                </div>

                <!-- Houses List -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h4 class="font-bold text-slate-800 mb-4">Active School Houses</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($houses as $h)
                            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50 relative overflow-hidden shadow-sm" style="border-top: 5px solid {{ $h->color }};">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-base font-bold text-slate-800">{{ $h->name }}</h5>
                                    <span class="w-4 h-4 rounded-full border border-white shadow" style="background-color: {{ $h->color }};"></span>
                                </div>
                                <p class="text-xs text-slate-600 mt-2 italic">{{ $h->description ?: 'No motto specified.' }}</p>
                                <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Enrolled Students:</span>
                                    <span class="font-bold text-slate-800">{{ $h->students_count }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">No houses configured yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Subjects Tab -->
        <div x-show="tab === 'subjects'" class="space-y-6" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Subject Form -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
                    <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-book-open text-teal-600"></i>
                        <span>Add Subject</span>
                    </h4>
                    <form action="{{ route('academics.subjects.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Subject Name</label>
                            <input type="text" name="name" placeholder="e.g. Mathematics" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Subject Code</label>
                            <input type="text" name="code" placeholder="e.g. MATH-101" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Subject Type</label>
                            <select name="type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
                                <option value="core">Core</option>
                                <option value="elective">Elective</option>
                                <option value="activity">Activity / Practical</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                            Save Subject
                        </button>
                    </form>
                </div>

                <!-- Subjects Table -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200">
                        <h4 class="font-bold text-slate-800">Curriculum Subjects</h4>
                    </div>
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3">Code</th>
                                <th class="px-6 py-3">Subject Name</th>
                                <th class="px-6 py-3">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($subjects as $subj)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-3 font-mono font-bold text-teal-700">{{ $subj->code }}</td>
                                    <td class="px-6 py-3 font-medium text-slate-900">{{ $subj->name }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold uppercase {{ $subj->type === 'core' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $subj->type }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-slate-400">No subjects registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

