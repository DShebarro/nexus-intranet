<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Validator;
use App\Models\User;

class AuthController extends BaseController
{
    private User $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    public function showLogin(Request $req): void
    {
        $this->renderStandalone('auth/login', ['error' => null]);
    }

    public function login(Request $req): void
    {
        $data = [
            'email'    => $req->post('email'),
            'password' => $req->post('password'),
        ];

        $errors = Validator::validate($data, [
            'email'    => 'required|email',
            'password' => 'required|string|min:4',
        ]);

        if ($errors) {
            $this->renderStandalone('auth/login', [
                'error' => reset($errors)[0],
                'email' => $data['email'],
            ]);
            return;
        }

        $user = $this->user->findByEmail($data['email']);

        if (!$user || !$this->user->verifyPassword($data['password'], $user['password'])) {
            $this->renderStandalone('auth/login', [
                'error' => 'E-mail ou senha incorretos.',
                'email' => $data['email'],
            ]);
            return;
        }

        Auth::login($user);
        $this->log('auth', "Login: {$user['name']}");
        $this->redirect('/dashboard');
    }

    public function logout(Request $req): void
    {
        $user = Auth::user();
        if ($user) {
            $this->log('auth', "Logout: {$user['name']}");
        }
        Auth::logout();
        $this->redirect('/login');
    }

    public function profile(Request $req): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->json(['error' => 'Não autenticado.'], 401);
            return;
        }
        $this->json($user);
    }
}
