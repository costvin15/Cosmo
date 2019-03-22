<?php

namespace App\model\Category;

use App\Mapper\HistoryActivities;


class CategoryAgainstTime implements InterfaceCategory{


    public function check(HistoryActivities $historyActivities){
        $count = 0;
        foreach($historyActivities as $history){
            if($history->getActivity()->getCategory() == AGAINST_TIME){
                $count++;
            }
        }
        if($count >= 3)
            return true;
        
        return false;
    }

}