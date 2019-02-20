<?php

namespace App\Plugin\Activities\Problems\Model;

class PythonExecute {
    /**
     * Armazena um vetor de arrays com entrada e saida
     */
    private $casos;
    /**
     * Armazerna o codigo a ser compilado
     */
    private $codigo;
    /**
     * Media do tempo de execução do código
     */
    private $tempo_de_execucao;

    /**
     * Construtor
     */
    public function __construct($casos, $codigo){
        $this->casos = $casos;
        $this->codigo = $this->criaArquivoTemporario($codigo, ".py");
        $this->tempo_de_execucao = [];
    }

    /**
     * Retorna o tempo de execução do código
     */
    public function getTempoDeExecucao(){
        return $this->tempo_de_execucao;
    }

    /**
     * Retorna o vetor de entradas armazenado no vetor de objetos $casos
     */
    protected function getEntradas(){
        $entradas = [];
        foreach ($this->casos as $caso)
            $entradas[] = $caso["in"];
        return $entradas;
    }
    
    /**
     * Retorna o vetor de saidas armazenado no vetor de objetos $casos
     */
    protected function getSaidas(){
        $saidas = [];
        foreach ($this->casos as $caso)
            $saidas[] = $caso["out"];
        return $saidas;
    }

    /**
     * Compila e executa o codigo passado, tendo como entrada $this->getEntradas()
     */
    protected function executar(){
        $saidas = [];
        foreach ($this->getEntradas() as $entrada){
            $arquivo = $this->criaArquivoTemporario($entrada);
            $this->tempo_de_execucao["in"] = microtime(true);
            $saidas[] = shell_exec("python \"" . $this->codigo . "\" < \"" . $arquivo . "\"");
            $this->tempo_de_execucao["out"] = microtime(true);
        }
        return $saidas;
    }

    /**
     * Recebe o resultado da execução do código, e armazena em um arquivo temporario
     */
    protected function criaArquivoSaidasUsuario(){
        $nome_arquivo_temporario_saida = $this->criaArquivoTemporario();
        $arquivo_temporario_saida = fopen($nome_arquivo_temporario_saida, "a");

        foreach ($this->executar() as $saida)
            fwrite($arquivo_temporario_saida, $saida . "\n");
        fclose($arquivo_temporario_saida);

        return $nome_arquivo_temporario_saida;
    }

    /**
     * Recebe as saidas esperadas, e armazena em um arquivo temporario
     */
    protected function criaArquivoSaidasEsperadas(){
        $nome_arquivo_temporario_saida = $this->criaArquivoTemporario();
        $arquivo_temporario_saida = fopen($nome_arquivo_temporario_saida, "a");

        foreach ($this->getSaidas() as $saida)
            fwrite($arquivo_temporario_saida, $saida . "\n");
        fclose($arquivo_temporario_saida);

        return $nome_arquivo_temporario_saida;
    }

    /**
     * Verifica se os dois arquivos sao iguais
    */
    public function resultado(){
        $resposta = $this->criaArquivoSaidasUsuario();
        $gabarito = $this->criaArquivoSaidasEsperadas();

        if (strcmp(file_get_contents($resposta), file_get_contents($gabarito)) == 0)
            return array(true, "Correto", file_get_contents($resposta));
        else
            return array(false, "Errado", file_get_contents($gabarito));
    }

    /**
     * Cria um arquivo temporario do conteudo passado por parametro
     */
    private function criaArquivoTemporario($conteudo = "", $extensao = ""){
        $arquivo = tempnam(sys_get_temp_dir(), "COSMO_ARQUIVO_TEMPORARIO_");
        if ($extensao != ""){
            rename($arquivo, $arquivo . $extensao);
            $arquivo .= $extensao;
        }
        file_put_contents($arquivo, $conteudo);
        return $arquivo;
    }
}