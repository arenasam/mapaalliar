<?php

namespace Diario\Controller;

use Application\Controller\BaseController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Session\Container;

use Diario\Form\PesquisarAjuda as formPesquisa;
use Diario\Form\Ajuda as formAjuda;

use Diario\Form\PesquisarAjudaAdmin as formPesquisaAdmin;
use Diario\Form\AjudaAdmin as formAjudaAdmin;

class AjudaController extends BaseController
{
    private $campos = array(
            'Matrícula'                 =>  'matricula',
            'Nome do funcionário'       => 'nome_funcionario',
            'Unidade de origem'         =>  'nome_unidade',
            'Data e hora de início'     => 'data_inicio',
            'Data e hora de término'    => 'data_fim',
            'Horário de início'         => 'hora_inicio',
            'Horário de término'        =>  'hora_fim',
            'Área que irá atuar'        => 'nome_area',
            'Setor que irá atuar'       =>  'nome_setor',
            'Unidade de destino'        =>  'unidade_destino'
        );

    

    public function indexAction(){
        $this->layout('layout/gestor');
    	$serviceAjuda = $this->getServiceLocator()->get('Ajuda');
        
        $usuario = $this->getServiceLocator()->get('session')->read();
        $formPesquisa = new formPesquisa('frmAjuda', $this->getServiceLocator(), $usuario);

    	$formPesquisa = parent::verificarPesquisa($formPesquisa);
        $ajudas = $serviceAjuda->getAjudas($this->sessao->parametros, $usuario['funcionario'])->toArray();
        
        foreach ($ajudas as $key => $ajuda) {
            $ajudas[$key]['data_inicio'] = $formPesquisa->converterData($ajuda['data_inicio']);
            $ajudas[$key]['data_fim'] = $formPesquisa->converterData($ajuda['data_fim']);
        }

        if($this->getRequest()->isPost()){
            $dados = $this->getRequest()->getPost();
            if(isset($dados->exportar)){
                parent::gerarExcel($this->campos, $ajudas, 'Ajuda');
            }
        }
        
        $paginator = new Paginator(new ArrayAdapter($ajudas));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(5);
        
        return new ViewModel(array(
                                'ajudas'         => $paginator,
                                'formPesquisa'  => $formPesquisa
                            ));
    }

    public function novoAction(){
        $this->layout('layout/gestor');
        $usuario = $this->getServiceLocator()->get('session')->read();
        $formAjuda = new formAjuda('frmAjuda', $this->getServiceLocator(), $usuario);

        if($this->getRequest()->isPost()){
            $formAjuda->setData($this->getRequest()->getPost());
            if($formAjuda->isValid()){
                $dados = $formAjuda->getData();
                $funcionario = $this->serviceLocator->get('Funcionario')->getFuncionario($usuario['funcionario']);
                $dados['unidade_destino'] = $funcionario['unidade'];
                $idAjuda = $this->getServiceLocator()->get('Ajuda')->insert($dados);
                $this->flashMessenger()->addSuccessMessage('Ajuda inserida com sucesso!');
                return $this->redirect()->toRoute('alterarAjuda', array('id' => $idAjuda));
            }
        }
        return new ViewModel(array('formAjuda' => $formAjuda));
    }

    public function alterarAction(){
        $this->layout('layout/gestor');
        $idAjuda = $this->params()->fromRoute('id');
        $serviceAjuda = $this->getServiceLocator()->get('Ajuda');

        $usuario = $this->getServiceLocator()->get('session')->read();
        $formAjuda = new formAjuda('frmAjuda', $this->getServiceLocator(), $usuario);

        $ajuda = $serviceAjuda->getAjuda($idAjuda);
        if(!$ajuda){
            $this->flashMessenger()->addWarningMessage('Ajuda não encontrada!');
            return $this->redirect()->toRoute('listarAjuda');
        }

        $formAjuda->setData($ajuda);
        
        if($this->getRequest()->isPost()){
            $formAjuda->setData($this->getRequest()->getPost());
            if($formAjuda->isValid()){
                $dados = $formAjuda->getData();
                unset($dados['funcionario']);
                $serviceAjuda->update($dados, array('id' => $idAjuda));
                $this->flashMessenger()->addSuccessMessage('Ajuda alterada com sucesso!');
                return $this->redirect()->toRoute('alterarAjuda', array('id' => $idAjuda));
            }
        }

        
        return new ViewModel(array(
            'formAjuda'    => $formAjuda
            ));
    }

