<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div
                class="relative h-48 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 shadow-lg overflow-hidden mb-20">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 -mt-32 relative z-10">

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 relative mt-12">

                        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 w-32 h-32">
                            <div
                                class="w-full h-full rounded-full border-4 border-white shadow-lg overflow-hidden bg-gray-200">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=128&bold=true"
                                    alt="{{ $user->name }}" class="h-full w-full object-cover">
                            </div>
                        </div>

                        <div class="pt-20 pb-8 px-6 text-center">
                            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 font-medium mb-4">{{ $user->email }}</p>

                            <div class="flex justify-center gap-2 mb-8">
                                @if (method_exists($user, 'getRoleNames') && $user->getRoleNames()->isNotEmpty())
                                    @foreach ($user->getRoleNames() as $role)
                                        <span
                                            class="px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider border border-indigo-100 shadow-sm">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                @else
                                    <span
                                        class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider">User</span>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 pt-6 flex justify-around">
                                <div class="text-center group">
                                    <span
                                        class="block text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">Aktif</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</span>
                                </div>
                                <div class="text-center group">
                                    <span
                                        class="block text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $user->created_at->format('M Y') }}</span>
                                    <span
                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Bergabung</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6 mt-12 lg:mt-0">

                    <div
                        class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div
                        class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                        @include('profile.partials.update-password-form')
                    </div>

                    <div
                        class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-red-100 relative overflow-hidden bg-red-50/10">
                        <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
