<script setup>
import {
    ArrowDown,
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    BookOpenCheck,
    BriefcaseBusiness,
    Building2,
    ChartNoAxesCombined,
    ChevronRight,
    CircleUserRound,
    FileCheck2,
    Goal,
    GraduationCap,
    HeartHandshake,
    Instagram,
    LayoutDashboard,
    Linkedin,
    Menu,
    MessagesSquare,
    Network,
    Quote,
    School,
    ShieldCheck,
    Sparkles,
    UsersRound,
    X,
    Youtube,
} from 'lucide-vue-next';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    payload: {
        type: Object,
        required: true,
    },
});

const modules = [
    {
        number: '01',
        title: 'Portal Akademik',
        description: 'Jadwal, rombel, presensi, nilai, transkrip, dan ruang belajar tersusun dalam satu alur akademik yang konsisten.',
        icon: GraduationCap,
    },
    {
        number: '02',
        title: 'Manajemen Kesiswaan',
        description: 'Pendataan siswa, kedisiplinan, izin, keterlambatan, prestasi, hingga alumni dapat dipantau secara menyeluruh.',
        icon: UsersRound,
    },
    {
        number: '03',
        title: 'SDM & Kepegawaian',
        description: 'Fingerprint, izin pegawai, evaluasi kinerja, kalender kerja, dan dokumen SDM terhubung pada data yang sama.',
        icon: BriefcaseBusiness,
    },
    {
        number: '04',
        title: 'Hubin & Prakerin',
        description: 'Mapping industri, pembimbing, absensi GPS, jurnal, konsultasi, serta laporan PKL tersedia dalam satu ruang kerja.',
        icon: Building2,
    },
    {
        number: '05',
        title: 'Layanan Konseling',
        description: 'Pendampingan siswa menjadi lebih terarah melalui konsultasi, catatan perkembangan, dan tindak lanjut yang terlindungi.',
        icon: HeartHandshake,
    },
    {
        number: '06',
        title: 'Layanan Terpadu',
        description: 'Persuratan, pengaduan, notifikasi WhatsApp, QR verifikasi, dan layanan publik bergerak tanpa proses berulang.',
        icon: Network,
    },
    {
        number: '07',
        title: 'Perangkat Pembelajaran',
        description: 'Guru menyusun modul ajar, analisis pekan efektif, serta dokumen pembelajaran dengan format yang siap digunakan.',
        icon: BookOpenCheck,
    },
    {
        number: '08',
        title: 'OKR Sekolah',
        description: 'Target tahunan, bulanan, dan mingguan tiap unit dapat direncanakan, dievaluasi, dan dilaporkan secara transparan.',
        icon: Goal,
    },
    {
        number: '09',
        title: 'Dokumen Digital',
        description: 'Laporan PDF, arsip cloud, tanda tangan QR, analitik, dan Stella AI mempercepat pekerjaan administratif harian.',
        icon: FileCheck2,
    },
];

const navItems = computed(() => [
    { label: 'Layanan Aduan', href: props.payload.routes.complaint },
    { label: 'DigiReligi', href: props.payload.routes.digireligi },
    { label: 'Gallery Photo', href: props.payload.routes.gallery },
    { label: 'Forum Stella', href: props.payload.routes.forum },
    { label: 'Showcase Siswa', href: props.payload.routes.showcase },
]);

const currentSlide = ref(0);
const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const loading = ref(true);
const servicesSection = ref(null);
const moduleTrack = ref(null);
const moduleOffset = ref(0);
const worldCanvas = ref(null);
const logoLoadFailed = ref(false);
const statisticsVisible = ref(false);
const statsSection = ref(null);
const heroTimer = ref(null);
const canvasAnimation = ref(null);
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const activeSlide = computed(() => props.payload.slides[currentSlide.value] || props.payload.slides[0]);
const repeatedTickers = computed(() => [...props.payload.tickers, ...props.payload.tickers, ...props.payload.tickers]);

function goToSlide(index) {
    const total = props.payload.slides.length;
    currentSlide.value = (index + total) % total;
    restartHeroTimer();
}

