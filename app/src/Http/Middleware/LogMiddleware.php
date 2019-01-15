<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 30/12/16
 * Time: 19:49
 */

namespace App\Http\Middleware;


use App\Facilitator\App\SessionFacilitator;
use App\Facilitator\Database\DatabaseFacilitator;
use App\Mapper\Log;
use Monolog\Logger;

class LogMiddleware
{
    private $arrayLevelMonolog = [
        'DEBUG' => Logger::DEBUG,
        'INFO' => Logger::INFO,
        'NOTICE' => Logger::NOTICE,
        'WARNING' => Logger::WARNING,
        'ERROR' => Logger::ERROR,
        'CRITICAL' => Logger::CRITICAL,
        'ALERT' => Logger::ALERT,
        'EMERGENCY' => Logger::EMERGENCY
    ];

    private function castArray($arrayElement) {
        if ($arrayElement == null)
            return "";

        $stringReturn = "";

        foreach ($arrayElement as $key => $elem) {
            $stringReturn .= $key . '=' . $elem . ',';
        }

        return substr($stringReturn, 0, strlen($stringReturn) - 1);
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     * @return mixed
     * @throws \Exception
     */
    function __invoke($request, $response, $next)
    {
        if (!isset($_SESSION))
            session_start();
        $route = $request->getAttribute('route');

        if ($route == null) {
            throw new \Exception('Route not defined');
        }

        $arrayInfo = explode(':', $route->getCallable());

        $classReflection = new \ReflectionClass($arrayInfo[0]);

        $arrayLog = [];
        foreach ($classReflection->getMethods() as $method) {
            if ($method->getName() === $arrayInfo[1]) {
                preg_match('/@Log\s*\(([^)]+)\)/', $method->getDocComment(), $arrayLog);
                break 1;
            }
        }

        if (count($arrayLog)) {
            //Type
            preg_match('/type\s{0,}=\s{0,}["\']([^\'"]*)["\']/', $arrayLog[1], $arrayType);

            //Persist
            preg_match('/persist\s{0,}=\s{0,}\{(.*?)\}/', $arrayLog[1], $arrayPersist);

            //Message
            preg_match('/message\s{0,}=\s{0,}["\']([^\'"]*)["\']/', $arrayLog[1], $arrayMessage);

            if (!(count($arrayType) && count($arrayPersist) && count($arrayMessage))) {
                throw new \Exception('Annotation bad formated. Look controller ' . $route->getCallable() .
                    '. Example: @Log(type="INFO", persist={"verb", "session", "attributes"}, message="User authenticate in system")');
            }

            $type = $arrayType[1];
            $message = $arrayMessage[1];

            if (!key_exists($type, $this->arrayLevelMonolog))
            {
                throw new \Exception('Annotation bad formated. Look controller ' . $route->getCallable() . '. Type LOG (INFO, CRITICAL, NOTICE...).');
            }

            $arrayPersist = explode(",", trim($arrayPersist[1]));
            $logPersist = "";

            $log = new Log();
            foreach ($arrayPersist as $persist) {
                switch (strtolower(str_replace('"', "",trim($persist)))) {
                    case "verb":
                        $logPersist .= 'VERB#' . $request->getMethod() . '#';
                        break;
                    case "attributes":
                        $logPersist .= 'REQUESTATTR#' . $this->castArray($request->getParams()) . '#';
                        break;
                    case "session":
                        $sessionAttribute = SessionFacilitator::getAttributeSession();
                        $logPersist .= 'SESSIONATTR#' . $this->castArray($sessionAttribute);
                        if (($sessionAttribute !== null) && (key_exists('id', $sessionAttribute))) {
                            $log->setIdUser($sessionAttribute['id']);
                        }
                        break;
                }
            }


            $log->setLevel($this->arrayLevelMonolog[$type]);
            $log->setMessage($message . ': ' . $logPersist);
            $log->setDate(new \DateTime('now'));

            try {
                $dm = DatabaseFacilitator::getConnection();
                $dm->persist($log);
                $dm->flush();
            } catch(\Exception $ex) {
                throw $ex;
            }
        }

        return $next($request, $response);
    }


}