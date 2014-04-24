<?php

class Application_Model_DbTable_Teams extends Zend_Db_Table_Abstract
{

    protected $_name = 'teams';
    protected $_primary = 'id';
    
    public function getAll($filterArr=array(),$toArray = false)
    {
        $filter = $this->select();
        if (is_array($filterArr)) {
            foreach ($filterArr as $fieldId => $fieldValue) {
                $filter->where($fieldId.'=?', $fieldValue);
            }
        }
        if($toArray){
            return $this->fetchAll($filter)->toArray();
        }else{
            return $this->fetchAll($filter);
        }
    }
    public function getByFilter($filterArr = array(),$toArray=false)
    {
        $filter = $this->select();
        if (is_array($filterArr)) {
            foreach ($filterArr as $fieldId => $fieldValue) {
                $filter->where($fieldId . '=?', $fieldValue);
            }
        }
        $row = $this->fetchRow($filter);
        if (!$row) {
            return array();
        }
        else{
            if($toArray){
                return $row->toArray();
            }
            else{
                return $row;
            }
        }
    }
    
    public function add($formData) {
        $this->insert($formData);
    }
    public function edit($id, $formData){
        $this->update($formData,$this->_primary.' = '.(int)$id);    
    }
    
    public function deleteTeam($id){
        $this->delete($this->_db->quoteInto('id = ?', $id));
    }
}

