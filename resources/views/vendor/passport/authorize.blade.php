<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authorize App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CDN biar cepat --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-lg w-full">

        <h1 class="text-2xl font-bold text-gray-900 mb-4">
            Otorisasi Aplikasi
        </h1>

        <p class="text-gray-700 mb-6">
            Aplikasi <span class="font-semibold">{{ $client->name }}</span> meminta izin untuk mengakses akun <span class="font-bold">{{ $user->email }}</span>.
        </p>

        {{-- Scopes --}}
        @if (count($scopes) > 0)
            <p class="text-gray-800 font-medium mb-2">Aplikasi ini akan dapat:</p>
            <ul class="list-disc ml-6 mb-6 text-gray-700">
                @foreach ($scopes as $scope)
                    <li>{{ $scope->description }}</li>
                @endforeach
            </ul>
        @endif

        <div class="flex gap-4">
            {{-- Approve --}}
            <form method="post" action="{{ route('passport.authorizations.approve') }}" class="w-1/2">
                @csrf
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                    Izinkan
                </button>
            </form>

            {{-- Deny --}}
            <form method="post" action="{{ route('passport.authorizations.deny') }}" class="w-1/2">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">

                <button
                    type="submit"
                    class="w-full bg-gray-400 hover:bg-gray-500 text-white py-2 rounded-lg transition">
                    Tolak
                </button>
            </form>
        </div>

    </div>
</div>
</body>
</html>
