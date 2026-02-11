<x-layouts.customer :title="'Set your password – Partyhelp'">
    <div class="max-w-md mx-auto">
        <h1 class="mb-2 text-xl font-semibold text-white">Set your venue portal password</h1>
        <p class="mb-6 text-gray-400">Choose a password to sign in to the Partyhelp venue portal.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-900/30 text-red-300 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('venue.set-password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}" />
            <input type="hidden" name="email" value="{{ $email }}" />

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" id="password" required autofocus autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 text-white px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-600 bg-gray-800 text-white px-3 py-2 focus:ring-2 focus:ring-violet-500 focus:border-transparent" />
            </div>

            <button type="submit" class="primary-cta-button w-full py-2.5 rounded-lg font-medium">
                Set password and sign in
            </button>
        </form>

        <p class="mt-4 text-xs text-gray-500">This link expires in 60 minutes. After setting your password you will be redirected to sign in.</p>
    </div>
</x-layouts.customer>
