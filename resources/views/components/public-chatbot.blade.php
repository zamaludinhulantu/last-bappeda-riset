<style>[x-cloak]{display:none!important;}</style>

<div x-data="publicChatBot()" x-cloak class="fixed z-50 w-[92vw] max-w-[380px] right-4 bottom-4 flex flex-col items-end gap-3">
    <div x-show="isOpen" x-transition.opacity.scale class="w-full overflow-hidden rounded-2xl border border-[#cde3ff] bg-white shadow-xl shadow-[#cde3ff]/50">
        <div class="flex items-center justify-between gap-2 bg-gradient-to-r from-[#0f3d73] via-[#1c4f88] to-[#0f3d73] px-4 py-3 text-white">
            <div>
                <p class="text-sm font-semibold">Asisten SIPRISDA</p>
                <p class="text-[11px] text-white/80">Tanya apa saja seputar portal riset</p>
            </div>
            <div class="flex items-center gap-1">
                <span class="text-[11px] bg-white/15 text-white px-2 py-1 rounded-full">Beta</span>
                <button type="button" @click="isOpen = false" class="rounded-full bg-white/15 p-2 hover:bg-white/25 transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
        <div class="bg-gradient-to-b from-[#f6f9ff] to-white flex flex-col h-full">
            <div x-ref="messages" class="space-y-2 px-3 py-3 max-h-80 overflow-y-auto">
                <template x-for="(message, index) in messages" :key="index">
                    <div class="flex" :class="message.from === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[80%] rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm" :class="message.from === 'user' ? 'bg-[#0f3d73] text-white' : 'bg-white border border-[#e5edff] text-gray-800'">
                            <p class="text-[11px] font-semibold mb-1" :class="message.from === 'user' ? 'text-white/80' : 'text-[#0f3d73]'" x-text="message.from === 'user' ? 'Anda' : 'SIPRISDA Bot'"></p>
                            <div x-show="message.html" x-html="message.html" class="space-y-1 text-[13px] leading-relaxed"></div>
                            <p x-show="!message.html" x-text="message.text"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div class="border-t border-[#cde3ff]/70 bg-white/80 backdrop-blur px-3 py-3 space-y-2">
                <div class="flex flex-wrap gap-2">
                    <template x-for="quick in quickReplies" :key="quick.prompt">
                        <button type="button" @click="useQuick(quick.prompt)" class="rounded-full border border-[#cde3ff] bg-[#e7f5ff]/80 px-3 py-1 text-[12px] font-semibold text-[#0f3d73] hover:bg-white transition">
                            <span x-text="quick.label"></span>
                        </button>
                    </template>
                </div>
                <form class="flex items-center gap-2" @submit.prevent="sendMessage">
                    <input type="text" x-model="input" class="flex-1 rounded-xl border border-[#cde3ff]/80 bg-white px-3 py-2 text-sm focus:border-[#0f3d73] focus:ring-2 focus:ring-[#cde3ff]" placeholder="Tulis pertanyaan Anda..." />
                    <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-[#0f3d73] px-3 py-2 text-sm font-semibold text-white hover:bg-[#0c2f5a] transition">
                        <i class="fas fa-paper-plane text-xs"></i> Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>

    <button type="button" @click="toggle()" class="group inline-flex items-center gap-3 rounded-full bg-[#0f3d73] px-4 py-3 text-white shadow-lg shadow-[#cde3ff]/80 hover:shadow-xl hover:scale-[1.02] transition">
        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 ring-2 ring-white/30">
            <i class="fas fa-message text-lg"></i>
        </div>
        <div class="text-left leading-tight">
            <p class="text-[11px] uppercase tracking-[0.2em] text-white/70">Butuh bantuan?</p>
            <p class="text-base font-semibold">Tanya SIPRISDA</p>
        </div>
    </button>
</div>

@push('scripts')
<script>
function publicChatBot() {
    const urls = {
        guide: @json(route('public.guide')),
        announcements: @json(route('public.announcements')),
        news: @json(route('public.news')),
    };

    const replyCatalog = [
        {
            keywords: ['ajukan', 'pengajuan', 'proposal', 'daftar', 'registrasi'],
            html: `Panduan pengajuan riset ada di <a href="${urls.guide}" target="_blank" rel="noopener">halaman panduan</a>. Lengkapi berkas, ikuti jadwal, lalu kirim melalui akun Anda.`
        },
        {
            keywords: ['pengumuman', 'katalog', 'riset disetujui', 'hasil seleksi'],
            html: `Daftar riset yang sudah disetujui bisa dicek di <a href="${urls.announcements}" target="_blank" rel="noopener">halaman pengumuman</a>.`
        },
        {
            keywords: ['berita', 'update', 'kabar', 'informasi terbaru'],
            html: `Berita terbaru tersedia di <a href="${urls.news}" target="_blank" rel="noopener">halaman berita</a>.`
        },
        {
            keywords: ['login', 'masuk', 'akun', 'password', 'kata sandi'],
            text: 'Pastikan email terdaftar dan sudah diverifikasi. Jika lupa kata sandi, gunakan fitur lupa password di halaman masuk untuk mengatur ulang.'
        },
        {
            keywords: ['kontak', 'hubungi', 'helpdesk', 'admin', 'narahubung'],
            text: 'Untuk bantuan lanjutan, kirim pesan melalui form di menu panduan atau hubungi admin Bappeda setempat.'
        }
    ];

    return {
        init() {
            this.$nextTick(() => this.scrollToBottom());
        },
        isOpen: false,
        input: '',
        messages: [
            { from: 'bot', text: 'Halo! Saya asisten SIPRISDA. Tanyakan cara ajukan riset, cek pengumuman, atau arahkan Anda ke halaman yang tepat.' }
        ],
        quickReplies: [
            { label: 'Cara ajukan riset', prompt: 'Bagaimana cara mengajukan riset?' },
            { label: 'Cek pengumuman', prompt: 'Di mana saya bisa melihat pengumuman riset?' },
            { label: 'Berita terbaru', prompt: 'Saya ingin membaca berita terbaru.' },
            { label: 'Bantuan login', prompt: 'Saya tidak bisa login.' }
        ],
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },
        useQuick(prompt) {
            this.input = prompt;
            this.sendMessage();
        },
        sendMessage() {
            const text = this.input.trim();
            if (!text) return;

            this.messages.push({ from: 'user', text });
            this.input = '';
            this.scrollToBottom();

            setTimeout(() => {
                const reply = this.makeReply(text);
                this.messages.push(reply);
                this.scrollToBottom();
            }, 220);
        },
        makeReply(text) {
            const normalized = text.toLowerCase();
            const match = replyCatalog.find(entry => entry.keywords.some(keyword => normalized.includes(keyword)));

            if (match) {
                if (match.html) {
                    return { from: 'bot', html: match.html };
                }
                return { from: 'bot', text: match.text };
            }

            return { from: 'bot', text: 'Terima kasih, pertanyaannya sudah dicatat. Coba buka menu Panduan atau Pengumuman sementara kami siapkan jawaban lebih lengkap.' };
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const box = this.$refs.messages;
                if (box) box.scrollTop = box.scrollHeight;
            });
        }
    };
}
</script>
@endpush
