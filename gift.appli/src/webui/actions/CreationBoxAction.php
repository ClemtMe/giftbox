<?php
namespace gift\appli\webui\actions;
use gift\appli\core\application\exceptions\EntityNotFoundException;
use gift\appli\core\application\exceptions\ExceptionDatabase;
use gift\appli\core\application\usecases\BoxManagement;
use gift\appli\webui\exceptions\ProviderAuthentificationException;
use gift\appli\webui\providers\AuthProviderInterface;
use gift\appli\webui\providers\CsrfTokenProviderInterface;
use gift\appli\webui\providers\SessionAuthProvider;
use gift\appli\webui\providers\SessionCsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreationBoxAction
{
    private string $template;
    private BoxManagement $boxManagement;
    private AuthProviderInterface $authProvider;
    private CsrfTokenProviderInterface $csrfTokenProvider;

    public function __construct()
    {
        $this->template = 'pages/CreationBox.twig';
        $this->boxManagement = new BoxManagement();
        $this->authProvider = new SessionAuthProvider();
        $this->csrfTokenProvider = new SessionCsrfTokenProvider();
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $csrfToken = $params['csrf_token'] ?? '';
            $boxName = $params['name'] ?? '';
            $boxDescription = $params['description'] ?? '';
            $isGift = $params['is_gift'] ?? false;
            $gift = $params['gift_message'] ?? '';

            // Vérifier le token CSRF
            try {
                $this->csrfTokenProvider->checkCsrf($csrfToken);
            } catch (\gift\appli\webui\exceptions\CsrfException $e) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Token CSRF invalide.");
            }

            // Vérifier que le nom de la box n'est pas vide
            if (empty($boxName) || FILTER_VAR($boxName, FILTER_SANITIZE_SPECIAL_CHARS) === false) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Le nom de la box ne peut pas être vide.");
            }
            // Vérifier que la description de la box n'est pas vide
            if (empty($boxDescription) || FILTER_VAR($boxDescription, FILTER_SANITIZE_SPECIAL_CHARS) === false) {
                throw new \Slim\Exception\HttpBadRequestException($request, "La description de la box ne peut pas être vide.");
            }
            // Vérifier que le message de cadeau n'est pas vide si la box est un cadeau
            if ($isGift && (empty($gift) || FILTER_VAR($gift, FILTER_SANITIZE_SPECIAL_CHARS) === false)) {
                throw new \Slim\Exception\HttpBadRequestException($request, "Le message de cadeau ne peut pas être vide.");
            }

            try {
                $user = $this->authProvider->getSignedInUser();
            } catch (ProviderAuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request, "Authentification échouée : " . $e->getMessage());
            }

            // Créer la box
            try {
                $boxid = $this->boxManagement->createEmptyBox($user['id'], $boxName, $boxDescription, $isGift, $gift);
            } catch (EntityNotFoundException $e) {
                throw new \Slim\Exception\HttpNotFoundException($request, "L'utilisateur n'a pas été trouvé.");
            } catch (ExceptionDatabase $e) {
                throw new \Slim\Exception\HttpInternalServerErrorException($request, "Erreur lors de la création de la box : " . $e->getMessage());
            }

            // Ajouter un message flash de succès
            $request = $request->withAttribute('flash_message', 'Box créée avec succès !');

            $_SESSION['box'] = $boxid;

            // Rediriger vers la page d'accueil
            $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader('Location', $routeParser->urlFor('home'))->withStatus(302);
        }else{
            // Générer un token CSRF
            $csrfToken = $this->csrfTokenProvider->generateCsrf();

            // Récupérer l'utilisateur connecté
            try {
                $user = $this->authProvider->getSignedInUser();
            } catch (ProviderAuthentificationException $e) {
                throw new \Slim\Exception\HttpUnauthorizedException($request, "Authentification échouée : " . $e->getMessage());
            }

            // Rendre la vue de création de box
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render($response, $this->template, [
                'csrf_token' => $csrfToken,
            ]);
        }
    }
}