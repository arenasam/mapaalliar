<?php

namespace Mensal\Model;

use Application\Model\BaseTable;

class Qmatic Extends BaseTable {

    public function getDados($params = false){
        return $this->getTableGateway()->select(function($select) use ($params) {
            $select->join(
                    array('u' => 'tb_empresa_unidade'),
                    'u.id = unidade',
                    array('nome_unidade' => 'nome')
                );

            $select->join(
                    array('e' => 'tb_empresa'),
                    'e.id = u.empresa',
                    array('nome_empresa' => 'nome')
                );

            if($params){
                if(isset($params['empresa']) && !empty($params['empresa'])){
                    $select->where(array('empresa' => $params['empresa']));
                }

                if(isset($params['unidade']) && !empty($params['unidade'])){
                    $select->where(array('unidade' => $params['unidade']));
                }
            }
        }); 
    }

    public function getDado($idQmatic){
        return $this->getTableGateway()->select(function($select) use ($idQmatic) {
            $select->join(
                    array('u' => 'tb_empresa_unidade'),
                    'u.id = unidade',
                    array('empresa')
                );

                $select->where(array('tb_qmatic.id' => $idQmatic));
        })->current(); 
    }


}