function nextSlide() {
    goToSlide(currentSlide.value + 1);
}

function previousSlide() {
    goToSlide(currentSlide.value - 1);
}

function restartHeroTimer() {
    window.clearInterval(heroTimer.value);
    if (props.payload.slides.length > 1 && !reducedMotion) {
        heroTimer.value = window.setInterval(() => {
            currentSlide.value = (currentSlide.value + 1) % props.payload.slides.length;
        }, 6500);
    }
}

function handleScroll() {
    scrolled.value = window.scrollY > 24;

    if (!servicesSection.value || !moduleTrack.value || window.innerWidth < 900) {
        moduleOffset.value = 0;
        return;
    }

    const section = servicesSection.value;
    const rect = section.getBoundingClientRect();
    const scrollable = section.offsetHeight - window.innerHeight;
    const progress = Math.min(1, Math.max(0, -rect.top / Math.max(scrollable, 1)));
    const maxOffset = Math.max(0, moduleTrack.value.scrollWidth - window.innerWidth + 96);
    moduleOffset.value = progress * maxOffset;
}

function closeMobileMenu() {
    mobileMenuOpen.value = false;
}

function moduleIllustrationStyle(index) {
    const column = (index % 3) * 50;
    const row = Math.floor(index / 3) * 50;

    return {
        backgroundImage: `url("${props.payload.assets.serviceModules}")`,
        backgroundPosition: `${column}% ${row}%`,
    };
}

function initializeWorldCanvas() {
    const canvas = worldCanvas.value;
    if (!canvas) return;

    const context = canvas.getContext('2d');
    let nodes = [];
    let pointerX = 0;
    let pointerY = 0;

    const resize = () => {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = rect.height * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        nodes = Array.from({ length: window.innerWidth < 700 ? 18 : 34 }, (_, index) => ({
            x: (index * 137.5) % rect.width,
            y: (index * 83.7) % rect.height,
            vx: ((index % 3) - 1) * 0.08,
            vy: ((index % 5) - 2) * 0.035,
        }));
    };

    const onPointerMove = (event) => {
        const rect = canvas.getBoundingClientRect();
        pointerX = event.clientX - rect.left;
        pointerY = event.clientY - rect.top;
    };

    const draw = () => {
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;
        context.clearRect(0, 0, width, height);

        nodes.forEach((node) => {
            node.x += node.vx;
            node.y += node.vy;
            if (node.x < 0 || node.x > width) node.vx *= -1;
            if (node.y < 0 || node.y > height) node.vy *= -1;
        });

        nodes.forEach((node, index) => {
            nodes.slice(index + 1).forEach((other) => {
                const distance = Math.hypot(node.x - other.x, node.y - other.y);
                if (distance < 190) {
                    context.strokeStyle = `rgba(220, 38, 38, ${0.13 * (1 - distance / 190)})`;
                    context.lineWidth = 1;
                    context.beginPath();
                    context.moveTo(node.x, node.y);
                    context.lineTo(other.x, other.y);
                    context.stroke();
                }
            });

            const pointerDistance = Math.hypot(node.x - pointerX, node.y - pointerY);
            if (pointerDistance < 230) {
                context.strokeStyle = `rgba(239, 68, 68, ${0.32 * (1 - pointerDistance / 230)})`;
                context.beginPath();
                context.moveTo(node.x, node.y);
                context.lineTo(pointerX, pointerY);
                context.stroke();
            }

            context.fillStyle = 'rgba(220, 38, 38, 0.42)';
            context.beginPath();
            context.arc(node.x, node.y, 2, 0, Math.PI * 2);
            context.fill();
        });

        if (!reducedMotion) canvasAnimation.value = requestAnimationFrame(draw);
    };

    resize();
    draw();
    window.addEventListener('resize', resize);
    canvas.addEventListener('pointermove', onPointerMove);
    canvas._cleanup = () => {
        window.removeEventListener('resize', resize);
        canvas.removeEventListener('pointermove', onPointerMove);
    };
}

