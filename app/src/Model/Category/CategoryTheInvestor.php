<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;


class CategoryTheInvestor implements InterfaceCategory{


    public function check($historyActivities,$star,$user){
        $count = 0;
        foreach($historyActivities as $history){
            if($history->getActivity()->getCategory() == InterfaceCategory::CHALLENGE){
                $count++;
            }
        }
        if($count >= 1 && !$star->getCompleted())
            return true;
        
        return false;
    }

}