<x-guest-layout>
    <form method="POST" action="{{ route('registrar') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nombre" value="Nombre" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required
                autofocus autocomplete="nombre" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- Apellido -->
        <div class="mt-4">
            <x-input-label for="apellido" value="Apellido" />
            <x-text-input id="apellido" class="block mt-1 w-full" type="text" name="apellido" :value="old('apellido')"
                required autofocus autocomplete="apellido" />
            <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
        </div>

        <div class="md:flex md:flex-auto md:justify-between md:gap-2">
            <!-- Documento  -->
            <div class="mt-4 md:flex md:flex-col w-full">
                <x-input-label for="documento" value="Documento" />
                <x-text-input id="documento" class="block mt-1 w-full" type="text" name="documento" :value="old('documento')"
                    required autofocus autocomplete="documento" maxlength="8" />
                <x-input-error :messages="$errors->get('documento')" class="mt-2" />
            </div>

            <!-- CUIT/CUIL  -->
            <div class="mt-4 md:flex md:flex-col w-full">
                <x-input-label for="cuit_cuil" value="CUIT/CUIL" />
                <x-text-input id="cuit_cuil" class="block mt-1 w-full" type="text" name="cuit_cuil" :value="old('cuit_cuil')"
                    required autofocus autocomplete="cuit_cuil" maxlength="13" />
                <x-input-error :messages="$errors->get('cuit_cuil')" class="mt-2" />
            </div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar Contraseña" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('iniciar-sesion') }}">
                {{ 'Ya estas registrado?' }}
            </a>

            <x-primary-button class="ml-4">
                {{ 'Registrarte' }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
