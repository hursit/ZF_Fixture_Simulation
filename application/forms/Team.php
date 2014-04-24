<?php

class Application_Form_Team extends Zend_Form
{

    public function init()
    {
        parent::init();
    }
    public function add(){
        $this->setName('addTeam');   
         
        $name = new Zend_Form_Element_Text('name');
        $name->setAttrib('placeHolder', 'Takım Adını Giriniz')
                ->setLabel('Takım Adı')
                ->setRequired(TRUE)
                ->setAttrib('class', 'input-prepend');
        
        $strength = new Zend_Form_Element_Text('strength');
        $strength->setAttrib('placeHolder','Takım Gücünü Giriniz')
                ->setRequired(true)
                ->setLabel('Takım Gücü')
                ->setAttrib('class', 'input-prepend');
        $submit = new Zend_Form_Element_Submit('submit');                
        $submit->setLabel('Ekle')
                ->setAttrib('class', 'btn btn-primary');
        
        $this->addElements(array($name, $strength, $submit));
        $this->setMethod('post');
        $this->setAttrib('class', 'loginform');
    }
    
    public function edit(){
        $this->setName('editTeam');   
        
        $team_id = $this->getAttrib('team_id');
        $teamsModel = new Application_Model_DbTable_Teams();
        $team = $teamsModel->getByFilter(array('id' => $team_id));
        
        $name = new Zend_Form_Element_Text('name');
        $name->setAttrib('placeHolder', 'Takım Adını Giriniz')
                ->setLabel('Takım Adı')
                ->setValue($team->name)
                ->setRequired(TRUE)
                ->setAttrib('class', 'input-prepend');
        
        $strength = new Zend_Form_Element_Text('strength');
        $strength->setAttrib('placeHolder','Takım Gücünü Giriniz')
                ->setRequired(true)
                ->setLabel('Takım Gücü')
                ->setValue($team->strength)
                ->setAttrib('class', 'input-prepend');
        $submit = new Zend_Form_Element_Submit('submit');                
        $submit->setLabel('Düzenle')
                ->setAttrib('class', 'btn btn-primary');
        
        $this->addElements(array($name, $strength, $submit));
        $this->setMethod('post');
        $this->setAttrib('class', 'loginform');
    }
    
}