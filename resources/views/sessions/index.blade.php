<x-layouts.app title="Academic Sessions">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span>Academic Session & Financial Year</span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Create Academic Session Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
            <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center space-x-2">
                <i class="fa-solid fa-calendar-plus text-teal-600"></i>
                <span>Add Academic Session</span>
            </h3>
            <p class="text-xs text-slate-500 mb-5">Define new academic / financial year</p>

            <form action="{{ route('sessions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Session Name</label>
                    <input type="text" name="name" placeholder="e.g. 2026-2027" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" name="start_date" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="end_date" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="is_current" id="is_current" value="1" class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                    <label for="is_current" class="text-xs font-medium text-slate-700 cursor-pointer">Set as Current Active Session</label>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 px-4 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                    Create Session
                </button>
            </form>
        </div>

        <!-- Sessions List Table -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-clock-rotate-left text-teal-600"></i>
                    <span>All Academic Sessions</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Session Name</th>
                            <th class="px-6 py-3">Duration</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Enrolled</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-slate-50 transition {{ $session->is_current ? 'bg-teal-50/40' : '' }}">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $session->name }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $session->start_date->format('M d, Y') }} &ndash; {{ $session->end_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->is_current)
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                                            <span>Current Active</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 font-medium">
                                    {{ $session->students_count }} students
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if(!$session->is_current)
                                        <form action="{{ route('sessions.set_current', $session->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-slate-100 hover:bg-teal-600 hover:text-white text-slate-700 text-xs font-semibold rounded transition border border-slate-200">
                                                Set Active
                                            </button>
                                        </form>

                                        <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this session?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-teal-700 font-bold">In Use</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">
                                    No sessions defined yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

