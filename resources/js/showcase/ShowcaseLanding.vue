<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm" :class="{ 'py-4': !scrolled, 'py-2': scrolled }">
            <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
                <a :href="payload.routes.home" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center p-1.5 shadow-md shadow-red-600/20 group-hover:scale-105 transition-transform">
                        <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <span class="font-outfit font-black text-xl tracking-tighter text-gray-900 group-hover:text-red-600 transition-colors">
                        SISFO <span class="text-red-600">TS</span>
                    </span>
                </a>
                <div>
                    <a :href="payload.routes.home" class="text-sm font-bold text-gray-500 hover:text-red-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-16 px-6 overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute inset-0 z-0 opacity-40 pointer-events-none">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-red-100 rounded-full blur-[100px] transform translate-x-1/3 -translate-y-1/4"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-red-50 rounded-full blur-[80px] transform -translate-x-1/4 translate-y-1/4"></div>
            </div>

            <div class="max-w-4xl mx-auto text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 border border-red-100 text-red-600 text-sm font-bold mb-6">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    Karya & Keahlian Siswa
                </div>
                
                <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tight leading-[1.1] mb-6">
                    Temukan <span class="text-red-600">Talenta Terbaik</span> SMK Telkom
                </h1>
                
                <p class="text-lg md:text-xl text-gray-500 font-medium leading-relaxed max-w-2xl mx-auto mb-12">
                    Eksplorasi portofolio karya dan keahlian siswa-siswi berprestasi kami. Temukan inovator muda masa depan untuk kebutuhan industri Anda.
                </p>

                <!-- Search Bar -->
                <div class="relative max-w-2xl mx-auto group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input 
                        type="text" 
                        v-model="searchQuery" 
                        @input="debounceSearch"
                        placeholder="Cari berdasarkan nama, keahlian (contoh: Laravel, Design)..." 
                        class="w-full pl-16 pr-6 py-5 bg-white border-2 border-gray-100 rounded-2xl text-gray-900 font-medium text-lg focus:border-red-500 focus:ring-0 shadow-lg shadow-gray-200/50 transition-all outline-none"
                    >
                    <!-- Loading indicator inside search -->
                    <div v-if="loading" class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none">
                        <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Results -->
        <div class="max-w-7xl mx-auto px-6 pb-24 relative z-10">
            <!-- State: Initial Loading -->
            <div v-if="initialLoading" class="flex flex-col items-center justify-center py-20">
                <svg class="animate-spin h-10 w-10 text-red-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 font-medium">Memuat data talenta...</p>
            </div>

            <!-- State: Empty Results -->
            <div v-else-if="students.length === 0" class="text-center py-20 bg-gray-50 rounded-[32px] border border-dashed border-gray-200">
                <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-5 text-gray-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Talenta Tidak Ditemukan</h3>
                <p class="text-gray-500 font-medium">Kami tidak dapat menemukan siswa dengan keahlian yang Anda cari.</p>
                <button @click="clearSearch" class="mt-6 text-red-600 font-bold hover:underline text-sm">Hapus Pencarian</button>
            </div>

            <!-- State: Results Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <a 
                    v-for="student in students" 
                    :key="student.id" 
                    :href="student.url"
                    class="group bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-red-900/10 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
                >
                    <!-- Card Header -->
                    <div class="h-24 bg-gradient-to-br from-red-600 to-red-800 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 12px 12px;"></div>
                    </div>
                    
                    <!-- Avatar -->
                    <div class="px-6 relative -mt-12 mb-3">
                        <div class="w-24 h-24 bg-white rounded-2xl p-1.5 shadow-md border border-gray-50 mx-auto transform group-hover:scale-105 transition-transform duration-300">
                            <div class="w-full h-full bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center text-3xl">
                                <img v-if="student.avatar" :src="student.avatar" :alt="student.name" class="w-full h-full object-cover">
                                <span v-else>👋</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="px-6 pb-6 text-center flex flex-col flex-1">
                        <h3 class="text-xl font-black text-gray-900 mb-1 leading-tight group-hover:text-red-600 transition-colors">{{ student.name }}</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-5">{{ student.jurusan }}</p>
                        
                        <!-- Skills Badges -->
                        <div v-if="student.skills.length > 0" class="flex flex-wrap justify-center gap-1.5 mb-6">
                            <span v-for="(skill, i) in student.skills" :key="i" class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">
                                {{ skill.name }}
                            </span>
                            <span v-if="student.skills_count > 3" class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-100">
                                +{{ student.skills_count - 3 }}
                            </span>
                        </div>
                        
                        <!-- Footer -->
                        <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between text-sm text-gray-500 font-bold">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ student.projects_count }} Karya
                            </div>
                            <div class="text-red-600 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity -translate-x-2 group-hover:translate-x-0 transform duration-300">
                                Profil <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="mt-12 flex items-center justify-center gap-2">
                <button 
                    @click="fetchData(pagination.current_page - 1)" 
                    :disabled="pagination.current_page === 1"
                    class="w-10 h-10 rounded-xl flex items-center justify-center border border-gray-200 text-gray-500 hover:border-red-500 hover:text-red-600 disabled:opacity-50 disabled:hover:border-gray-200 disabled:hover:text-gray-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                
                <span class="text-sm font-bold text-gray-700 px-4">
                    Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
                </span>
                
                <button 
                    @click="fetchData(pagination.current_page + 1)" 
                    :disabled="pagination.current_page === pagination.last_page"
                    class="w-10 h-10 rounded-xl flex items-center justify-center border border-gray-200 text-gray-500 hover:border-red-500 hover:text-red-600 disabled:opacity-50 disabled:hover:border-gray-200 disabled:hover:text-gray-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    payload: {
        type: Object,
        required: true
    }
});

const searchQuery = ref('');
const students = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
});
const initialLoading = ref(true);
const loading = ref(false);
const scrolled = ref(false);

let searchTimeout = null;

const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
};

const fetchData = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(props.payload.routes.api_search, {
            params: {
                page: page,
                search: searchQuery.value || null
            },
            headers: {
                'Accept': 'application/json'
            }
        });
        
        students.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page
        };
    } catch (error) {
        console.error("Gagal memuat data showcase:", error);
    } finally {
        loading.value = false;
        initialLoading.value = false;
    }
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchData(1);
    }, 400); // 400ms delay for search
};

const clearSearch = () => {
    searchQuery.value = '';
    fetchData(1);
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    fetchData();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
/* Scoped styles can be placed here */
</style>
