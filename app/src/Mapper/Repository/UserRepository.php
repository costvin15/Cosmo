<?php

namespace App\Mapper\Repository;


use App\Mapper\User;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Doctrine\ODM\MongoDB\Query\Query;

class UserRepository extends DocumentRepository
{

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function getUserWithUsernamePassword(string $username, string $password) {
        return $this->dm->getRepository(User::class)->findBy(['username' => $username, 'password' => $password]);
    }

    public function getUserWithUsername(string $username) {
        return $this->dm->getRepository(User::class)->findOneBy(['username' => $username ]);
    }
}