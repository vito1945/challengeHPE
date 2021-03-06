<?php

namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class RPSController extends AppController
{
    
    public function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        //use a null layout to not use the cake default layout
        $this->viewBuilder()->layout('');
    }
    
    /*
    Evaluate if the matches on the tournament have a valid structure
     * It check if each match is comprised of two players with a strategy
     *  */
    public function evaluateAllMatches($tournament)
    {
        $this->autoRender = false;
        $obj = json_decode($tournament);
        $errorsFound = 0;
        foreach ($obj as $theTournament)
        {
            //debug($theTournament);
            foreach ($theTournament as $partida)
            {
                if (count($partida)!=2)
                {
                    $errorsFound = 1;
                }
                if ($this->evaluateOptions($partida)==1)
                {
                    $errorsFound = 1;
                }
            }
        }
        return $errorsFound;
    }
    
    /*
    Algorithm that solve a tournament
     */
    public function processTournament($tournament)
    {
        $evaluateMatches =$this->evaluateAllMatches($tournament);
        if ($evaluateMatches==0) //no invalid matches found
        {
            $actualWinner = "";
            $obj = json_decode($tournament);

            $winnersTournament = array();
            $winnerMatches = array();

            $winnersAllTournaments = array();

            foreach ($obj as $theTournament)//iterate through each tournament on the text file
            {            
                $currentSize = count($theTournament);
                foreach ($theTournament as $partida)
                {
                    array_push($winnersTournament, $this->evaluateGame($partida));//evaluate each match
                }
                $counter = 1;
                $nuevaPartida = array();
                $tempWinner = array();

                while (count($winnersTournament)>1){

                    foreach ($winnersTournament as $elemento)
                    {
                      if ($counter & 1) {
                            $nuevaPartida[0] = $elemento;
                        } else { 
                            $nuevaPartida[1] = $elemento;
                            array_push($tempWinner, $this->evaluateGame($nuevaPartida));
                            $nuevaPartida = array();
                        }
                        $counter++;
                    }
                    $winnersTournament = array();
                    $winnersTournament = $tempWinner;
                    $tempWinner = array();

                }
                array_push($winnersAllTournaments,$winnersTournament);
                $winnersTournament = array();
            }

            $partidaFinal = array();
            array_push($partidaFinal,$winnersAllTournaments[0][0]);
            array_push($partidaFinal,$winnersAllTournaments[1][0]);
            $ganadorFinal = $this->evaluateGame($partidaFinal);
            $segundoLugar = array();

            //Take second place
            if ( strcmp($partidaFinal[0][0], $ganadorFinal[0]) == 0 )
            {
                array_push($segundoLugar,$winnersAllTournaments[1][0]);
            }
            if ( strcmp($partidaFinal[1][0], $ganadorFinal[0]) == 0 )
            {
                array_push($segundoLugar,$winnersAllTournaments[0][0]);
            }
            echo "Results for tournament with ID=" . ($this->getCurrentChampionshipID()) . "<br><br>";

            echo "Ganador<br>";
            echo "[" .  $ganadorFinal[0] . ", " . $ganadorFinal[1] . "]";
            echo "<br><br>";

            $this->updateDatabase($ganadorFinal[0], 3);

            echo "Segundo lugar<br>";
            echo "[" .  $segundoLugar[0][0] . ", " . $segundoLugar[0][1] . "]";

            $this->updateDatabase($segundoLugar[0][0], 1);            
        }
        else
        {
            echo "Game not possible, ammount of players is incorrect (it must be 2), or options incorrect (must be R, S, P)";
            
        }
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
                "Scores.championship_id",
                "Scores.position",
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
        
        $newChampID = 0;
        
        if ($query->isEmpty())
        {
            $scoreWinner = $scoresTable->newEntity();
            $scoreWinner->name = $name;
            $scoreWinner->points = $points;
            if ($points == 3)
            {
                $scoreWinner->position = 1;                
                $newChampID = 0;
                $newChampID = $this->getCurrentChampionshipID();
                $scoreWinner->championship_id = $newChampID;
            }
            else
            {
                $scoreWinner->position = 2;
                $scoreWinner->championship_id = $this->getCurrentChampionshipID()-1;
            }
            $scoresTable->save($scoreWinner);
            
        }
        else 
        {
            $scoreWinner = $scoresTable->get($idRow);
            $scoreWinner->points = $actualPoints  + $points;
            if ($points == 3)
            {
                $newChampID = 0;
                $newChampID = $this->getCurrentChampionshipID();
                $scoreWinner->position = 1;                
                $scoreWinner->championship_id = $newChampID;
            }
            else
            {
                $scoreWinner->position = 2;
                
                $scoreWinner->championship_id = $this->getCurrentChampionshipID()-1;
            }
            $scoresTable->save($scoreWinner);
        }
    }
    
    public function getCurrentChampionshipID()
    {
        $this->autoRender = false;
        $scoresTable = TableRegistry::get('Scores');
        $newChampID = 0;
        $queryChampionshipID = $scoresTable->find("all", array(
            "fields" => array(
                "Scores.championship_id",
            )
        ));
        $queryChampionshipID->order(['championship_id' => 'DESC']);
        $data = $queryChampionshipID->toArray();
        if ($queryChampionshipID->isEmpty())
        {
            $newChampID = 1;
        }
        else
        {
            $newChampID = $data[0]['championship_id']+1;    
        }
        return $newChampID;
    }
    
    public function getChampionshipIDs()
    {
        $this->autoRender = false;
        $scoresTable = TableRegistry::get('Scores');
        $newChampID = 0;
        $queryChampionshipID = $scoresTable->find("all", array(
            "fields" => array(
                "Scores.championship_id",
            )
        ));
        $queryChampionshipID->order(['championship_id' => 'DESC']);
        $stringIDs = "";
        $arrayChamps = array();
        foreach ($queryChampionshipID as $row)
        {
            array_push($arrayChamps,$row['championship_id']);
        }
        $uniqueArray = array_unique($arrayChamps);//eliminate duplicates
        
        foreach ($uniqueArray as $row)
        {
            $stringIDs .= $row . " ";
        }        
        echo  $stringIDs;
    }

    
    
    public function clearDatabase()
    {
        $this->autoRender = false;
        $scoresTable = TableRegistry::get('Scores');
        $scoresTable->deleteAll(['points' > 1]);
        echo "Database cleared!!!";
    }
    
    public function evaluateTwoPlayerMatch($player1, $strategyPlayer1, $player2, $strategyPlayer2)
    {
        $this->autoRender = false;
        $match = array();
        
        $player1Elements = array();
        array_push($player1Elements,$player1);
        array_push($player1Elements,$strategyPlayer1);
        
        $player2Elements = array();
        array_push($player2Elements,$player2);
        array_push($player2Elements,$strategyPlayer2);
        
        array_push($match,$player1Elements);
        array_push($match,$player2Elements);
        
        echo "Winner is " . $this->evaluateGame($match)[0];
        
    }
    
    public function evaluateGame($game)
    {        
        $this->autoRender = false;//avoids view from being rendered
        $obj = $game;
        $winner = "";
        
        if ( (count($obj) == 2) && ($this->evaluateOptions($game)==0) ){        
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
            if ( (strcasecmp($obj[0][1], "R") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
            if ( (strcasecmp($obj[0][1], "S") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "R") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "S") == 0) )//case insensitive comparison
            {
                $winner = $obj[1];
            }
            if ( (strcasecmp($obj[0][1], "P") == 0) && (strcasecmp($obj[1][1], "P") == 0) )//case insensitive comparison
            {
                $winner = $obj[0];
            }
        }
        else
        {
            echo "Game not possible, ammount of players is incorrect (it must be 2), or options incorrect (must be R, S, P) ";
            
        }       
        return $winner;        
    }
    
    public function evaluateOptions($game)
    {
        $obj = $game;
        
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
    
}