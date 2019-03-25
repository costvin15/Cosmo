<?php

namespace App\Mapper\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use App\Mapper\CategoryActivities;

class CategoryActivitiesRepository extends DocumentRepository
{
    public function findCategory(string $category) {
        return $this->dm->getRepository(CategoryActivities::class)->findOneBy(['category' => $category]);
    }
}