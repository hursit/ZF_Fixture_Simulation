<?php

class BackendController extends Zend_Controller_Action
{

    protected $_number_of_teams = 4;

    protected $_teamsModel = null;

    protected $_gamesModel = null;

    public function init()
    {
        $this->_teamsModel = new Application_Model_DbTable_Teams();
        $this->_gamesModel = new Application_Model_DbTable_Games();   
    
        if ($this->_helper->FlashMessenger->hasMessages()) {
           $this->view->messages = $this->_helper->FlashMessenger->getMessages();
        }
    }

    public function indexAction()
    {
        $this->view->teams = $this->_teamsModel->getAll();
        $form = new Application_Form_Team();
        $form->init();
        $form->add();
        
        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)){
                try {
                    unset($formData['submit']);
                     $this->_teamsModel->add($formData);
                     $this->_helper->flashMessenger("Takım Eklendi..");
                     $this->_redirect('/backend');
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
        }  
        $allTeams = $this->_teamsModel->getAll();
        
        // Takim sayisindan fazla eklenmemeli
        if(count($allTeams) < $this->_number_of_teams ){
            $form->setAction("/backend");
            $this->view->form = $form;
        } 
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index','index');
    }

    public function editTeamAction()
    {
        
        $team_id = $this->_getParam('id');
        $form = new Application_Form_Team(array('team_id'=> $team_id));
        $form->init();
        $form->edit();
        
        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)){
                try {
                    unset($formData['submit']);
                     $this->_teamsModel->edit($team_id,$formData);
                     $this->_helper->flashMessenger("Takım Güncellendi..");
                     $this->_redirect('/backend');
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
        }
        
        $form->setAction('/backend/edit-team/'.$team_id);
        $this->view->form = $form;
    }

    public function deleteTeamAction()
    {
        $team_id = $this->_getParam('id');
        try {
            $this->_teamsModel->deleteTeam($team_id);
            $this->_gamesModel->truncateGames();
            $this->_helper->flashMessenger("Takım ve Eşleşmeler Silindi..");
            $this->_redirect('/backend');
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function gamesAction()
    {
        if(count($this->_teamsModel->getAll())!= $this->_number_of_teams){
            $this->_helper->flashMessenger("Takımları eklemeden simulasyon yapamazsınız..(4 takım eklemelisiniz)");
            $this->_redirect('/backend');
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

    public function matchAction()
    {
        //Tablomuzu bosaltalim
        $this->_gamesModel->truncateGames();
        //Butun takimlari cekelim bir array'e atalim.
        $teams = $this->_teamsModel->getAll()->toArray();
        //Takimlari karistiralim
        shuffle($teams);
        
        //Bir takimi pivot alalim digerlerini ona gore atayalim
        $pivot = $teams[0];
        unset($teams[0]);
        
        //eslestirmeler burada olacak
        $matches = array();
        //ilk sezon haftalari sayisi
        $first_season_weeks = $this->_number_of_teams-1;
        for ($index = 1; $index <= $first_season_weeks; $index++) {
            $other_teams = $teams;
            //ilk macimiz
            array_push($matches, array('week'=> $index,'teams' => array($pivot,$other_teams[$index])));
            unset($other_teams[$index]);
            
            //kalan takimlar diger macimiz
            $other_first_team = null;
            $other_second_team = null;
            foreach ($other_teams as $team) {
                if($other_first_team == NULL){
                    $other_first_team = $team;
                }
                else{
                    $other_second_team = $team;
                }
            }
            array_push($matches, array('week'=> $index,'teams' => array($other_first_team,$other_second_team)));
        }
        //simdi bunlari veritabanina kaydedelim
        //Ikinci yariyi orda yer degistirerek daha rahat yapabiliriz
        $this->_gamesModel->addMatches($matches);
        $this->_helper->flashMessenger("Eşleştirme Yapıldı..");
        $this->_redirect('/backend/games');
    }

    public function playGamesAction()
    {
        $week = $this->_getParam('id');
        $this->play_games($week);
        $this->_helper->flashMessenger("Bu Haftanın Maçları Oynatıldı..");
        $this->_redirect('/backend/games');
    }
    protected function play_games($week){
        $matches = $this->_gamesModel->getAll(array('week'=> $week));
        foreach ($matches as $match) {
            $first_team = $this->_teamsModel->getByFilter(array('id' => $match->first_team_id));
            $second_team = $this->_teamsModel->getByFilter(array('id' => $match->second_team_id));
            
            $first_team_goal =(int)(rand(0,$first_team->strength) / 10);
            $second_team_goal = (int)(rand(0,$second_team->strength) / 10);
            $data = array(
                'first_team_goal' => $first_team_goal,
                'second_team_goal' => $second_team_goal,
                'status' => 'played'
            );
            $this->_gamesModel->edit($match->id, $data);
        }
    }

    public function scoreEditAction()
    {
        $match_id = $this->_getParam('id');
        $form = new Application_Form_Scoreedit(array('match_id' => $match_id));
       
        if($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)){
                try {
                    unset($formData['submit']);
                     $this->_gamesModel->edit($match_id,$formData);
                     $this->_helper->flashMessenger("Skor Güncellendi..");
                     $this->_redirect('/backend/games');
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
        }
        $form->setAction('/backend/score-edit/'.$match_id);
        $this->view->form = $form;
    }

    public function resetMatchesAction()
    {
        try {
            $this->_gamesModel->truncateGames();
            $this->_helper->flashMessenger("Bütün eşleşmeler silindi..");
            $this->_redirect('/backend/games');
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function playAllWeeksAction()
    {
        try {
            //hafta sayisi = (takim sayisi - 1) * 2
            for ($week = 1; $week <= ($this->_number_of_teams-1)*2; $week++) {
                    $games_played = $this->_gamesModel->getAll(array('week' => $week, 'status' => 'played'));
                    $games_played_status = (count($games_played)) ? true : false;
                    if(!$games_played_status){
                        $this->play_games($week);
                    }
            }
            $this->_helper->flashMessenger("Bütün Maçlar Oynatıldı..");
            $this->_redirect('/backend/games');
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }


}















