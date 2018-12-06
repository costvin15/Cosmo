<?php

namespace App\Mapper\Repository;


use App\Mapper\Activities;
use App\Mapper\AttemptActivities;
use App\Mapper\User;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Doctrine\ODM\MongoDB\Query\Query;

class AttemptActivitiesRepository extends DocumentRepository
{

    public function getNumberAttemptActivity(User $user, Activities $activities) {
        return count($this->dm->createQueryBuilder(AttemptActivities::class)
            ->field('user')->references($user)
            ->field('activity')->references($activities)
            ->getQuery()->execute());
    }

    public function getNumberAttemptActivityUser(User $user) {
        return count($this->dm->createQueryBuilder(AttemptActivities::class)
            ->field('user')->references($user)
            ->getQuery()->execute());
    }
}