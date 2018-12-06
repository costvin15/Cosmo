<?php

namespace App\Http\Controller;

use App\Http\Controller\Validate\RegisterControllerValidate;
use App\Mapper\Activities;
use App\Mapper\AttemptActivities;
use App\Mapper\GroupActivities;
use App\Mapper\HistoryActivities;
use App\Mapper\User;
use App\Model\Util\Mail;
use App\Model\Util\Rand;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RegisterController
 * @package App\Http\Controller
 * @Controller
 * @Route("/report")
 */
class RelatorioController extends AbstractController
{

    /**
     * RegisterController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex", alias="report.alex")
     */
    public function alexAction(Request $request, Response $response) {
        $arrayUser = $this->_dm->getRepository(User::class)->findAll();

        $count = 0;
        foreach ($arrayUser as $users) {
            if ($users->getInterface() == NULL) {
                $count++;
            }
        }

        echo $count;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex2", alias="report.alex2")
     */
    public function alex2Action(Request $request, Response $response) {
        $arrayUser = $this->_dm->getRepository(User::class)->findAll();

        $countA = 0;
        $countB = 0;
        $countC = 0;
        $countD = 0;

        $report = [];

        $report['A']['tipo'] = 'A';
        $report['A']['tentativas'] = 0;
        $report['A']['historico'] = 0;

        $report['B']['tipo'] = 'B';
        $report['B']['tentativas'] = 0;
        $report['B']['historico'] = 0;

        $report['C']['tipo'] = 'C';
        $report['C']['tentativas'] = 0;
        $report['C']['historico'] = 0;

        $report['D']['tipo'] = 'D';
        $report['D']['tentativas'] = 0;
        $report['D']['historico'] = 0;

        foreach ($arrayUser as $users) {
            if ($users->getInterface() == NULL) {
                $arrayHistory = $users->getHistoryActivities();
                $numberAttempt = $this->_dm->getRepository(AttemptActivities::class)->getNumberAttemptActivityUser($users);


                if (count($arrayHistory) == 0) {
                    $report['A']['tentativas']+= $numberAttempt;
                    $report['A']['historico']+=count($arrayHistory);
                } elseif ((count($arrayHistory) >= 1) && (count($arrayHistory) <= 8)) {
                    $report['B']['tentativas']+= $numberAttempt;
                    $report['B']['historico']+=count($arrayHistory);
                } elseif ((count($arrayHistory) >= 9) && (count($arrayHistory) <= 15)) {
                    $report['C']['tentativas']+= $numberAttempt;
                    $report['C']['historico']+=count($arrayHistory);
                } else {
                    $report['D']['tentativas']+= $numberAttempt;
                    $report['D']['historico']+=count($arrayHistory);
                }
            }
        }

        $fp = fopen(__DIR__ . '/file_tentativas_historico.csv', 'w');

        foreach ($report as $fields) {
            fputcsv($fp, $fields, '$');
        }

        fclose($fp);

        echo "Cabô";
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex3", alias="report.alex3")
     */
    public function alex3Action(Request $request, Response $response) {
        $arrayAttempt = $this->_dm->getRepository(AttemptActivities::class)->findAll();

        $arrayReturn = [];
        $arrayReturn['581a237c7b3ceb04bb7b47d7'] = 0;
        $arrayReturn['581a1cab7b3ceb04bb7b47d2'] = 0;
        $arrayReturn['581a10bd7b3ceb04bb7b47cd'] = 0;

        foreach ($arrayAttempt as $attempt) {
            $activity =  $attempt->getActivity();
            $group = $activity->getGroup();

            $arrayReturn[$group->getId()]++;
        }

        print_r($arrayReturn);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex4", alias="report.alex4")
     */
    public function alex4Action(Request $request, Response $response) {
        $arrayUser = $this->_dm->getRepository(User::class)->findAll();

        $groupA = [];
        $groupB = [];
        $groupC = [];
        $groupD = [];
        foreach ($arrayUser as $users) {
            if ($users->getInterface() == NULL) {
                $arrayHistory = $users->getHistoryActivities();

                if (count($arrayHistory) == 0) {
                    $groupA[] = $users->getFullname();
                } elseif ((count($arrayHistory) >= 1) && (count($arrayHistory) <= 8)) {
                    $groupB[] = $users->getFullname();
                } elseif ((count($arrayHistory) >= 9) && (count($arrayHistory) <= 15)) {
                    $groupC[] = $users->getFullname();
                } else {
                    $groupD[] = $users->getFullname();
                }
            }
        }

        echo "Grupo A<br>";
        echo "----------------------<br>";

        foreach($groupA as $nome) {
            echo $nome . "<br>";
        }

        echo "<br>";

        echo "Grupo B<br>";
        echo "----------------------<br>";

        foreach($groupB as $nome) {
            echo $nome . "<br>";
        }

        echo "<br>";

        echo "Grupo C<br>";
        echo "----------------------<br>";

        foreach($groupC as $nome) {
            echo $nome . "<br>";
        }

        echo "<br>";

        echo "Grupo D<br>";
        echo "----------------------<br>";

        foreach($groupD as $nome) {
            echo $nome . "<br>";
        }

        echo "<br>";
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex5", alias="report.alex5")
     */
    public function alex5Action(Request $request, Response $response) {
        //$arrayUser = $this->_dm->getRepository(User::class)->findBy([ 'id' => '5b03010bb47dc230105b31a4']);
        $arrayUser = $this->_dm->getRepository(User::class)->findAll();

        $arrayReport = [];

        foreach($arrayUser as $user) {

            $arrayHistoryActivities = $user->getHistoryActivities();

            foreach($arrayHistoryActivities as $historyActivity) {

                $arrayActivity = $historyActivity->getActivity();

                $arrayGroup = $arrayActivity->getGroup();

                $numberAttempt = $this->_dm->getRepository(AttemptActivities::class)->getNumberAttemptActivity($user, $arrayActivity);

                $arrayReport[] = [
                    'USUARIO_NOME' => $user->getFullname(),
                    'USUARIO_EMAIL' => $user->getUsername(),
                    'ATIVIDADE_NOME' => str_replace("\n", "", $arrayActivity->getQuestion()),
                    'GRUPO_NOME' => str_replace("\n", "", $arrayGroup->getName()),
                    'TENTATIVAS_NUMERO' => $numberAttempt
                ];

            }

        }

        $fp = fopen(__DIR__ . '/file.csv', 'w');

        foreach ($arrayReport as $fields) {
            fputcsv($fp, $fields, '$');
        }

        fclose($fp);

        echo "Cabô";
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex6", alias="report.alex6")
     */
    public function alex6Action(Request $request, Response $response) {
        $arrayUser = $this->_dm->getRepository(User::class)->findAll();
        $arrayActivity = $this->_dm->getRepository(Activities::class)->findAll();

        $arrayReport = [];

        foreach($arrayUser as $user) {

            foreach($arrayActivity as $activity) {


                if (!$this->_dm->getRepository(HistoryActivities::class)->existHistoryForActivity($user, $activity)) {
                    $numberAttempt = $this->_dm->getRepository(AttemptActivities::class)->getNumberAttemptActivity($user, $activity);

                    if ($numberAttempt > 0) {
                        $arrayGroup = $activity->getGroup();

                        $arrayReport[] = [
                            'USUARIO_NOME' => $user->getFullname(),
                            'USUARIO_EMAIL' => $user->getUsername(),
                            'ATIVIDADE_NOME' => str_replace("\n", "", $activity->getQuestion()),
                            'GRUPO_NOME' => str_replace("\n", "", $arrayGroup->getName()),
                            'TENTATIVAS_NUMERO' => $numberAttempt
                        ];
                    }
                }
            }

        }

        $fp = fopen(__DIR__ . '/file2.csv', 'w');

        foreach ($arrayReport as $fields) {
            fputcsv($fp, $fields, '$');
        }

        fclose($fp);

        echo "Cabô";
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex7", alias="report.alex7")
     */
    public function alex7Action(Request $request, Response $response) {
        $arrayGroup = $this->_dm->getRepository(GroupActivities::class)->findAll();

        $arrayReport = [];

        foreach ($arrayGroup as $group) {
            $qtdActivities = count($group->getActivity());

            $arrayReport[] = [
                'GROUP_NAME' => $group->getName(),
                'GROUP_QTD_ACTIVITIES' => $qtdActivities
            ];
        }


        $fp = fopen(__DIR__ . '/file3.csv', 'w');

        foreach ($arrayReport as $fields) {
            fputcsv($fp, $fields, '$');
        }

        fclose($fp);

        echo "Cabô";
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/alex8", alias="report.alex8")
     * @throws
     */
    public function alex8Action(Request $request, Response $response) {
        $idUser = "5b030252b47dc2305568efd4";

        $arrayUser = $this->_dm->getRepository(User::class)->findOneBy([ 'id' => $idUser ]);

        $arrayAttempt = $this->_dm->createQueryBuilder(AttemptActivities::class)
            ->field('user')->references($arrayUser)
            ->getQuery()->execute();

        $arrayReport = [];

        foreach($arrayAttempt as $attempt) {
            $activity = $attempt->getActivity();

            $arrayReport[] = [
                'QUESTAO_ID' => $activity->getId(),
                'QUESTAO_NOME' => str_replace("\n", "", $activity->getQuestion()),
                'TENTATIVA_RESPOSTA' => $attempt->getAnswer(),
                'TENTATIVA_CODE' => str_replace("\"", "", str_replace("\r", "", str_replace("\n", "", $attempt->getCode()))),
                'TENTATIVA_RETORNO' => str_replace("\n", "", $attempt->getPayload()[0])
            ];

        }


        $fp = fopen(__DIR__ . '/file4.csv', 'w');

        foreach ($arrayReport as $fields) {
            fputcsv($fp, $fields, '$');
        }

        fclose($fp);

        echo "Cabô";
    }

}