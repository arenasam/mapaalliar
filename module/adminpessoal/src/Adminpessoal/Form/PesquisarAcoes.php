<?php

 namespace Adminpessoal\Form;
 
use Application\Form\Base as BaseForm;
 
 class PesquisarAcoes extends BaseForm
 {
     
    /**
     * Sets up generic form.
     * 
     * @access public
     * @param array $fields
     * @return void
     */
   public function __construct($name, $serviceLocator, $usuario)
    {
        if($serviceLocator)
           $this->setServiceLocator($serviceLocator);

        parent::__construct($name);  
        //matricula
        $this->genericTextInput('matricula', 'Matrícula:', false, 'Númeroda matrícula');

        //funcionario
        $this->genericTextInput('nome_funcionario', 'Funcionário:', false, 'Nome do funcionário');

        //data
        $this->genericTextInput('inicio', 'Data de:', false);
        $this->genericTextInput('fim', 'Até:', false);
            

        $this->setAttributes(array(
            'role'   => 'form'
        ));

    }

    public function getData($flag = 17){
        $dados = parent::getData();
        $dados['inicio'] = parent::converterData($dados['inicio']);
        $dados['fim'] = parent::converterData($dados['fim']);

        return $dados;
    }

 }