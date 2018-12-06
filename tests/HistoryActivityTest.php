<?php

namespace Tests;

use App\Mapper\Activities;
use App\Mapper\User;
use App\Mapper\HistoryActivities;

class HistoryActivityTest extends BaseUnitTests
{

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $_dm;

    public function setUp()
    {
        parent::setUp();
        $this->_dm = $this->_ci->get('database');
    }

    /**
     * @test
     */
    public function shouldCreateHistory() {

        $user = $this->_dm->getRepository(User::class)->find('5808038d4b28a10c6cbc5c3c');
        $activity = $this->_dm->getRepository(Activities::class)->find('580ff69b5bd49c0ac5809682');

        $history = new HistoryActivities();
        $history->setActivity($activity);
        $history->setUser($user);
        $history->setTimeStart(new \DateTime('now'));
        $history->setTimeEnd(new \DateTime('now'));

        $this->_dm->persist($history);
        $this->_dm->flush();

    }

}