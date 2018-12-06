<?php

namespace App\Mapper\Repository;

use App\Mapper\Activities;
use App\Mapper\HistoryActivities;
use App\Mapper\User;
use Doctrine\ODM\MongoDB\DocumentRepository;

class HistoryActivitiesRepository extends DocumentRepository
{
    public function existHistoryForActivity(User $user, Activities $activities) {
        return count($this->dm->createQueryBuilder(HistoryActivities::class)
            ->field('user')->references($user)
            ->field('activity')->references($activities)
            ->getQuery()->execute());
    }

}