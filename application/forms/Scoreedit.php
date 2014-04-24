<?php

class Application_Form_Scoreedit extends Zend_Form
{

    public function init()
    {
        
        $this->setName('editScore');   
        
        $match_id = $this->getAttrib('match_id');
        $teamsModel = new Application_Model_DbTable_Teams();
        $gamesModel = new Application_Model_DbTable_Games();
        
        $match = $gamesModel->getByFilter(array('id' => $match_id));
        $first_team = $teamsModel->getByFilter(array('id' => $match->first_team_id));
        $second_team = $teamsModel->getByFilter(array('id' => $match->second_team_id));
        
        
        $first_team_goal = new Zend_Form_Element_Text('first_team_goal');
        $first_team_goal->setAttrib('placeHolder', $first_team->name." attıgı gol sayisini giriniz")
                ->setLabel($first_team->name)
                ->setValue($match->first_team_goal)
                ->setRequired(TRUE)
                ->setAttrib('class', 'input-prepend');
        $second_team_goal = new Zend_Form_Element_Text('second_team_goal');
        $second_team_goal->setAttrib('placeHolder', $second_team->name." attıgı gol sayisini giriniz")
                ->setLabel($second_team->name)
                ->setValue($match->second_team_goal)
                ->setRequired(TRUE)
                ->setAttrib('class', 'input-prepend');
        $submit = new Zend_Form_Element_Submit('submit');                
        $submit->setLabel('Düzenle')
                ->setAttrib('class', 'btn btn-primary');
        
        $this->addElements(array($first_team_goal, $second_team_goal, $submit));
        $this->setMethod('post');
        $this->setAttrib('class', 'loginform');
    }
}

