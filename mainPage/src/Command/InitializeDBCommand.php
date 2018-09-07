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

class InitializeDBCommand extends ContainerAwareCommand
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
        ->setName('app:initializeDB')

        // the short description shown while running "php bin/console list"
        ->setDescription('Initialize the database with teams and players')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Clean the DB and initialize with the actual teams and actual players')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("DELETE FROM user_room WHERE id_user>0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM seasonbet WHERE id_user>0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM player WHERE id_player>0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM team WHERE id_team>0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM user WHERE id_user>0");
        $statement->execute();
        $statement = $connection->prepare("DELETE FROM room WHERE id_room>0");
        $statement->execute();

        //Delete all images   
        $dir = 'img/';
        $handle = opendir($dir);
        $ficherosEliminados = 0;
        while ($file = readdir($handle)) {
            if (is_file($dir.$file)) {
                unlink($dir.$file);
            }
            $ficherosEliminados++;
        }

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
            $teamName = $team->nodeValue; //Get team name
            $badClass = $team->firstChild->firstChild->firstChild->attributes->getnamedItem("class")->value; //Get the CSS class of the team
            $class = "";
            for($j=0;$j<strlen($badClass);$j++){   //Shift space of the call for the point of the CSS class
                if($badClass[$j] == ' ') $class= $class . ".";
                else $class = $class . (string)$badClass[$j];
            }
            
            $teamsCSSClass []= $class;
            $equipos [] = $teamName; 
            $hrefs [] = $team->attributes->getNamedItem('href')->value; //Get url page team
        }

        unset($url);
        unset($teams);

        //Get path of CSS file
        $cssUrl;

        $links = $html->getElementsByTagName("link");

        for($i=0;$i<$links->length;$i++){
            $ato = new DomNamedNodeMap;
            $link=$links->item($i);
            $ato = $link->attributes;
            if($ato!=null){
                $type = new DomNamedNodeMap;
                $href = new DomNamedNodeMap;
                $type = $ato->getNamedItem("type");  //look for type="text/css"
                $href = $ato->getNamedItem("href"); //Look for the link of the css file
                if($type!=null && $type->value =="text/css"){
                    $cssUrl = $href->value;
                    break;
                }
            }
        }
        
        unset($links);

        $css = file_get_contents($cssUrl);
        for($i=0;$i<count($teamsCSSClass);$i++){
            $start= strpos($css, $teamsCSSClass[$i]) + strlen($teamsCSSClass[$i])+1;  //In the CSS file, look for the CSS class
            $subarray="";
            for($j=$start;$css[$j]!='}';$j++){   //Save what is between brackets in a string var
                $subarray = $subarray . $css[$j];
            }
            $start = strpos($subarray, "background-position:")+strlen("background-position:");  //Now I look for what I need inside the string var
            $pix="";
            for($j=$start;$j<strlen($subarray);$j++){   //Get the background-position
                $pix = $pix.$subarray[$j];
            }
            $statement = $connection->prepare("INSERT INTO team (id_team, name,  pix, position) VALUES ($i+1, '".$equipos[$i]."', '".$pix."', 1)");
            $statement->execute();
        }

        //Get players
        $id_player = 1;
        for($i=0;$i<count($hrefs);$i++){
            //cmd screen
            $output->writeln("Indexing players of ".$equipos[$i]);
            //end cmd screen
            $teamPage = new DomDocument;
            $teamPageURL = file_get_contents($hrefs[$i]);
            @$teamPage->loadHTML($teamPageURL);
            
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
                                    $namePlayer = $grandChild->childNodes[3]->nodeValue;
                                    if($grandChildSon->hasChildNodes()){
                                        $img = $grandChildSon->childNodes[1]->getAttribute("src");
                                        $folder = 'img/'.$id_player.'.jpg';
                                        file_put_contents($folder, file_get_contents($img));
                                        /*Get the full name of the player */
                                        $playerURL = $grandChild->getAttribute("href");
                                        if($playerURL!=null){
                                            $urlOfThePlayer=file_get_contents($playerURL);
                                            $html2 = new DomDocument;
                                            @$html2->loadHTML($urlOfThePlayer);
                                            $fullNamePlayer = $html2->getElementById("nombre")->textContent;
                                            /* End get the full name of the player */
                                        }
                                        else $fullNamePlayer = $namePlayer;
                                    }
                                    $statement = $connection->prepare("INSERT INTO player (id_player, id_team, name, fullName, position, active, goals, shots, passes, assits, recoveries, goals_conceded) 
                                                                       VALUES ($id_player, $i+1, \"".$namePlayer."\", \"".$fullNamePlayer."\",'".$position."', 1, 0, 0, 0, 0, 0, 0)");
                                    $statement->execute();
                                    $id_player++;
                                    //cmd screen
                                    $io->progressAdvance(1); 
                                    //end cmd screen
                                }
                            }
                        }
                    }
                }   
            }
            $io->progressFinish();
        }
        $io->success('All the teams and their players are in our DB!!');
    }
}
?>