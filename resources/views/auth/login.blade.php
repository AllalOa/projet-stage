<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SciCouncil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full">
            <div class="text-center mb-10">
                <div class="mx-auto w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-xl shadow-indigo-100 mb-4">SC</div>
                <h1 class="text-3xl font-bold text-slate-900">Welcome Back</h1>
                <p class="text-slate-500 mt-2">Sign in to Scientific Council CMS</p>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-10">

                {{-- Erreurs --}}
                @if ($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-rose-700 font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Message de succès (ex: après logout) --}}
                @if (session('status'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-700 font-medium">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="nom@universite.dz"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200' }} rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-bold text-slate-700">Mot de Passe</label>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" required
                                placeholder="••••••••"
                                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-slate-500 italic">Se souvenir de moi (30 jours)</label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i> Se Connecter
                    </button>
                </form>

                {{-- Comptes de démonstration --}}
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 text-center">Comptes de démonstration</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        @foreach([
                            ['role'=>'Postulant',    'email'=>'postulant@univ.dz'],
                            ['role'=>'Examinateur',  'email'=>'exam1@univ.dz'],
                            ['role'=>'Sous-Comm.',   'email'=>'subcomm@univ.dz'],
                            ['role'=>'Président',    'email'=>'president@univ.dz'],
                            ['role'=>'Admin',        'email'=>'admin@univ.dz'],
                        ] as $demo)
                        <button type="button"
                            onclick="document.getElementById('email').value='{{ $demo['email'] }}'; document.getElementById('password').value='123456789';"
                            class="text-left p-2 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg border border-slate-200 hover:border-indigo-200 transition-all font-medium text-slate-600">
                            <span class="block font-bold text-slate-800">{{ $demo['role'] }}</span>
                            <span class="text-slate-400">{{ $demo['email'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    <p class="text-center text-xs text-slate-400 mt-2">Mot de passe : <code class="bg-slate-100 px-1 rounded">123456789</code></p>
                </div>
            </div>
            
            <p class="mt-10 text-center text-xs text-slate-400 uppercase tracking-widest font-bold">Scientific Council &copy; 2026</p>
        </div>
    </div>
</body>
</html>
