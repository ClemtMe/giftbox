<?php

namespace gift\appli\core\application\auth;

use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\User;

class AuthService implements AuthServiceInterface
{

    public function register(string $username, string $password): string
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user = new User();
            $user->user_id = $username;
            $user->password = $password;
            $user->role = 1;
            $user->save();
            return $user->id;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionDatabase('Erreur de base de donnée: ' . $e->getMessage());
        }
    }

    public function loginByCredential(string $username, string $password): bool
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user = User::where('user_id', $username)
                ->first();
            if($user === null) {
                return false;
            }
            if(!password_verify($password, $user->password)) {
                return false;
            }
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionDatabase('Erreur de base de donnée: ' . $e->getMessage());
        }
    }
}