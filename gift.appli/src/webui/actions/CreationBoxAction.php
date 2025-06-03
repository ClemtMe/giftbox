<?php
namespace gift\appli\webui\actions;
use gift\appli\core\application\usecases\BoxManagement;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreationBoxAction
{
    private string $template;
    private BoxManagement $boxManagement;

    public function __construct()
    {
        $this->template = 'pages/CreationBox.twig';
        $this->boxManagement = new BoxManagement();
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!isset($_SESSION['user_id'])) {
            $response->getBody()->write('Utilisateur non connecté');
            return $response->withStatus(403);
        }
        $queryParams = $request->getQueryParams();
        try {
            $boxId = $this->boxManagement->createEmptyBox(
                $_SESSION['user_id'],
                $queryParams['libelle'],
                $queryParams['description'],
                isset($queryParams['is_gift']),
                $queryParams['gift_message'] ?? ''
            );
            $_SESSION['current_box_id'] = $boxId;
            return $response->withHeader('Location', '/box/view')->withStatus(302);
        } catch (\Exception $e) {
            return $response->withStatus(500)->write('Error: ' . $e->getMessage());
        }
    }
}