    public function carregarfuncionarioAction(){
        $params = $this->getRequest()->getPost();
        //instanciar form
        $usuario = $this->getServiceLocator()->get('session')->read();
        $formAjuda = new formAjuda('frmAjuda', $this->getServiceLocator(), $usuario);
        $funcionarios = $formAjuda->setFuncionarioByUnidade($params->unidade);
        
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setVariables(array('funcionario' => $funcionarios));
        return $view;
    }


    //admin
    public function indexadminAction(){
        $serviceAjuda = $this->getServiceLocator()->get('Ajuda');
        
        $formPesquisa = new formPesquisaAdmin('frmAjuda', $this->getServiceLocator());

        $formPesquisa = parent::verificarPesquisa($formPesquisa);
        $ajudas = $serviceAjuda->getAjudas($this->sessao->parametros)->toArray();
        
        foreach ($ajudas as $key => $ajuda) {
            $ajudas[$key]['data_inicio'] = $formPesquisa->converterData($ajuda['data_inicio']);
            $ajudas[$key]['data_fim'] = $formPesquisa->converterData($ajuda['data_fim']);
        }

        if($this->getRequest()->isPost()){
            $dados = $this->getRequest()->getPost();
            if(isset($dados->exportar)){
                parent::gerarExcel($this->campos, $ajudas, 'Ajuda');
            }
        }
        
        $paginator = new Paginator(new ArrayAdapter($ajudas));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(5);
        
        return new ViewModel(array(
                                'ajudas'         => $paginator,
                                'formPesquisa'  => $formPesquisa
                            ));
    }

    public function novoadminAction(){
        $formAjuda = new formAjudaAdmin('frmAjuda', $this->getServiceLocator());

        if($this->getRequest()->isPost()){
            $formAjuda->setData($this->getRequest()->getPost());
            if($formAjuda->isValid()){
                $dados = $formAjuda->getData();
                $idAjuda = $this->getServiceLocator()->get('Ajuda')->insert($dados);
                $this->flashMessenger()->addSuccessMessage('Ajuda inserida com sucesso!');
                return $this->redirect()->toRoute('alterarAjudaAdmin', array('id' => $idAjuda));
            }
        }
        return new ViewModel(array('formAjuda' => $formAjuda));
    }

    public function alteraradminAction(){
        $idAjuda = $this->params()->fromRoute('id');
        $serviceAjuda = $this->getServiceLocator()->get('Ajuda');

        $formAjuda = new formAjudaAdmin('frmAjuda', $this->getServiceLocator());

        $ajuda = $serviceAjuda->getAjuda($idAjuda);
        if(!$ajuda){
            $this->flashMessenger()->addWarningMessage('Ajuda não encontrada!');
            return $this->redirect()->toRoute('listarAjudaAdmin');
        }

        $formAjuda->setData($ajuda);
        
        
        if($this->getRequest()->isPost()){
            $formAjuda->setData($this->getRequest()->getPost());
            if($formAjuda->isValid()){
                $dados = $formAjuda->getData();
                unset($dados['funcionario']);
                $serviceAjuda->update($dados, array('id' => $idAjuda));
                $this->flashMessenger()->addSuccessMessage('Ajuda alterada com sucesso!');
                return $this->redirect()->toRoute('alterarAjudaAdmin', array('id' => $idAjuda));
            }
        }

        
        return new ViewModel(array(
            'formAjuda'    => $formAjuda
            ));
    }

}
