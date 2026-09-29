<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Admin Login — Nautivance')] class extends Component
{
    #[Validate('required|email')]
    public string $email = '';
 
    #[Validate('required|string')]
    public string $password = '';
 
    public bool $remember = false;
 
    public function login()
    {
        $this->validate();
 
        $key = Str::lower($this->email).'|'.request()->ip();
 
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }
 
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key);
 
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }
 
        RateLimiter::clear($key);
        session()->regenerate();
 
        return $this->redirectIntended('/admin/dashboard', navigate: true);
    }
};
?>

<div class="flex min-h-screen items-center justify-center bg-mist px-5 py-12 text-ink">
    <div class="w-full max-w-md">

        <a href="/" class="mb-8 flex items-center justify-center gap-3">
            <img src="{{ asset('assets/images/nautivance-favicon.png') }}" alt="Nautivance logo" class="size-12 object-contain">
            <span class="font-serif text-2xl font-extrabold tracking-wider text-navy">NAUTIVANCE</span>
        </a>
        
        <div class="rounded-2xl border border-[#e4eaf0] bg-white p-8 shadow-[0_8px_25px_rgba(11,35,60,.05)]">
            <h1 class="font-serif text-3xl leading-tight text-navy">Admin Login</h1>
            <p class="mt-1 text-sm text-muted">Masuk untuk melanjutkan ke dashboard.</p>

            <form wire:submit="login" class="mt-7 space-y-5" novalidate>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-[#33465c]">Email</label>
                    <input id="email" type="email" wire:model="email" required autofocus
                           autocomplete="username" inputmode="email" placeholder="admin@nautivance.com"
                           class="w-full rounded-lg border px-4 py-3.5 text-[15px] outline-none transition
                                  focus:border-ocean focus:ring-4 focus:ring-ocean/15
                                  @error('email') border-red-400 @else border-[#cbd6e2] @enderror">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">

                    <label for="password" class="mb-1.5 block text-sm font-semibold text-[#33465c]">
                        Kata Sandi
                    </label>
                
                    <div class="relative">
                        <input id="password"
                               wire:model="password"
                               required
                               :type="show ? 'text' : 'password'"
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-lg border px-4 py-3.5 pr-12 text-[15px] outline-none transition
                                      focus:border-ocean focus:ring-4 focus:ring-ocean/15
                                      @error('password') border-red-400 @else border-[#cbd6e2] @enderror">
                
                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-ocean hover:text-navy"
                                :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                
                            <x-lucide-eye x-show="!show" class="size-5"/>
                            <x-lucide-eye-off x-show="show" class="size-5"/>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="login"
                        class="w-full rounded-lg bg-gold px-5 py-3.5 font-extrabold text-ink transition
                               hover:brightness-95 focus:outline-none focus:ring-4 focus:ring-gold/40
                               disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="login">Login</span>
                    <span wire:loading wire:target="login">Memproses…</span>
                </button>
            </form>
        </div>
    </div>
</div>