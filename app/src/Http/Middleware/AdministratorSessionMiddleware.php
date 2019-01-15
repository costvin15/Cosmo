<?php

namespace App\Http\Middleware;

use App\Facilitator\App\ContainerFacilitator;
use RKA\Session;
use App\Facilitator\App\SessionFacilitator;

class AdministratorSessionMiddleware {
    public function __invoke($request, $response, $next){
        $container = ContainerFacilitator::getContainer();
        $session = new Session();
        $current_session = $session->get("cosmo-session");
        $router = $container->get("router");
        
        if ($current_session && count($current_session) == 0){
            if ($request->isXhr())
                return $response->withStatus(500, utf8_decode("Sessião expirada. Atualize a página!"));
            return $response->withRedirect($router->pathFor("login.index"));
        }

        $attributes = SessionFacilitator::getAttributeSession();
        if (!$attributes)
            return $response->withRedirect($router->pathFor("login.index"));
        if (!$attributes["administrator"])
            return $response->withRedirect($router->pathFor("dashboard.index"));

        $response = $next($request, $response);
        return $response;
    }
}