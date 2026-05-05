<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche la page de connexion.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    /**
     * Traite le formulaire de connexion.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        return back()
            ->withErrors(['email' => 'Identifiants incorrects. Veuillez réessayer.'])
            ->onlyInput('email');
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Redirige vers le dashboard selon le rôle.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'postulant'       => redirect()->route('postulant.dashboard'),
            'examiner'        => redirect()->route('examiner.dashboard'),
            'president-sub'   => redirect()->route('subcommission.dashboard'),
            'president-council' => redirect()->route('president.dashboard'),
            'admin'           => redirect()->route('admin.users'),
            default           => redirect()->route('login'),
        };
    }
}
