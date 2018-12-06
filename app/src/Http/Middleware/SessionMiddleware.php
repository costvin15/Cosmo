<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 19/10/16
 * Time: 21:02
 */

namespace App\Http\Middleware;


use App\Facilitator\App\ContainerFacilitator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\Session;

class SessionMiddleware
{

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {

        $_ci = ContainerFacilitator::getContainer();

        $session = new Session();
        $arraySession = $session->get('cosmo-session');
        if (count($arraySession) == 0) {
            if ($request->isXhr()) {
                return $response->withStatus(500, utf8_decode('Sessão expirou. Atualize a página!'));
            }

            $router = $_ci->get('router');
            return $response->withRedirect($router->pathFor('login.index'));
        }
        $response = $next($request, $response);
        return $response;
    }

}