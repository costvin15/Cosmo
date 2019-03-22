<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;
use App\Mapper\Star;
use Documents\User;


interface InterfaceCategory{

    const AGAINST_TIME = "Contra o tempo";
    const REQUIRED = "Obrigatória";
    const CHALLENGE = "Desafio";
    const CATEGORIES = [InterfaceCategory::AGAINST_TIME,InterfaceCategory::REQUIRED,InterfaceCategory::CHALLENGE];

    public function check($historyActivities, $star, $user);

}