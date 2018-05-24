<?php

 namespace Cadastros\Form;
 
 use Application\Form\Base as BaseForm; 

 class AdicionarGestor extends BaseForm {
     
    /**
     * Sets up generic form.
     * 
     * @access public
     * @param array $fields
     * @return void
     */
   public function __construct($name, $serviceLocator)
    {
        if($serviceLocator)
           $this->setServiceLocator($serviceLocator);

        parent::__construct($name);      

        //empresa
        $empresas = $this->serviceLocator->get('Empresa')->getRecordsFromArray(array(), 'nome');
        $empresas = $this->prepareForDropDown($empresas, array('id', 'nome'));
        $this->_addDropdown('empresa', '* Empresa:', true, $empresas, 'carregarUnidade(this.value, "C");');

        //unidade
        $this->_addDropdown('unidade', '* Unidade:', true, array('' => 'Selecione uma empresa'), 'carregarLider(this.value);');

        //lider_atual
        $this->_addDropdown('lider_imediato', '* Gestor:', true, array('' => 'Selecione uma unidade'));

        $this->setAttributes(array(
            'class'  => 'form-inline'
        ));
        
    }
 }
