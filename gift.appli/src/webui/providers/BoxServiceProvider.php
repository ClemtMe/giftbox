<?php
namespace gift\appli\webui\providers;

use gift\appli\core\application\exceptions\InvalidTokenException;
use gift\appli\core\application\exceptions\TokenMissingException;
use gift\appli\core\application\usecases\BoxAcces;
use gift\appli\core\application\usecases\BoxInterface;
use gift\appli\core\domain\exceptions\EntityNotFoundException;
use gift\appli\webui\exceptions\BoxAccesException;
use Random\RandomException;

class BoxServiceProvider implements BoxServiceProviderInterface
{
    private BoxInterface $boxAcces;

    public function __construct()
    {
        $this->boxAcces = new BoxAcces();
    }

    /**
     * @throws BoxAccesException
     */
    public function getBoxByToken(string $token): array
    {
        try {
            $box = $this->boxAcces->accesBoxByToken($token);
        } catch ( TokenMissingException | InvalidTokenException | EntityNotFoundException $e) {
            throw new BoxAccesException("Erreur lors de la récupération de la box: " . $e->getMessage());
        }
        return $box;
    }

    /**
     * @throws BoxAccesException
     */
    public function generateBoxAccesLink(string $boxid): string
    {
        try {
            $token = base64_encode(random_bytes(32));
        } catch (RandomException $e) {
            throw new BoxAccesException("Erreur lors de la génération du token: " . $e->getMessage());
        }
        try {
            $this->boxAcces->setBoxToken($boxid, $token);
        } catch (\Exception $e) {
            throw new BoxAccesException("Erreur lors de la sauvegarde du token: " . $e->getMessage());
        }
        return urlencode($token);
    }
}
