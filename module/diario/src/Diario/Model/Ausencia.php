<?php

namespace Diario\Model;

use Application\Model\BaseTable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class Ausencia Extends BaseTable {

    public function getAusencias($params = false, $idGestor = false){
        return $this->getTableGateway()->select(function($select) use ($params, $idGestor) {
            $select->join(
                    array('f' => 'tb_funcionario'),
                    'f.id = funcionario',
                    array('nome_funcionario' => 'nome', 'matricula')
                );

            $select->join(
                    array('fg' => 'tb_funcionario_gestor'),
                    'fg.funcionario = f.id',
                    array(),
                    'LEFT'
                );

            $select->join(
                    array('u' => 'tb_empresa_unidade'),
                    'u.id = f.unidade',
                    array('nome_unidade' => 'nome')
                );

            $select->join(
                    array('e' => 'tb_empresa'),
                    'e.id = u.empresa',
                    array('nome_empresa' => 'nome')
                );

            $select->join(
                    array('func' => 'tb_funcao'),
                    'func.id = f.funcao',
                    array('nome_funcao' => 'nome')
                );

            $select->join(
                    array('s' => 'tb_setor'),
                    's.id = func.setor',
                    array('nome_setor' => 'nome')
                );

            $select->join(
                    array('a' => 'tb_area'),
                    's.area = a.id',
                    array('nome_area' => 'nome')
                );


            if($idGestor){
                $select->where
                        ->nest
                            ->equalTo('f.lider_imediato', $idGestor)
                            ->or
                            ->equalTo('fg.gestor', $idGestor)
                        ->unnest;
            }


            if($params){
            	if(!empty($params['matricula'])){
                    $select->where->like('f.matricula', '%'.$params['matricula'].'%');
                }

                if(!empty($params['nome_funcionario'])){
                    $select->where->like('f.nome', '%'.$params['nome_funcionario'].'%');
                }

                if(!empty($params['inicio']) && !empty($params['fim'])){
                    $select->where->between('data', $params['inicio'], $params['fim']);
                }

                if(!empty($params['empresa'])){
                    $select->where(array('e.id' => $params['empresa']));
                }

                if(!empty($params['unidade'])){
                    $select->where(array('u.id' => $params['unidade']));
                }

            }
            $select->group('f.id');
            $select->order('data DESC, f.nome');
        }); 
    }

    public function getAusenciaFuncionarioToEscala($idFuncionario, $mes, $ano){
        //ultimo dia do mes
        return $this->getTableGateway()->select(function($select) use ($idFuncionario, $mes, $ano) {
            $ultimoDia = date("t", mktime(0,0,0,$mes,'01',$ano));
            $inicio = $ano.'-'.$mes.'-01';
            $fim = $ano.'-'.$mes.'-'.$ultimoDia;
            
            $select->where->addPredicate(new Expression('(data >= '.$inicio.' OR data <= '.$fim.')'));

            $select->where(array('funcionario' => $idFuncionario));

        });
    }
}
