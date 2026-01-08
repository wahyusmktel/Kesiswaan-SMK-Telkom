<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                    {{ __('Manajemen Hak Akses') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Atur izin dan kewenangan untuk setiap peran pengguna dalam sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100 uppercase tracking-wider">
                    Super Admin Only
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar: Role List -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 sticky top-4">
                        <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Pilih Role</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($roles as $role)
                                <button type="button" 
                                    onclick="selectRole('{{ $role->id }}', '{{ $role->name }}')"
                                    id="role-btn-{{ $role->id }}"
                                    class="role-btn w-full text-left px-5 py-4 hover:bg-red-50 transition-all flex items-center justify-between group">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-700 group-hover:text-red-700 transition-colors">{{ $role->name }}</span>
                                        <span class="text-xs text-gray-400 mt-0.5">{{ $role->permissions->count() }} Hak Akses Aktif</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Content: Permission Checklist -->
                <div class="lg:col-span-3">
                    <div id="permission-container" class="hidden">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800" id="selected-role-name">Role Name</h3>
                                    <p class="text-xs text-gray-500 mt-0.5 italic">Centang untuk memberikan hak akses, hapus centang untuk mencabut.</p>
                                </div>
                                <button type="button" onclick="savePermissions()" 
                                    class="inline-flex items-center px-6 py-2.5 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all shadow-md shadow-red-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>

                            <div class="p-6 bg-gray-50/30">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
                                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-50">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                </div>
                                                <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider">{{ $group }}</h4>
                                            </div>
                                            <div class="space-y-3">
                                                @foreach($groupPermissions as $permission)
                                                    <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer group/item">
                                                        <div class="relative flex items-center mt-0.5">
                                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                                class="permission-checkbox w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500 transition-all cursor-pointer">
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <span class="text-sm font-medium text-gray-700 group-hover/item:text-gray-900 transition-colors">{{ $permission->name }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 py-20 px-6 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Pilih Role di Samping</h3>
                            <p class="text-gray-500 leading-relaxed italic">Silakan pilih salah satu role dari daftar di sebelah kiri untuk mulai mengatur hak akses dan kewenangan sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentRoleId = null;

        function selectRole(roleId, roleName) {
            currentRoleId = roleId;
            
            // UI Updates
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('bg-red-50', 'border-l-4', 'border-red-600', 'pl-4'));
            const activeBtn = document.getElementById('role-btn-' + roleId);
            activeBtn.classList.add('bg-red-50', 'border-l-4', 'border-red-600', 'pl-4');
            
            document.getElementById('permission-container').classList.remove('hidden');
            document.getElementById('empty-state').classList.add('hidden');
            document.getElementById('selected-role-name').innerText = roleName;

            // Reset checkboxes
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);

            // Fetch permissions
            fetch(`/super-admin/permissions/${roleId}`)
                .then(response => response.json())
                .then(data => {
                    data.permissions.forEach(permName => {
                        const checkbox = document.querySelector(`.permission-checkbox[value="${permName}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat hak akses.', 'error');
                });
        }

        function savePermissions() {
            if (!currentRoleId) return;

            const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked'))
                .map(cb => cb.value);

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Hak akses role akan diperbarui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    
                    fetch(`/super-admin/permissions/${currentRoleId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ permissions: selectedPermissions })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#dc2626'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Gagal menyimpan perubahan.', 'error');
                    });
                }
            });
        }
    </script>
    <style>
        .role-btn.bg-red-50 .font-bold { color: #dc2626; }
        .role-btn.bg-red-50 svg { color: #dc2626; }
        .permission-checkbox:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }
    </style>
    @endpush
</x-app-layout>
