<?php

namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class WebServicesController extends AppController
{
     public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        $scoresTable = TableRegistry::get('Scores');
        $scores = $scoresTable->find('all');
        $this->set([
            'scores' => $scores,
            '_serialize' => ['scores']
        ]);
    }

    /*
     * Example URL
    http://localhost:8383/ChallengeHPE/WebServices/getTopPlayers/3.json
    */
    public function getTopPlayers($numberOfPlayers)
    {  
        $scoresTable = TableRegistry::get('Scores');
        $queryTop = $scoresTable->find("all", array(
            "fields" => array(
                "Scores.name",
                "Scores.points"
            )
        ))->limit($numberOfPlayers);
        
        $queryTop->order(['points' => 'DESC']);
        
        $this->set([
            'scores' => $queryTop,
            '_serialize' => ['scores']
        ]);   
    }
    
    /*
     * Example URL
    http://localhost:8383/ChallengeHPE/WebServices/championshipResults/23.json
     */
    public function championshipResults($idChampionship)
    {
        $scoresTable = TableRegistry::get('Scores');
        $queryResults = $scoresTable->find("all", array(
            "fields" => array(
                "Scores.name",
                "Scores.position",
                "Scores.championship_id"
            ),
            'conditions' => ['Scores.championship_id =' => $idChampionship]
        ));
        
        $this->set([
            'scores' => $queryResults,
            '_serialize' => ['scores']
        ]); 
        
    }
}