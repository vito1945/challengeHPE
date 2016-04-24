<?php

namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class RPSController extends AppController
{
    public function index()
    {
        
    }
    
    public function processTournament($tournament)
    {
        //$this->autoRender = false;
        $actualWinner = "";
        $obj = json_decode($tournament);
        /*
         * Helps to determine if the match number is even or no
         */
        $matchCounter = 0;
        $totalMatches = 0;
                
        $winnersTournament = array();
        $winnerMatches = array();
        
        $winnersAllTournaments = array();
        
        foreach ($obj as $theTournament)
        {            
            $currentSize = count($theTournament);
            foreach ($theTournament as $partida)
            {
                array_push($winnersTournament, $this->evaluateGame($partida));
            }
            
            $counter = 1;
            $nuevaPartida = array();
            $tempWinner = array();
                            
            while (count($winnersTournament)>1){
                
                foreach ($winnersTournament as $elemento)
                {
                  if ($counter & 1) {
                        echo $counter . ' odd';
                        $nuevaPartida[0] = $elemento;
                    } else { 
                        echo $counter . 'even';
                        $nuevaPartida[1] = $elemento;
                        //debug($nuevaPartida);
                        array_push($tempWinner, $this->evaluateGame($nuevaPartida));
                        $nuevaPartida = array();
                    }
                    $counter++;
                }
                $winnersTournament = array();
                $winnersTournament = $tempWinner;
                $tempWinner = array();
                
            }
            //debug($winnersTournament);
            array_push($winnersAllTournaments,$winnersTournament);
            $winnersTournament = array();
        }
        
        debug($winnersAllTournaments);
        
        echo "------------------------------";
        /***********/
        $partidaFinal = array();
        array_push($partidaFinal,$winnersAllTournaments[0][0]);
        array_push($partidaFinal,$winnersAllTournaments[1][0]);
        $ganadorFinal = $this->evaluateGame($partidaFinal);
        $segundoLugar = array();
        
        debug($partidaFinal);
        /*Take second place*/
        if ( strcmp($partidaFinal[0][0], $ganadorFinal[0]) == 0 )
        {
            array_push($segundoLugar,$winnersAllTournaments[1][0]);
        }
        if ( strcmp($partidaFinal[1][0], $ganadorFinal[0]) == 0 )
        {
            array_push($segundoLugar,$winnersAllTournaments[0][0]);
        }
        echo "Ganador";
        debug($ganadorFinal);
        
        $this->updateDatabase($ganadorFinal[0], 3);
       
        
        echo "Segundo lugar";
        debug($segundoLugar);
        
        $this->updateDatabase($segundoLugar[0][0], 1);
        /***********/
        
    }
    
    public function updateDatabase($name, $points)
    {        
        $this->autoRender = false;
        $scoresTable = TableRegistry::get('Scores');
        
        /*
         * Check if user exist on database
         */
        $query = $scoresTable->find("all", array(
            "fields" => array(
                "Scores.id",
                "Scores.name",
                "Scores.points"
            ),
            'conditions' => ['Scores.name = ' => $name]
        ));
        
        $idRow = "";
        $actualPoints = 0;
        foreach ($query as $row)
        {
            $idRow = $row['id'];
            $actualPoints = $row['points'];
        }
        
        if ($query->isEmpty())
        {
            $scoreWinner = $scoresTable->newEntity();
            $scoreWinner->name = $name;
            $scoreWinner->points = $points;
            $scoresTable->save($scoreWinner);
            
        }
        else 
        {
            //debug("HACER UPDATE");
            
            $scoreWinner = $scoresTable->get($idRow);
            $scoreWinner->points = $actualPoints  + $points;
            $scoresTable->save($scoreWinner);
        }
    }
    
    public function clearDatabase()
    {
        $this->autoRender = false;
        debug("ENTRO AL CLEAR");
        $scoresTable = TableRegistry::get('Scores');
        $scoresTable->deleteAll(['points' > 1]);
    }
    
    public function evaluateGame($game)
    {        
        $this->autoRender = false;//avoids view from being rendered
        //$obj = json_decode($game);
        $obj = $game;
        
        /*var_dump($options);
        var_dump($obj);*/
        $winner = "";
        
        if ( (count($obj) == 2) && ($this->evaluateOptions($game)==0) ){
        
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is ". $obj[0][0] . " since its the first player"; //draw
            }
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is " . $obj[0][0] . " since rock beats scissors";
            }
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
                //$winner = "Winner is " . $obj[1][0] . " since paper beats rock";
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
                //$winner = "Winner is " . $obj[1][0] . " since rock beats scissors";
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is ". $obj[0][0] . " since its the first player"; //draw
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is " . $obj[0][0] . " since scissors beats paper";
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is " . $obj[0][0] . " since paper beats rock";
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
                //$winner = "Winner is " . $obj[1][0] . " since scissors beats paper";
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
                //$winner = "Winner is ". $obj[0][0] . " since its the first player"; //draw
            }
        }
        else
        {
            echo "Game not possible, ammount of players is incorrect (it must be 2), or options incorrect (must be R, S, P) ";
            
        }
        //return 0;
        
        /*echo $obj[0][1]  . "---";
        echo $obj[1][1] . "---";*/
        
        /*$this->set('winner',$winner);
        $this->set('game', $game);*/
        
        return $winner;
        /*return false;*/
        
    }
    
    public function evaluateOptions($game)
    {
        //$obj = json_decode($game);
        $obj = $game;
        
        //var_dump($obj);
        $result = 0;
        
        if ( (strcasecmp($obj[0][1],"R")!=0) && (strcasecmp($obj[0][1],"S")!=0) && (strcasecmp($obj[0][1],"P")!=0))
        {
            $result = 1;
        }
        if ( (strcasecmp($obj[1][1],"R")!=0) && (strcasecmp($obj[1][1],"S")!=0) && (strcasecmp($obj[1][1],"P")!=0))
        {
            $result = 1;
        }
        return $result;
    }
    
    public function evaluateIndividualMatch($player1, $player2)
    {
        /*$winner = "";
        if ( (strcasecmp($player1, "R") == 0) && (strcasecmp($player2, "R") == 0) )//case insensitive comparison
        {
            $winner = "Draw";
        }*/
        /*if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
        {
            $winner = $obj[0][0];
        }*/
        
        return false;
    }
    
}