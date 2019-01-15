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
        try {
            $_ci = ContainerFacilitator::getContainer();
            $session = new Session();
            $arraySession = $session->get('cosmo-session');
            $router = $_ci->get('router');
            
            if ($arraySession && count($arraySession) == 0) {
                if ($request->isXhr()){
                    return $response->withStatus(500, utf8_decode('Sessão expirada. Atualize a página!'));
                }

                return $response->withRedirect($router->pathFor('login.index'));
            }

            $response = $next($request, $response);
            return $response;
        } catch (\Exception $e){
            return $response->withRedirect($router->pathFor('login.index'));            
        }
    }

}