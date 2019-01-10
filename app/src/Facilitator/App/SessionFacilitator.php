<?php

namespace App\Facilitator\App;

use RKA\Session;

class SessionFacilitator
{

    public static function getSession() {
        return new Session();
    }

    public static function getAttributeSession() {
        $session = new Session();
        $ci = ContainerFacilitator::getContainer();

        $arraySettings = $ci->get('settings');
        return $session->get($arraySettings['session']['name']);
    }

    public static function destroy() {
        $session = new Session();
        $session->clearAll();
    }

}