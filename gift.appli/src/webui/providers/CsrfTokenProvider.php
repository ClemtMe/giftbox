<?php
namespace gift\appli\webui\providers;

class CsrfTokenProvider{
    static function generate(){
        $token = md5(uniqid(mt_rand(), true));
        $_SESSION['token'] = $token;
        return $token;
    }

    /**
     * @throws \Exception
     */
    static function check($token){
        if($token != $_SESSION['token']){
            unset($_SESSION['token']);
            throw new \Exception("Token invalide");
        }
    }
}