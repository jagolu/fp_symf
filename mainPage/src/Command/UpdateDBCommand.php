<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityRepository;
use DOMDocument;
use DOMNodeList;
use DOMNode;
use DOMNamedNodeMap;

class UpdateDBCommand extends ContainerAwareCommand
{
    public function __construct(bool $requirePassword = false)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('app:updateDB')

        // the short description shown while running "php bin/console list"
        ->setDescription('Update the player table')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Update the not active players and insert the new ones')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();

        $url=file_get_contents('http://www.laliga.es/laliga-santander');
        $html = new DomDocument;
        $teams = new DOMNodeList;
        $links = new DOMNodeList;
        $teamsCSSClass = [];
        $equipos = [];
        $hrefs = [];

        @$html->loadHTML($url);

        $teams = $html->getElementById("equipos")->getElementsByTagName("a");

        //Get ALL teams
        for($i=0;$i<$teams->length;$i++){
            $team = new DomNode;
            $team = $teams->item($i); //Get the team
            
            $equipos [] = $team->nodeValue; //Get team name
            $hrefs [] = $team->attributes->getNamedItem('href')->value; //Get url page team
        }

        unset($url);
        unset($teams);

        //Get players
        $id_player = 1;
        $allPlayers = [];

        for($i=0;$i<count($hrefs);$i++){
            //cmd screen
            $output->writeln("Updating players of ".$equipos[$i]);
            //end cmd screen
            $teamPage = new DomDocument;
            $teamPageURL = file_get_contents($hrefs[$i]);
            @$teamPage->loadHTML($teamPageURL);
            $thisTeamPlayers = [];
            
            $wholeSquad = $teamPage->getElementById("plantilla");
            $squad = $wholeSquad->childNodes;

            //cmd screen
            $io = new SymfonyStyle($input, $output);
            $io->progressStart();
            //cmd screen end

            for($j=0;$j<$squad->length;$j++){
                $position = 0; //If is 0 we don't insert on the DB, if 1 the player is a goalkeeper, if is 2 is pitch player
                for($k=0;$k<$squad[$j]->childNodes->length;$k++){
                    $nodeChild = $squad[$j]->childNodes[$k];
                    if($nodeChild->hasChildNodes()){
                        if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue == 'Porteros'){
                            $position = 1;
                        }
                        else if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue == 'Defensas'){
                            $position = 2;
                        }
                        else if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue == 'Centrocampistas'){
                            $position = 3;
                        }
                        else if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue == 'Delanteros'){
                            $position = 4;
                        }
                        if($nodeChild->getAttribute ("class") == 'posiciones' && ($position >0 || $position < 5)){
                            //We watch every player in every position
                            for($t=0;$t<$nodeChild->childNodes->length;$t++){
                                $grandChild = $nodeChild->childNodes[$t];
                                if($grandChild->hasChildNodes()){
                                    $grandChildSon = $grandChild->childNodes[1];
                                    $namePlayer = $grandChild->childNodes[3]->nodeValue; //Get the name player
                                    $statement = $connection->prepare("SELECT active FROM player 
                                                                        WHERE id_team = $i+1 AND name = \"$namePlayer\"");

                                    $statement->execute();
                                    $isThePlayerActive = $statement->fetch()['active'];
                                    
                                    if($isThePlayerActive == null){
                                        //The players doesn't exists
                                        $statement = $connection->prepare("SELECT count(*) FROM player");
                                        $statement->execute();
                                        $numberTotalOfPlayers = $statement->fetch()['count(*)'];
                                        $numberTotalOfPlayers = $numberTotalOfPlayers+1;

                                        if($grandChildSon->hasChildNodes()){
                                            $img = $grandChildSon->childNodes[1]->getAttribute("src");
                                            $folder = 'img/'.$numberTotalOfPlayers.'.jpg';
                                            file_put_contents($folder, file_get_contents($img));
                                        }
                                        $statement = $connection->prepare("INSERT INTO player (id_player, id_team, name, fullName, position, active, goals, shots, passes, assits, recoveries, goals_conceded) 
                                                                           VALUES ($numberTotalOfPlayers, $i+1, \"".$namePlayer."\", '".$position."', 1, 0, 0, 0, 0, 0, 0)");
                                        $statement->execute();
                                        $output->writeln("A player didnt exist");
                                    }
                                    else if($isThePlayerActive == 0){
                                        //The player exists but is not active
                                        $statement = $connection->prepare("UPDATE player SET active = 1 
                                                                           WHERE id_team = $i+1 AND name = \"$namePlayer\"");
                                        $statement->execute();
                                        $output->writeln("A player that existed wasnt active");
                                    }

                                    $thisTeamPlayers [] = $namePlayer;
                                    //cmd screen
                                    $io->progressAdvance(1); 
                                    //end cmd screen*/
                                }
                            }
                        }
                    }
                }   
            }
            $allPlayers [] = $thisTeamPlayers;
            $io->progressFinish();
        }

        //Check if a player is inactive
        $totalPlayersRightNow = 0;
        for($idTeam=1; $idTeam<(count($allPlayers)+1); $idTeam++){
            $statement = $connection->prepare("SELECT id_player, name, active FROM player 
                                        WHERE id_team = $idTeam");
            $statement->execute();
            while($row=$statement->fetch()){
                $theName = $row['name'];
                $isInIt = false;
                if(!in_array($theName, $allPlayers[$idTeam-1]) && $row['active']==1){
                    $statement2 = $connection->prepare("UPDATE player SET active=0 
                                                WHERE id_team = $idTeam AND name = \"$theName\"");
                    $statement2->execute();
                    $output->writeln("A player has set innactive");
                    $totalPlayersRightNow = $totalPlayersRightNow-1;
                }
                $totalPlayersRightNow = $totalPlayersRightNow+1;
            }
        }
        $io->success('All the players have been updated in our DB!!');
        $io->success('There are '.$totalPlayersRightNow.' active players');
    }
}
?>