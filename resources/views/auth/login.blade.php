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
                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" placeholder="name@university.edu" class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-slate-700">Password</label>
                            <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" placeholder="••••••••" class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <label for="remember" class="ml-2 text-sm text-slate-500 italic">Remember me for 30 days</label>
                    </div>

                    <button type="button" onclick="location.href='/dashboard'" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]">
                        Sign In
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-slate-500">
                    Don't have an account? <a href="#" class="font-bold text-indigo-600 hover:text-indigo-700">Contact Admin</a>
                </div>
            </div>
            
            <p class="mt-10 text-center text-xs text-slate-400 uppercase tracking-widest font-bold">Scientific Council &copy; 2026</p>
        </div>
    </div>
</body>
</html>
