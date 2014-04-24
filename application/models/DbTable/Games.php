<?php

class Application_Model_DbTable_Games extends Zend_Db_Table_Abstract
{

    protected $_name = 'games';
    protected $_primary = 'id';
    protected $_number_of_teams = 4;
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
        $this->update($formData,'id = '.(int)$id);    
    }
    public function deleteMatch($id){
        $this->delete($this->_db->quoteInto('id = ?', $id));
    }
    public function truncateGames(){
        $this->delete("1=1");
    }
    public function addMatches($array){
        $this->_db->beginTransaction();
        try {
            foreach ($array as $match){
                //birinci yari
                $data = array(
                    'week' => $match['week'],
                    'first_team_id' => $match['teams'][0]['id'],
                    'second_team_id' => $match['teams'][1]['id']
                );
                $this->insert($data);
                
                //ikinci yari
                //takimlarin yerleri degismeli hafta da artmali
                $data = array(
                    'week' => $match['week']+3,
                    'first_team_id' => $match['teams'][1]['id'],
                    'second_team_id' => $match['teams'][0]['id']
                );
                $this->insert($data);
            }
            $this->_db->commit();
        } catch (Exception $exc) {
            $this->_db->rollBack();
            echo $exc->getTraceAsString();
        }
    }
    public function getTeamWeekStatus($team_id,$week){
        //kendi evindeki maclar
        $games_at_home = $this->getByFilter(array('first_team_id' => $team_id,'week' => $week));
        //deplasmandaki maclar
        $games_at_guest = $this->getByFilter(array('second_team_id' => $team_id,'week' => $week));
        if(count($games_at_home)){
            return array('this_team_goal' => $games_at_home->first_team_goal,'other_team_goal' => $games_at_home->second_team_goal);
        }
        elseif(count($games_at_guest)){
            return array('this_team_goal' => $games_at_guest->second_team_goal,'other_team_goal' => $games_at_guest->first_team_goal);
        }
    }
    
    //takim sayisi 4
    public function getTeamStatus($team_id,$end_week = NULL){
        $end_week = ($end_week == NULL) ? ($this->_number_of_teams-1)*2 : $end_week;
        $point = 0;
        $draw = 0;
        $win = 0;
        $defeat = 0;
        $avarage = 0;
        for ($week = 1; $week <= $end_week; $week++) {
            $matches_played = $this->getAll(array('week' => $week,'status' => 'played'));
            if(!count($matches_played)){
                continue;
            }
            $week_status = $this->getTeamWeekStatus($team_id, $week);
            $this_team_goal = $week_status['this_team_goal'];
            $other_team_goal = $week_status['other_team_goal'];
            if($this_team_goal > $other_team_goal){
                $win += 1;
            }
            elseif ($this_team_goal == $other_team_goal) {
                $draw += 1;
            }
            else{
                $defeat += 1;
            }
            $avarage += $this_team_goal-$other_team_goal;
        }
        $point = $win * 3 + $draw;
        return array(
            'win' => $win,
            'draw' => $draw,
            'defeat' => $defeat,
            'played_games_number' => $end_week,
            'point' => $point,
            'avarage' => $avarage
        );
    }
}

