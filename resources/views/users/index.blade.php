<x-layouts.app title="User & Access Management">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span>System Users & Roles</span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ editingUser: null }">
        <!-- 1. Add / Edit User Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-fit">
            <h4 class="font-bold text-slate-800 mb-1 flex items-center space-x-2">
                <i class="fa-solid fa-user-shield text-teal-600"></i>
                <span x-text="editingUser ? 'Edit User Account' : 'Create User Account'">Create User Account</span>
            </h4>
            <p class="text-xs text-slate-500 mb-4">Manage access credentials & roles</p>

            <!-- Create Form -->
            <form x-show="!editingUser" action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                    <input type="text" name="name" placeholder="e.g. John Doe" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                    <input type="email" name="email" placeholder="user@school.com" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                    <input type="password" name="password" placeholder="Min 6 characters" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Role *</label>
                    <select name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                        <option value="operator">Operator (Front Desk / Fees)</option>
                        <option value="teacher">Teacher (Academics / Marks)</option>
                        <option value="admin">Administrator (Full Access)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Link to Staff Member</label>
                    <select name="staff_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                        <option value="">None (Standalone Account)</option>
                        @foreach($unlinkedStaff as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->employee_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-teal-600 rounded border-slate-300">
                    <label for="is_active" class="text-xs font-medium text-slate-700 cursor-pointer">Active Account</label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm transition shadow">
                    Create User Account
                </button>
            </form>

            <!-- Edit Form -->
            <form x-show="editingUser" :action="'/users/' + (editingUser ? editingUser.id : '')" method="POST" class="space-y-4" x-cloak>
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                    <input type="text" name="name" x-model="editingUser.name" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                    <input type="email" name="email" x-model="editingUser.email" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Change password..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Role *</label>
                    <select name="role" x-model="editingUser.role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                        <option value="operator">Operator</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" x-model="editingUser.is_active" class="w-4 h-4 text-teal-600 rounded border-slate-300">
                    <label for="edit_is_active" class="text-xs font-medium text-slate-700 cursor-pointer">Active Account</label>
                </div>

                <div class="flex space-x-2">
                    <button type="button" @click="editingUser = null" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-xs transition shadow">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Users Table -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h4 class="font-bold text-slate-800">Registered System Users</h4>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('users.index', ['role' => 'admin']) }}" class="px-2 py-1 rounded text-[11px] font-semibold {{ request('role') === 'admin' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600' }}">Admin</a>
                    <a href="{{ route('users.index', ['role' => 'operator']) }}" class="px-2 py-1 rounded text-[11px] font-semibold {{ request('role') === 'operator' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">Operator</a>
                    <a href="{{ route('users.index', ['role' => 'teacher']) }}" class="px-2 py-1 rounded text-[11px] font-semibold {{ request('role') === 'teacher' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-600' }}">Teacher</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">User</th>
                            <th class="px-6 py-3.5">Role</th>
                            <th class="px-6 py-3.5">Linked Staff</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                            <span class="text-xs text-slate-400 font-mono">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold uppercase
                                        {{ $user->role === 'admin' ? 'bg-rose-100 text-rose-800' : ($user->role === 'operator' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800') }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    {{ $user->staff ? $user->staff->full_name . ' (' . $user->staff->employee_id . ')' : 'None' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold uppercase {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $user->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button type="button" @click="editingUser = {{ json_encode($user) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">
                                        Edit
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.toggle', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold {{ $user->is_active ? 'text-rose-500 hover:text-rose-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

