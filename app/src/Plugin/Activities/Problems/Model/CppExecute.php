<?php


namespace App\Plugin\Activities\Problems\Model;

class CppExecute
{
    private $exec;
    private $sandbox;
    public $entradaCodigo;
    public $inResp;
    public $outResp;
    private $modeArq;
    private $modeRun;
    public $outFile;
    public $outDataFromDB;
    private $outFileName;
    private $respFileName;
    private $debug;
    private $timeIn;
    private $timeOut;

    private $return; /* retorno do codigo */

    public function __construct()
    {
        /*
         * $this->modeArq
         * -> Como arquivar a resposta da atividade
         * ('arquivar', 'temporario')
         */
        $this->modeArq = "arquivar";

        /*
         * $this->modeRun
         * -> Como arquivar a execultavel de teste da atividade
         * ('arquivar', 'temporario')
         */
        $this->modeRun = "temporario";

        $this->sandbox = "";
        if (PHP_OS == 'Darwin')
            $this->exec = "/usr/bin/gcc";
        elseif (PHP_OS == "WINNT")
            $this->exec = "g++";
        else 
            $this->exec = "/usr/bin/gcc";
        $this->codeEncpt = "base64";
    }

    /**
     * Como usar ->
     * $obj = new atvSystem();
     *
     *	$obj->criarEntradaCodigo($data); #CODIGO DO ALUNO
     *	$obj->setRespostaInFile($resposta) #STDIN DA RESPOSTA
     *  $obj->criaSaidaTeste($data) #TESTE DE SAIDA DA RESPOSTA
     *	$obj->runCode(); @return se esta certo ou errado
     *
     */
    public function getTimeIn(){
        return $this->timeIn;
    }

    public function getTimeOut(){
        return $this->timeOut;
    }

    public function criarEntradaCodigo($data)
    {
        $this->debug = $data;
        $this->setEntradaCodigo($this->criaTempFileW($data, ".cc"));
    }

    private function criaTempFileW($text = null, $extension = "")
    {
        $filename = tempnam(sys_get_temp_dir(), 'TMP_COSMO_PROBLEMS_');
        if ($extension !== "") {
            rename($filename, $filename . $extension);
            $filename = $filename . $extension;
        }
        file_put_contents($filename, $text);

        return $filename;
    }

    public function criaSaidaTeste($data) //Saida Correta
    {
        $this->outDataFromDB = $data;
        $this->outResp = $this->criaTempFileW($data);
    }

    private function setEntradaCodigo($filename)
    {
        $this->entradaCodigo = $filename;
        return ;
    }

    public function setRespostaInFile($data)
    {
        $this->inResp = $this->criaTempFileW($data);
    }

    public function getReturn()
    {
        return $this->return;
    }

    public function formataExec($type = "exec")
    {
        $tempDir = sys_get_temp_dir();

        switch ($type) {
            case 'exec':
                if($this->modeRun == "temporario") {
                    $filename = tempnam($tempDir, 'COSMO');
                    shell_exec($this->sandbox . $this->exec . " \"" . $this->entradaCodigo . "\" -o \"" . $filename . "\"");
                    return "\"". $filename . "\" < \"" . $this->inResp . "\"";
                } elseif( $this->modeRun == "arquivar") {
                    return $this->sandbox.$this->exec." ".$this->entradaCodigo." < ".$this->inResp." > out.file";
                }

            case 'diff':
                if (PHP_OS == 'Linux' || PHP_OS == 'Darwin')
                    return "diff -bB ".$this->outFile." ".$this->outResp;
                else
                    return "FC /b \"".$this->outFile."\" \"".$this->outResp."\"";
        }
    }

    public function runCode(){
        $this->timeIn = microtime(true);
        $this->return = shell_exec($this->formataExec());
        $this->timeOut = microtime(true);

        if(is_null($this->return))
            $this->return = 'Sem resposta';

//        $this->return = trim(preg_replace('/\s+/', ' ', $this->return));

        $this->outFile = $this->criaTempFileW($this->return);

        return $this->difference();
    }

    private function difference()
    {
        $diff = shell_exec($this->formataExec("diff"));


        if (PHP_OS == 'Linux' || PHP_OS == 'Darwin')
        {
            if(is_null($diff))
                return array(true, 'Correto', $this->return);
            else
                return array(false, 'Errado', $this->return, $this->outDataFromDB);
        }
        else
        {
            if (strpos($diff,'Keine Unterschiede gefunden') !== false
                || strpos($diff,'no differences encountered') !== false
                || strpos($diff,'nenhuma') !== false) {
                return array(true, 'Correto', $this->return." ". mb_convert_encoding($diff, "UTF-8", "ISO-8859-1"));
            }
            else
            {
                return array(false, 'Errado', $this->return,$this->outDataFromDB);
            }
        }
    }
}