onMounted(async () => {
    await nextTick();
    restartHeroTimer();
    initializeWorldCanvas();
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleScroll);
    handleScroll();

    const observer = new IntersectionObserver(([entry]) => {
        if (entry.isIntersecting) statisticsVisible.value = true;
    }, { threshold: 0.35 });
    if (statsSection.value) observer.observe(statsSection.value);
    statsSection.value._observer = observer;

    window.setTimeout(() => {
        loading.value = false;
    }, reducedMotion ? 100 : 850);
});

onBeforeUnmount(() => {
    window.clearInterval(heroTimer.value);
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleScroll);
    cancelAnimationFrame(canvasAnimation.value);
    worldCanvas.value?._cleanup?.();
    statsSection.value?._observer?.disconnect();
});
</script>

<template>
    <div class="landing-shell">
        <Transition name="loader-fade">
            <div v-if="loading" class="landing-loader" aria-live="polite">
                <div class="loader-symbol">
                    <span></span><span></span><span></span>
                    <strong>TS</strong>
                </div>
                <p>Menghubungkan layanan sekolah</p>
            </div>
        </Transition>

        <header class="site-header" :class="{ 'is-scrolled': scrolled, 'menu-open': mobileMenuOpen }">
            <div class="header-inner">
                <a :href="payload.routes.home" class="brand" aria-label="Kembali ke halaman utama">
                    <span class="brand-mark">
                        <img
                            v-if="payload.school.logo && !logoLoadFailed"
                            :src="payload.school.logo"
                            :alt="payload.school.name"
                            @error="logoLoadFailed = true"
                        >
                        <span v-else>TS</span>
                    </span>
                    <span class="brand-copy">
                        <strong>SISFO TS</strong>
                        <small>{{ payload.school.name }}</small>
                    </span>
                </a>

                <nav class="desktop-navigation" aria-label="Navigasi utama">
                    <a v-for="item in navItems" :key="item.label" :href="item.href">{{ item.label }}</a>
                    <a :href="payload.authenticated ? payload.routes.dashboard : payload.routes.login" class="primary-navigation-action">
                        <LayoutDashboard v-if="payload.authenticated" :size="17" />
                        <CircleUserRound v-else :size="17" />
                        {{ payload.authenticated ? 'Dashboard Utama' : 'Login' }}
                    </a>
                </nav>

                <button
                    class="mobile-menu-button"
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen"
                    :aria-label="mobileMenuOpen ? 'Tutup menu' : 'Buka menu'"
                >
                    <X v-if="mobileMenuOpen" :size="22" />
                    <Menu v-else :size="22" />
                </button>
            </div>

            <Transition name="mobile-menu">
                <nav v-if="mobileMenuOpen" class="mobile-navigation" aria-label="Navigasi mobile">
                    <a v-for="item in navItems" :key="item.label" :href="item.href" @click="closeMobileMenu">
                        {{ item.label }} <ChevronRight :size="17" />
                    </a>
                    <a :href="payload.authenticated ? payload.routes.dashboard : payload.routes.login" class="mobile-login" @click="closeMobileMenu">
                        {{ payload.authenticated ? 'Dashboard Utama' : 'Login ke SISFO' }} <ArrowUpRight :size="17" />
                    </a>
                </nav>
            </Transition>
        </header>

        <main>
            <section class="hero-section" aria-label="Sorotan utama">
                <TransitionGroup name="hero-slide">
                    <div v-for="(slide, index) in payload.slides" v-show="index === currentSlide" :key="slide.id" class="hero-media">
                        <img :src="slide.image" :alt="slide.title">
                    </div>
                </TransitionGroup>
                <div class="hero-shade"></div>

                <div class="hero-content page-container">
                    <div class="hero-copy" :key="activeSlide.id">
                        <p class="section-kicker light">{{ activeSlide.eyebrow }}</p>
                        <h1>{{ activeSlide.title }}</h1>
                        <p class="hero-description">{{ activeSlide.description }}</p>
                        <div class="hero-actions">
                            <a v-if="activeSlide.ctaLabel && activeSlide.ctaUrl" :href="activeSlide.ctaUrl" class="button button-red">
                                {{ activeSlide.ctaLabel }} <ArrowUpRight :size="18" />
                            </a>
                            <a href="#layanan" class="button button-glass">
                                Lihat Ekosistem <ArrowDown :size="18" />
                            </a>
                        </div>
                    </div>

                    <div class="hero-controls" v-if="payload.slides.length > 1">
                        <div class="slide-counter"><strong>{{ String(currentSlide + 1).padStart(2, '0') }}</strong><span>/ {{ String(payload.slides.length).padStart(2, '0') }}</span></div>
                        <button type="button" @click="previousSlide" aria-label="Slide sebelumnya"><ArrowLeft :size="20" /></button>
                        <button type="button" @click="nextSlide" aria-label="Slide berikutnya"><ArrowRight :size="20" /></button>
                    </div>
                </div>

                <div class="hero-progress" v-if="payload.slides.length > 1">
                    <button v-for="(slide, index) in payload.slides" :key="slide.id" type="button" @click="goToSlide(index)" :class="{ active: index === currentSlide }" :aria-label="`Buka slide ${index + 1}`">
                        <span></span>
                    </button>
                </div>
            </section>

            <section class="ticker-wrap" aria-label="Info terkini">
                <div class="page-container">
                    <div class="ticker-bar">
                        <div class="ticker-label"><Sparkles :size="16" /> Info Terkini</div>
                        <div class="ticker-viewport">
                            <div class="ticker-track">
                                <a v-for="(ticker, index) in repeatedTickers" :key="`${ticker.id}-${index}`" :href="ticker.url || '#statistics'">
                                    <span>{{ ticker.text }}</span><i></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="statistics" ref="statsSection" class="statistics-section">
                <div class="page-container statistics-grid">
                    <article v-for="(statistic, index) in payload.statistics" :key="statistic.label">
                        <span class="stat-index">{{ String(index + 1).padStart(2, '0') }}</span>
                        <strong>{{ statisticsVisible ? statistic.value.toLocaleString('id-ID') : '0' }}{{ statistic.suffix }}</strong>
                        <p>{{ statistic.label }}</p>
                    </article>
                </div>
            </section>

            <section id="layanan" ref="servicesSection" class="services-scroll-section">
                <div class="services-sticky">
                    <canvas ref="worldCanvas" class="world-lines" aria-hidden="true"></canvas>
                    <div class="services-heading page-container">
                        <div>
                            <p class="section-kicker">SISFO Ecosystem</p>
                            <h2>Layanan Sekolah<br>Terintegrasi</h2>
                        </div>
                        <p>Platform tunggal yang menghubungkan seluruh aspek operasional sekolah untuk efisiensi dan transparansi maksimal.</p>
                    </div>

                    <div class="module-track-viewport">
                        <div ref="moduleTrack" class="module-track" :style="{ transform: `translate3d(${-moduleOffset}px, 0, 0)` }">
                            <article v-for="(module, index) in modules" :key="module.number" class="module-card">
                                <div class="module-card-media" :style="moduleIllustrationStyle(index)" role="img" :aria-label="`Ilustrasi ${module.title}`">
                                    <div class="module-card-top">
                                        <span>{{ module.number }}</span>
                                        <component :is="module.icon" :size="24" />
                                    </div>
                                </div>
                                <div class="module-card-copy">
                                    <h3>{{ module.title }}</h3>
                                    <p>{{ module.description }}</p>
                                </div>
                                <ArrowUpRight :size="21" class="module-arrow" />
                            </article>
                            <article class="module-card module-card-visual">
                                <img :src="payload.assets.modules" alt="Ilustrasi ekosistem layanan sekolah terintegrasi">
                                <div><Quote :size="26" /><p>Lebih sedikit pekerjaan berulang. Lebih banyak waktu untuk mendampingi siswa.</p></div>
                            </article>
                        </div>
                    </div>

                    <div class="scroll-instruction"><ArrowRight :size="17" /> Scroll untuk menjelajah</div>
                </div>
            </section>

            <section id="architecture" class="architecture-section">
                <div class="page-container architecture-heading">
                    <p class="section-kicker">Connected Architecture</p>
                    <div class="architecture-title-row">
                        <h2>Satu Data, Sejuta Solusi<br>Untuk SMK Telkom</h2>
                        <p>SISFO TS bukan sekadar aplikasi, melainkan platform yang menghubungkan data siswa dari Dapodik dengan monitoring real-time di sekolah. Memastikan koordinasi antara Guru, Siswa, dan Orang Tua berjalan harmonis.</p>
                    </div>
                </div>

                <div class="architecture-visual">
                    <img :src="payload.assets.modules" alt="Arsitektur data SISFO TS yang saling terhubung">
                    <div class="architecture-overlay"></div>
                    <div class="architecture-nodes page-container">
                        <div><School :size="22" /><span>Dapodik</span></div>
                        <div><ChartNoAxesCombined :size="22" /><span>Monitoring Real-time</span></div>
                        <div><MessagesSquare :size="22" /><span>Kolaborasi Sekolah</span></div>
                        <div><ShieldCheck :size="22" /><span>Data Terlindungi</span></div>
                    </div>
                </div>
            </section>

            <section id="berita" class="news-section">
                <div class="page-container">
                    <div class="news-heading">
                        <div>
                            <p class="section-kicker">Berita Terkini</p>
                            <h2>Informasi & Berita<br>Terbaru</h2>
                        </div>
                        <p>Ikuti perkembangan terbaru seputar kegiatan, prestasi, dan informasi penting dari {{ payload.school.name }}.</p>
                    </div>

                    <div v-if="payload.news.length" class="news-grid">
                        <a v-for="(item, index) in payload.news" :key="item.id" :href="item.url" class="news-card" :class="{ featured: index === 0 }">
                            <div class="news-image"><img :src="item.image" :alt="item.title"></div>
                            <div class="news-copy">
                                <div><span>{{ item.category }}</span><time>{{ item.date }}</time></div>
                                <h3>{{ item.title }}</h3>
                                <p>{{ item.summary || 'Baca informasi lengkap dan perkembangan terbaru dari sekolah.' }}</p>
                                <strong>Baca berita <ArrowUpRight :size="17" /></strong>
                            </div>
                        </a>
                    </div>
                    <div v-else class="news-empty">
                        <Sparkles :size="28" />
                        <h3>Berita terbaru sedang dipersiapkan.</h3>
                        <p>Kembali lagi untuk mengikuti perkembangan sekolah.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="page-container footer-main">
                <div class="footer-brand">
                    <span class="brand-mark">
                        <img
                            v-if="payload.school.logo && !logoLoadFailed"
                            :src="payload.school.logo"
                            :alt="payload.school.name"
                            @error="logoLoadFailed = true"
                        >
                        <span v-else>TS</span>
                    </span>
                    <div>
                        <strong>SISFO TS</strong>
                        <p>{{ payload.school.name }}</p>
                    </div>
                </div>

                <nav class="footer-legal">
                    <a :href="payload.routes.privacy">Privacy</a>
                    <a :href="payload.routes.terms">Terms</a>
                    <a :href="payload.routes.security">Security</a>
                </nav>

                <div class="footer-social">
                    <a href="#" aria-label="Instagram"><Instagram :size="19" /></a>
                    <a href="#" aria-label="YouTube"><Youtube :size="20" /></a>
                    <a href="#" aria-label="LinkedIn"><Linkedin :size="19" /></a>
                </div>
            </div>

            <div class="footer-bottom page-container">
                <span>© {{ new Date().getFullYear() }} {{ payload.school.name }}</span>
                <span>Built for better education</span>
            </div>

            <div class="footer-statement" aria-hidden="true">
                <div>THE REAL INFORMATIC SCHOOLS&nbsp;&nbsp;THE REAL INFORMATIC SCHOOLS&nbsp;&nbsp;</div>
            </div>
        </footer>
    </div>
</template>
