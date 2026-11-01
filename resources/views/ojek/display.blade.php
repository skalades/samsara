<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Monitor Antrean Ojek | Basecamp SAMSARA Cigedug</title>
    <link rel="icon" type="image/jpeg" href="/LOGO.jpeg">

    <!-- Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans bg-slate-950 text-white selection:bg-[#D97736] overflow-hidden"
      x-data="tvDisplay()">

    <!-- TOP HEADER BAR -->
    <header class="h-20 sm:h-24 px-6 sm:px-10 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <img src="/LOGO.jpeg" alt="Logo SAMSARA" class="h-12 w-12 sm:h-14 sm:w-14 rounded-2xl object-cover ring-2 ring-[#D97736]">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="font-heading font-black text-2xl sm:text-3xl text-white tracking-wider">SAMSARA</h1>
                    <span class="px-2.5 py-0.5 rounded-md bg-[#D97736] text-white font-extrabold text-xs tracking-widest uppercase">OJEK POS</span>
                </div>
                <p class="text-xs sm:text-sm text-emerald-400 font-semibold tracking-wide">
                    Pangkalan Resmi Paguyuban Ojek Cigedug &bull; Basecamp Gunung Cikuray 2.821 MDPL
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <!-- Audio Toggle Button -->
            <button type="button" @click="toggleAudio()"
                    class="px-4 py-2 rounded-xl border text-xs font-bold flex items-center gap-2 transition"
                    :class="audioEnabled ? 'bg-emerald-600/20 border-emerald-500/50 text-emerald-300' : 'bg-slate-800 border-slate-700 text-slate-400'">
                <i class="fas" :class="audioEnabled ? 'fa-volume-high text-emerald-400' : 'fa-volume-xmark'"></i>
                <span x-text="audioEnabled ? 'Suara Panggilan Aktif' : 'Aktifkan Suara TV'"></span>
            </button>

            <!-- Digital Clock -->
            <div class="text-right">
                <span class="font-heading font-black text-2xl sm:text-3xl text-amber-300 block tracking-wider" x-text="currentTime"></span>
                <span class="text-xs text-slate-400 font-medium" x-text="currentDate"></span>
            </div>
        </div>
    </header>

    <!-- MAIN DISPLAY SCREEN -->
    <main class="flex-1 p-6 sm:p-8 grid grid-cols-12 gap-6 overflow-hidden">

        <!-- LEFT HERO: CURRENT CALLED TICKET (7 Columns) -->
        <section class="col-span-12 lg:col-span-7 flex flex-col justify-between bg-gradient-to-br from-[#1E3A2B] via-[#12281d] to-slate-950 rounded-3xl p-6 sm:p-10 border-2 border-[#2D5841] shadow-2xl relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

            <div>
                <!-- Top Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-amber-400/20 text-amber-300 font-extrabold text-sm border border-amber-400/40">
                        <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
                        SEDANG DIPANGGIL SEKARANG
                    </span>
                    <span class="text-xs text-slate-300 font-medium">Silakan menuju pangkalan motor</span>
                </div>

                <template x-if="latestCalled">
                    <div>
                        <!-- Ticket Number in Huge Text -->
                        <div class="py-4">
                            <span class="text-xs sm:text-sm text-slate-300 font-semibold tracking-wider uppercase block">Nomor Antrean:</span>
                            <h2 class="font-heading font-black text-5xl sm:text-7xl lg:text-8xl text-amber-300 tracking-wider drop-shadow-md my-1"
                                x-text="latestCalled.ticket_number">
                            </h2>
                        </div>

                        <!-- Route & Passenger Details -->
                        <div class="bg-black/30 backdrop-blur-md rounded-2xl p-5 border border-white/10 mt-4 space-y-4">
                            <div>
                                <span class="text-xs text-emerald-300 uppercase tracking-wider font-bold block mb-1">Rute Perjalanan:</span>
                                <h3 class="font-heading font-extrabold text-xl sm:text-2xl text-white" x-text="latestCalled.route?.route_name"></h3>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-white/10">
                                <div>
                                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block">Nama Penumpang:</span>
                                    <span class="font-heading font-bold text-lg sm:text-xl text-white block mt-0.5" x-text="latestCalled.passenger_name"></span>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block">Waktu Panggil:</span>
                                    <span class="font-mono font-bold text-lg text-emerald-400 block mt-0.5" x-text="formatTime(latestCalled.called_at)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Driver Card -->
                        <div class="mt-6 p-5 rounded-2xl bg-gradient-to-r from-emerald-950/80 to-[#1E3A2B]/80 border-2 border-emerald-500/40 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                                    <i class="fas fa-motorcycle"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-300 block">Driver Bertugas:</span>
                                    <h4 class="font-heading font-black text-xl sm:text-2xl text-white" x-text="latestCalled.driver?.name"></h4>
                                    <p class="text-xs text-slate-300" x-text="latestCalled.driver?.motorcycle_model"></p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Plat Motor:</span>
                                <span class="font-mono font-black text-xl sm:text-2xl px-3.5 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-amber-300 tracking-wider shadow-inner block mt-1"
                                      x-text="latestCalled.driver?.license_plate">
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!latestCalled">
                    <div class="h-64 flex flex-col items-center justify-center text-center text-slate-400">
                        <i class="fas fa-motorcycle text-5xl text-slate-600 mb-4 animate-bounce"></i>
                        <h3 class="font-heading font-bold text-2xl text-slate-200">Menunggu Panggilan Berikutnya</h3>
                        <p class="text-sm text-slate-400 max-w-md mt-1">
                            Semua antrean saat ini telah terlayani atau belum ada panggilan aktif dari koordinator.
                        </p>
                    </div>
                </template>
            </div>

            <!-- Footer Note in Screen -->
            <div class="pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-2">
                    <i class="fas fa-shield-halved text-emerald-400"></i>
                    Wajib gunakan helm keselamatan & ikuti arahan driver resmi Cigedug
                </span>
                <span class="font-mono text-emerald-400 font-bold">Basecamp SAMSARA v1.4</span>
            </div>
        </section>

        <!-- RIGHT SIDE: QUEUE LISTS & ROTATION (5 Columns) -->
        <section class="col-span-12 lg:col-span-5 flex flex-col gap-6 overflow-hidden">

            <!-- STANDBY DRIVER ROTATION LIST -->
            <div class="flex-1 bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-white flex items-center gap-2">
                        <i class="fas fa-list-ol text-emerald-400"></i>
                        Giliran Driver Berikutnya (FIFO)
                    </h3>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-xs"
                          x-text="standbyDrivers.length + ' Siap'"></span>
                </div>

                <div class="flex-1 overflow-y-auto space-y-2.5 pr-1">
                    <template x-for="(att, idx) in standbyDrivers.slice(0, 6)" :key="att.id">
                        <div class="p-3 rounded-2xl border transition flex items-center justify-between"
                             :class="idx === 0 ? 'bg-emerald-950/60 border-emerald-500/50 text-white' : 'bg-slate-800/60 border-slate-700/60 text-slate-300'">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg font-heading font-black text-xs flex items-center justify-center shrink-0"
                                      :class="idx === 0 ? 'bg-emerald-500 text-slate-950 font-black' : 'bg-slate-700 text-slate-300'"
                                      x-text="'#' + (idx + 1)"></span>
                                <div>
                                    <span class="font-bold text-sm block text-white" x-text="att.driver?.name"></span>
                                    <span class="text-xs text-slate-400" x-text="att.driver?.motorcycle_model"></span>
                                </div>
                            </div>
                            <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-amber-300"
                                  x-text="att.driver?.license_plate"></span>
                        </div>
                    </template>

                    <template x-if="standbyDrivers.length === 0">
                        <div class="text-center py-8 text-slate-500 text-xs">
                            Semua driver sedang dalam perjalanan.
                        </div>
                    </template>
                </div>
            </div>

            <!-- WAITING PASSENGERS QUEUE -->
            <div class="h-56 bg-slate-900/90 rounded-3xl p-6 border border-slate-800 shadow-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                    <h3 class="font-heading font-extrabold text-base text-white flex items-center gap-2">
                        <i class="fas fa-user-clock text-amber-400"></i>
                        Antrean Penumpang Menunggu
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold text-xs"
                          x-text="waitingTickets.length + ' Orang'"></span>
                </div>

                <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                    <template x-for="(ticket, idx) in waitingTickets.slice(0, 5)" :key="ticket.id">
                        <div class="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-mono font-bold text-amber-300" x-text="ticket.ticket_number"></span>
                                <span class="text-slate-300 font-medium ml-2" x-text="ticket.passenger_name"></span>
                            </div>
                            <span class="text-[11px] text-slate-400" x-text="ticket.route?.route_name"></span>
                        </div>
                    </template>

                    <template x-if="waitingTickets.length === 0">
                        <div class="text-center py-6 text-slate-500 text-xs">
                            Tidak ada antrean penumpang yang menunggu.
                        </div>
                    </template>
                </div>
            </div>

        </section>

    </main>

    <!-- TV Display Scripts -->
    <script>
        function tvDisplay() {
            return {
                currentTime: '',
                currentDate: '',
                audioEnabled: false,
                lastAnnouncedTicketId: null,

                latestCalled: {{ Js::from($latest_called) }},
                standbyDrivers: {{ Js::from($standby_drivers) }},
                waitingTickets: {{ Js::from($waiting_tickets) }},

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // Polling real-time updates every 3 seconds
                    setInterval(() => this.fetchDisplayData(), 3000);
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                toggleAudio() {
                    this.audioEnabled = !this.audioEnabled;
                    if (this.audioEnabled) {
                        this.speak("Suara panggilan antrean ojek aktif.");
                    }
                },

                speak(text) {
                    if (!this.audioEnabled || !('speechSynthesis' in window)) return;
                    window.speechSynthesis.cancel(); // Cancel any existing speech
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.95;
                    utterance.pitch = 1.0;
                    window.speechSynthesis.speak(utterance);
                },

                announceCalledTicket(ticket) {
                    if (!ticket || !this.audioEnabled) return;
                    if (ticket.id === this.lastAnnouncedTicketId) return;

                    this.lastAnnouncedTicketId = ticket.id;

                    const announcement = `Panggilan nomor antrean ${ticket.ticket_number}. Atas nama ${ticket.passenger_name}. Menuju ${ticket.route?.route_name || 'tujuan'}. Silakan menuju pangkalan bersama driver ${ticket.driver?.name || 'resmi'}, nomor plat ${ticket.driver?.license_plate || ''}.`;
                    this.speak(announcement);
                },

                async fetchDisplayData() {
                    try {
                        const res = await fetch('{{ route("ojek.public.api.display") }}');
                        if (res.ok) {
                            const data = await res.json();
                            const prevCalledId = this.latestCalled?.id;
                            this.latestCalled = data.latest_called;
                            this.standbyDrivers = data.standby_drivers;
                            this.waitingTickets = data.waiting_tickets;

                            // If a new ticket is called, announce it via voice!
                            if (this.latestCalled && this.latestCalled.id !== prevCalledId) {
                                this.announceCalledTicket(this.latestCalled);
                            }
                        }
                    } catch (e) {
                        console.error('TV Fetch error:', e);
                    }
                },

                formatTime(isoString) {
                    if (!isoString) return '-';
                    const d = new Date(isoString);
                    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }
            };
        }
    </script>
</body>
</html>
