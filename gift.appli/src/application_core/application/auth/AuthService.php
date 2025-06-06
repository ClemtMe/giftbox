<?php

namespace gift\appli\core\application\auth;

use gift\appli\core\application\exceptions\AuthentificationException;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionInterne;
use gift\appli\core\domain\entities\User;
use Ramsey\Uuid\Uuid;

class AuthService implements AuthServiceInterface
{

    /**
     * @throws ExceptionInterne
     * @throws AuthentificationException
     */
    public function register(string $username, string $password): string
    {

        $user = User::where('user_id', $username)
            ->first();
        if ($user !== null) {
            throw new AuthentificationException('Utilisateur déjà existant');
        }
        $password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
        try {
            $user = new User();
            $user->id = Uuid::uuid4()->toString();
            $user->user_id = $username;
            $user->password = $password;
            $user->role = 1;
            $user->save();
            return $user->id;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionInterne('Erreur de base de donnée: ' . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionInterne
     * @throws AuthentificationException
     */
    public function loginByCredential(string $username, string $password): string
    {
        try {
            $user = User::where('user_id', $username)
                ->first();
            if($user === null) {
                throw new ExceptionInterne('Utilisateur non trouvé');
            }
            if(!password_verify($password, $user->password)) {
                throw new AuthentificationException('Mot de passe incorrect');
            }
            return $user->id;
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionInterne('Erreur de base de donnée: ' . $e->getMessage());
        }
    }

    /**
     * @throws ExceptionInterne
     * @throws EntityNotFoundException
     */
    public function getUserById(string $userId): array
    {
        try {
            $user = User::find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('Utilisateur non trouvé');
            }
            return [
                'id' => $user->id,
                'username' => $user->user_id,
                'role' => $user->role
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            throw new ExceptionInterne('Erreur de base de donnée: ' . $e->getMessage());
        }
    }
}