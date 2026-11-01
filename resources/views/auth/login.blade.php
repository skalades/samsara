<x-layouts.app :title="'Login Staf & Petugas'">
    <div class="min-h-[75vh] flex flex-col justify-center py-6 sm:px-6 lg:px-8" x-data="loginForm()">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div class="inline-flex p-3 rounded-2xl bg-[#1E3A2B] shadow-xl border border-[#2D5841] mb-4">
                <img src="/LOGO.jpeg" alt="SAMSARA Logo" class="h-14 w-14 rounded-xl object-cover ring-2 ring-[#D97736]/70">
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold font-heading text-slate-900 tracking-tight">
                Portal Staf & Petugas
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Sistem Manajemen Terintegrasi Basecamp Gunung Cikuray 2.821 MDPL
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-5 shadow-xl sm:rounded-2xl border border-slate-200/80 sm:px-10">
                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Alamat Email Staf
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input type="email" name="email" id="email" required autofocus
                                x-model="email"
                                value="{{ old('email') }}"
                                placeholder="nama@samsara.id"
                                class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] focus:border-[#1E3A2B] text-sm @error('email') border-rose-500 focus:ring-rose-500 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                x-model="password"
                                placeholder="••••••••"
                                class="block w-full pl-10 pr-10 py-3 border border-slate-300 rounded-xl leading-5 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A2B] focus:border-[#1E3A2B] text-sm @error('password') border-rose-500 focus:ring-rose-500 @enderror">
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1E3A2B] focus:ring-[#1E3A2B] border-slate-300">
                            <span class="text-xs text-slate-600">Ingat sesi perangkat ini</span>
                        </label>
                        <span class="text-xs text-slate-400">Security SSL 256-bit</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-[#1E3A2B] hover:bg-[#162B20] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1E3A2B] transition active:scale-[0.99]">
                        <i class="fas fa-right-to-bracket text-[#D97736]"></i>
                        Masuk ke Dashboard Staf
                    </button>
                </form>

                <!-- DEMO QUICK LOGIN ACCORDION / PILLS (For Ease of Testing) -->
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider text-center mb-3">
                        ⚡ Quick Login Demo (Klik untuk Isi Instan)
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button type="button" @click="fill('admin@samsara.id', 'password')"
                            class="p-2.5 rounded-lg border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50 text-left transition flex flex-col">
                            <span class="font-bold text-slate-800">Superadmin</span>
                            <span class="text-[10px] text-slate-500">Command & Finansial</span>
                        </button>

                        <button type="button" @click="fill('kasir@samsara.id', 'password')"
                            class="p-2.5 rounded-lg border border-slate-200 hover:border-sky-600 hover:bg-sky-50 text-left transition flex flex-col">
                            <span class="font-bold text-slate-800">Kasir Basecamp</span>
                            <span class="text-[10px] text-slate-500">POS & Scan Pickup</span>
                        </button>

                        <button type="button" @click="fill('pos1@samsara.id', 'password')"
                            class="p-2.5 rounded-lg border border-slate-200 hover:border-orange-600 hover:bg-orange-50 text-left transition flex flex-col">
                            <span class="font-bold text-slate-800">Petugas Pos 1</span>
                            <span class="text-[10px] text-slate-500">Scanner EOD PWA</span>
                        </button>

                        <button type="button" @click="fill('sar@samsara.id', 'password')"
                            class="p-2.5 rounded-lg border border-slate-200 hover:border-rose-600 hover:bg-rose-50 text-left transition flex flex-col">
                            <span class="font-bold text-slate-800">Komandan SAR</span>
                            <span class="text-[10px] text-slate-500">Pos 4 & Safety Track</span>
                        </button>
                    </div>
                </div>

                <!-- Back to Public Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-[#1E3A2B] font-medium transition inline-flex items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Kembali ke Portal Publik Pendaki
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function loginForm() {
            return {
                email: '{{ old('email') }}',
                password: '',
                showPassword: false,
                fill(email, pass) {
                    this.email = email;
                    this.password = pass;
                }
            }
        }
    </script>
    @endpush
</x-layouts.app>
