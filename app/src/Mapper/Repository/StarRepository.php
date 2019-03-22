<?php

namespace App\Mapper\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Mapper\GroupActivities;
use App\Mapper\Star;
use App\Mapper\CategoryActivities;
use App\Mapper\User;

class StarRepository extends DocumentRepository
{
    public function findStar(User $user, GroupActivities $group,CategoryActivities $category) {
        return $this->dm->getRepository(Star::class)->findOneBy(['user' => $user, 'groupActivities' => $group,"categoryActivities" => $category]);
    }
}