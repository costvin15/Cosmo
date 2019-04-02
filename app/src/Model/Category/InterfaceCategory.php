<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;
use App\Mapper\Star;
use Documents\User;


interface InterfaceCategory{

    const AGAINST_TIME = "Contra o tempo";
    const REQUIRED = "Obrigatória";
    const CHALLENGE = "O Investidor";
    const PVP = "PvP";
    const CATEGORIES = [InterfaceCategory::AGAINST_TIME,InterfaceCategory::REQUIRED,InterfaceCategory::CHALLENGE, InterfaceCategory::PVP];

    public function check($historyActivities, $star, $user);

}