<?php

namespace App\Model\Category;

use App\Mapper\HistoryActivities;
use App\Mapper\Star;
use App\Mapper\User;


class CategoryRequired implements InterfaceCategory {
    public function check($historyActivities,$star,$user){
        if ($star->getCompleted())
            return false;
        $count = 0;
        foreach($historyActivities as $history){
            if($history->getActivity()->getCategory() == InterfaceCategory::REQUIRED)
                $count++;
        }
        if($count >= 3)
            return true;
        return false;
    }

}