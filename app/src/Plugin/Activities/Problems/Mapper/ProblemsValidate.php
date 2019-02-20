<?php

namespace App\Plugin\Activities\Problems\Mapper;

use App\Facilitator\App\SessionFacilitator;
use App\Facilitator\Database\DatabaseFacilitator;
use App\Mapper\Activities;
use App\Mapper\AttemptActivities;
use App\Mapper\HistoryActivities;
use App\Mapper\User;
use App\Plugin\Activities\Problems\Model\CppExecute;
use App\Plugin\Activities\Problems\Model\LuaExecute;
use App\Plugin\Activities\Problems\Model\PythonExecute;
use Doctrine\ODM\MongoDB\DocumentManager;

class ProblemsValidate
{
    /**
     * @var DocumentManager;
     */
    private $_dm;

    function __invoke(array $data)
    {
        $this->_dm = DatabaseFacilitator::getConnection();

        $idActivity = $data['id_activity'];
        $activity = $this->_dm->getRepository(Activities::class)->find($idActivity);
        $problem = $activity->getActivities();

        switch ($data['language']) {
            case "cpp":
                $obj = new CppExecute($problem->getTasks(), $data["source_code"]);
                break;
            case "lua":
                $obj = new LuaExecute($problem->getTasks(), $data["source_code"]);
                break;
            case "python":
                $obj = new PythonExecute($problem->getTasks(), $data["source_code"]);
                break;
            default:
                $obj = new LuaExecute($problem->getTasks(), $data["source_code"]);
                break;
        }

        $arrayReturn = $obj->resultado();

        $classReturn = new \stdClass();
        $classReturn->answer = $arrayReturn[0];
        $classReturn->payload = [
            'command' => $arrayReturn[2]
        ];
        $classReturn->timeIn = $obj->getTempoDeExecucao()["in"];
        $classReturn->timeOut = $obj->getTempoDeExecucao()["out"];

        return $classReturn;
    }

    public function saveAttempt($data, $returnValidate) {
        $this->_dm = DatabaseFacilitator::getConnection();

        $idActivity = $data['id_activity'];
        $activity = $this->_dm->getRepository(Activities::class)->find($idActivity);

        $attributeSession = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributeSession['id']);

        $attempt = new AttemptActivities();

        $attempt->setActivity($activity);
        $attempt->setUser($user);
        $attempt->setCode($data['source_code']);
        $attempt->setAnswer($returnValidate->answer);
        $attempt->setPayload($returnValidate->payload);

        $this->_dm->persist($attempt);
        $this->_dm->flush();
    }

    public function saveHistory(array $data) {
        $this->_dm = DatabaseFacilitator::getConnection();

        $idActivity = $data['id_activity'];
        $activity = $this->_dm->getRepository(Activities::class)->find($idActivity);

        $attributeSession = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributeSession['id']);

        $historyCursor = $this->_dm->createQueryBuilder(HistoryActivities::class)
            ->field('activity')
            ->references($activity)
            ->field('user')
            ->references($user)
            ->getQuery()
            ->execute();
        $history = $historyCursor->getNext();

        if ($history == null){
            $history = new HistoryActivities();

            $user->setAnsweredActivities($user->getAnsweredActivities() + 1);
            $this->_dm->persist($user);
            $this->_dm->flush();
        }

        $history->setActivity($activity);
        $history->setUser($user);
        $history->setCode($data['source_code']);
        $history->setLanguage($data["language"]);
        $history->setTimeStart($data['dateini']);
        $history->setTimeEnd($data['datefim']);

        if (key_exists('classification', $data)) {
            $history->setClassification($data['classification']);
        } else {
            if ($history->getClassification() == null)
                $history->setClassification("0");
        }

        if (key_exists('difficulty', $data)) {
            $history->setDifficulty($data['difficulty']);
        } else {
            if ($history->getDifficulty() == null)
                $history->setDifficulty("0");
        }

        $this->_dm->persist($history);
        $this->_dm->flush();
    }
}