<x-guest-layout>
    <!-- Session Status -->

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="formOnline">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3" id="botonEnviarLogin">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Solo mientras la inscripción está abierta. --}}
    @if (! empty($inscripcionAbierta))
        <div class="mt-6 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
            <p class="font-semibold">¿Querés ser guardavidas en la temporada {{ $inscripcionAbierta->nombre }}?</p>
            <p class="mt-1">
                La inscripción está abierta hasta el {{ $inscripcionAbierta->fecha_fin_postulacion->format('d/m/Y') }}.
                <a href="{{ route('register') }}" class="font-semibold underline hover:text-sky-700">Registrate para postularte</a>.
            </p>
        </div>
    @endif

    <div id="error" class="mt-3 text-sm font-semibold text-red-600"></div>

    <div id="contenedorOffline" style="display: none;">
        <input type="text" id="nombre" placeholder="Nombre del guardavidas">

        <button id="btnWhatsapp" style="display: none;">
            Enviar registro por WhatsApp
        </button>

        <p id="estado"></p>
    </div>

</x-guest-layout>
<script>
    const BASE_URL = "{{ url('/') }}";
</script>
 <script src="{{ asset('js/loginIdUserOffline.js') }}"></script>
 <script src="{{ asset('js/pantallaOffline.js') }}"></script>
