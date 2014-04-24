<?php

class IndexController extends Zend_Controller_Action
{

    protected $_gamesModel = null;
    protected $_teamsModel = null;
    protected $_number_of_teams = 4;

    public function init()
    {
        $this->_gamesModel = new Application_Model_DbTable_Games();
        $this->_teamsModel = new Application_Model_DbTable_Teams();
    }

    public function indexAction()
    {
        $this->view->teams = $this->_teamsModel->getAll();
        
    }

    public function loginAction()
    {
        /// Layout olmayacak. Cunku header vs. login icin farkli olacak
        $this->_helper->layout->disableLayout();
        $form = new Application_Form_Login();
        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)){
                $email = $form->getValue('email');
                $password = $form->getValue('password');
                $authAdapter = $this->getMembersAuthAdapter();
                $authAdapter->setIdentity($email)
                        ->setCredential($password);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if($result->isValid()){
                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(
                            null,'password'));
                    $this->_redirect('/backend');
                }
                else{
                    //Duzeltilecek..
                    echo "giris basarisiz";
                }       
            }
        } 
        $form->setAction('login');
        $this->view->form = $form;
    }

    private function getMembersAuthAdapter()
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('members')
                ->setIdentityColumn('email')
                ->setCredentialColumn('password');
        return $authAdapter;
    }

    public function fixtureOfWeekAction()
    {  
        $week = $this->_getParam('id');
        $this->view->teams = $this->_teamsModel->getAll();
        $this->view->matches = $this->_gamesModel->getAll(array('week' => $week));
        $this->view->week = $week;
        $played_matches = $this->_gamesModel->getAll(array('week' => $week,'status' => 'played'));
        $this->view->games_played_status = (count($played_matches)) ? TRUE : FALSE;
    }

    public function allWeeksAction()
    {
        if(count($this->_teamsModel->getAll())!= $this->_number_of_teams){
            $this->_helper->flashMessenger("Henüz Takımlar Eklenmemiş");
            $this->_redirect('/');
        }
        $this->view->teams = $this->_teamsModel->getAll();
        $number_of_week = ($this->_number_of_teams-1)*2;
        $matches = array();
        //Her hafta takim sayisinin yarisi kadar mac yapilacak.
        //toplamda (takimsayisi-1)*2 hafta olacak
        //toplama (takimsayisi-1)*2*(takimsayisi/2)mac olacak
        // bu da (takimsayisi-1)*takimsayisi eder.
        $number_of_all_matches = $this->_number_of_teams * ($this->_number_of_teams-1);
        //eslestirmeler duzgun yapilmis mi.?
        $all_Matches = $this->_gamesModel->getAll();
        $matches_status = (count($all_Matches) == $number_of_all_matches) ? true : false;
        
        //eslestirme yapildiysa maclari diziye gonder
        if($matches_status){
            for ($week = 1; $week <= $number_of_week; $week++) {
                $games_of_week = $this->_gamesModel->getAll(array('week' => $week));
                $games_played_status = false;
                //eslestirme yapildiysa maclar oynanmis mi.?
                if($matches_status){
                    $games_played = $this->_gamesModel->getAll(array('week' => $week, 'status' => 'played'));
                    $games_played_status = (count($games_played)) ? true : false;
                }
                array_push($matches, array('week' => $week, 
                                        'matches' => $games_of_week,
                                        'games_played_status' => $games_played_status));
            }
        }
        $this->view->matches_status = $matches_status;
        $this->view->matches = $matches;
    }
}









