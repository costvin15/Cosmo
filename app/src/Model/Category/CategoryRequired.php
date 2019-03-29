<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;
use App\Mapper\Star;
use App\Mapper\User;


class CategoryRequired implements InterfaceCategory{

    public function check($historyActivities,$star,$user){
        $count = 0;
        foreach($historyActivities as $history){
            if($history->getActivity()->getCategory() == InterfaceCategory::REQUIRED){
                $count++;
            }
        }
        if($count >= 3 && !$star->getCompleted())
            return true;       
        return false;
    }

}