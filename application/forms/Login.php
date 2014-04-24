<?php

class Application_Form_Login extends Zend_Form
{
    
    public function init()
    {
        $this->setName('login');   
         
        $email = new Zend_Form_Element_Text('email');
        $email->setAttrib('placeHolder', 'Email Adresinizi Giriniz')
                ->setLabel('Email Adresiniz')
                ->setRequired(TRUE)
                ->setAttrib('class', 'input-prepend');
        
        $password = new Zend_Form_Element_Password('password');
        $password->setAttrib('placeHolder','Sifrenizi Giriniz')
                ->setRequired(true)
                ->setLabel('Sifreniz')
                ->setAttrib('class', 'input-prepend');
        $submit = new Zend_Form_Element_Submit('submit');                
        $submit->setLabel('Giris')
                ->setAttrib('class', 'btn btn-primary');
        
        $this->addElements(array($email, $password, $submit));
        $this->setMethod('post');
        $this->setAttrib('class', 'loginform');
    }
}