<?php

namespace gift\appli\core\application\auth;

use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\domain\entities\User;

class AuthService implements AuthServiceInterface
{

    /**
     * @throws ExceptionDatabase
     */
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

    /**
     * @throws ExceptionDatabase
     * @throws AuthentificationException
     */
    public function loginByCredential(string $username, string $password): string
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $user = User::where('user_id', $username)
                ->first();
            if($user === null) {
                throw new ExceptionDatabase('Utilisateur non trouvé');
            }
            if(!password_verify($password, $user->password)) {
                throw new AuthentificationException('Mot de passe incorrect');
            }
            return $user->id;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionDatabase('Erreur de base de donnée: ' . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionDatabase
     */
    public function getUserById(string $userId): array
    {
        try {
            $user = User::find($userId);
            if ($user === null) {
                throw new ExceptionDatabase('Utilisateur non trouvé');
            }
            return $user->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionDatabase('Erreur de base de donnée: ' . $e->getMessage());
        }
    }
}