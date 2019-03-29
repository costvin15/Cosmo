<?php

require __DIR__ . "/vendor/autoload.php";

$settings = require __DIR__ . "/app/settings.php";
$app = new \Slim\App($settings);

$container = $app->getContainer();
$container["database-settings"] = new \Slim\Collection(require __DIR__ . "/app/database.php");

$session_middleware_settings = $app->getContainer()->get("settings");
$session_middleware_settings = $session_middleware_settings["session"] ?: [];

require __DIR__ . "/app/middleware.php";
require __DIR__ . "/app/dependencies.php";

\Slim3\Annotation\Slim3Annotation::create($app, realpath(__DIR__ . "/app/src/Http/Controller"), realpath(__DIR__ . "/app/src/Http/Controller/Cache"));
\App\Facilitator\App\ContainerFacilitator::setApplication($app);

$app->add(new \RKA\SessionMiddleware($session_middleware_settings));