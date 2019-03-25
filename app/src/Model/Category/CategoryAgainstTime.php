<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;


class CategoryAgainstTime implements InterfaceCategory{


    public function check($historyActivities,$star,$user){
        $count = 0;
        foreach($historyActivities as $history){
            if($history->getActivity()->getCategory() == InterfaceCategory::AGAINST_TIME){
                $count++;
            }
        }
        if($count >= 3 && !$star->getCompleted())
            return true;
        
        return false;
    }

}