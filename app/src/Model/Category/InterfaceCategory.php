<?php

namespace App\Model\Category;

interface InterfaceCategory{

    const AGAINST_TIME = "Contra o tempo";
    const REQUIRED = "Obrigatória";
    const CHALLENGE = "Desafio";
    const CATEGORIES = [InterfaceCategory::AGAINST_TIME,InterfaceCategory::REQUIRED,InterfaceCategory::CHALLENGE];

    public function check($historyActivities);

}