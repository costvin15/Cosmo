<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 29/10/16
 * Time: 19:44
 */

namespace Tests\PluginActivities\Model;


use App\PluginActivities\Problems\Model\LuaExecute;
use Tests\BaseUnitTests;

class LuaExecuteTest extends BaseUnitTests
{

    public function setUp()
    {
        parent::setUp();
    }


    /**
     * @test
     */
    public function shouldValidateSumNumbers() {
        /*
         * Como usar ->
         * $obj = new atvSystem();
         *
         *	$obj->criarEntradaCodigo($data); #CODIGO DO ALUNO
         *	$obj->setRespostaInFile($resposta) #STDIN DA RESPOSTA
         *  $obj->criaSaidaTeste($data) #TESTE DE SAIDA DA RESPOSTA
         *	$obj->runCode(); @return se esta certo ou errado
         *
         */

        $obj = new LuaExecute();
        $obj->criarEntradaCodigo(file_get_contents(__DIR__ . '/soma.lua'));
        $obj->setRespostaInFile(file_get_contents(__DIR__  . '/in.txt'));
        $obj->criaSaidaTeste(file_get_contents(__DIR__  . '/out.txt'));
        $return = $obj->runCode();
        $this->assertTrue($return[0]);
    }

}