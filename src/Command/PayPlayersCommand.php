<?php
namespace App\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

class PayPlayersCommand extends ContainerAwareCommand
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
        ->setName('app:payPlayers')
        // the short description shown while running "php bin/console list"
        ->setDescription('Pay the players')
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Pay the correct coins to the players')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output){
        function getOneCookie($string, $start){
            $start = strpos($string, $start."=");
            $end = strpos ($string, ";");
            $length = $end - $start +1;
            return substr ($string, $start, $length);
        }
    
        function getCookies($http_response_header){
            $cookie =       'language=es_ES; '.
                            'tiplineup_table=false; '.
                            getOneCookie($http_response_header[7], 'PHPSESSID').' '.
                            'session_language=es_ES; '.
                            getOneCookie($http_response_header[15], 'c').' '.
                            'tz=Europe%2FMadrid; '.
                            getOneCookie($http_response_header[13], 'phpbb2mysql_data').' '.
                            getOneCookie($http_response_header[14], 'phpbb2mysql_sid');
            return $cookie;
        }
    
        function getPID($string){
            $start = strpos($string, "pid=");
            return substr($string, $start+4, strlen($string));
        }
    
        function postRequest($url, $data, $cookies, $show = false){
            $options = array(
                'http' => array(
                    'header'    =>  "content-Type: application/x-www-form-urlencoded\r\n".
                                    "cookie: ".$cookies."\r\n",
                    'method'    => 'POST',
                    'content'   => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents ($url, false, $context);
            if($show){
                echo 'request--><br/>'.var_dump($options).'<br/><br/><br/><br/>response--><br/>';
                echo var_dump($http_response_header).'<br/><br/><br/><br/><br/><br/><br/><br/>';
            }
            return $http_response_header;
        }
    
        function getRequest($url, $cookies){
            $Options = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=> "Cookie: ".$cookies."\r\n"
                )
            );
            $context = stream_context_create($Options);
            $file = file_get_contents($url, false, $context);
            return $file;
        }
    
        function getRanking($cookies){
            $file = getRequest(
                'https://www.comunio.es/standings.phtml?currentweekonly_x', 
                $cookies
            );
    
            $html = new \DOMDocument();
            @$html->loadHTML($file);
            $tableRanking = $html->getElementById('tablestandings');
            foreach($tableRanking->getElementsByTagName('tr') as $player) {
                $playerStats=$player->childNodes;
                $user = [];
                $n_col = 0;
                foreach($player->childNodes as $playerStats){
                    $value = $playerStats->nodeValue;
                    foreach($playerStats->getElementsByTagName('a') as $link){
                        $user [] = getPID($link->getAttribute('href'));
                    }
                    if($value != "Jugador" && $value != "Puntos" && $n_col!=0 && $n_col!=1){
                        $user [] = $value;
                    }
                    $n_col++;
                }
                if(count($user)!=0) $ranking [] = $user;
            }
            return $ranking;
        }
    
        function getAmountAndMessageByPosition($position, $coinsPerPosition){
            $position = $position + 1;
            if(array_key_exists($position, $coinsPerPosition)){
                $amountAndMessage = array(
                    "amount"    => $coinsPerPosition[$position],
                    "message"   => $position."ª posicion."
                );
            }
            return $amountAndMessage;
        }
    
        function getAmountAndMessageByPoints($points, $coinsPerPoint){
            return array(
                "amount"    => intval($points)*$coinsPerPoint,
                "message"   => $points." puntos (".$coinsPerPoint."monedas por punto)."
            );
        }
    
        function payToAPlayer($url, $player_id, $amountAndMessage, $cookies){
            postRequest(
                $url,
                array(
                    'newsDis'       => 'messageDis',
                    'pid_to'        => $player_id,
                    'amount'        => $amountAndMessage["amount"],
                    'content'       => $amountAndMessage["message"]
                ),
                $cookies//, true
            );
        }
    
        function pay($ranking, $coinsPerPosition, $coinsPerPoint,$cookies){
            $url = 'https://www.comunio.es/administration.phtml?penalty_x';
            $file = getRequest( $url,  $cookies );
            $position = -1;
            $beforePoints = -10000000;
            $samePosition = 0;
            for($i=0;$i<count($ranking);$i++){
                $player_id = $ranking[$i][0];
                $points = $ranking[$i][1];

                if($points != $beforePoints){
                    $position = $position + $samePosition + 1;
                    $samePosition = 0;
                    $beforePoints = $points;
                }
                else $samePosition++; 

                payToAPlayer(
                    $url, $player_id, 
                    getAmountAndMessageByPosition($position, $coinsPerPosition),
                    $cookies
                );
    
                payToAPlayer(
                    $url, $player_id, 
                    getAmountAndMessageByPoints($points, $coinsPerPoint),
                    $cookies
                );
            }
        }
    
        function login($userAndPass){
            $dontHaveCookiesYet ="";
            $responseHeaders = postRequest(
                'https://www.comunio.es/login.phtml',
                array(
                    'login'     => $userAndPass["user"],
                    'pass'      => $userAndPass["pass"],
                    'action'    => 'login'
                ),
                $dontHaveCookiesYet
            );
            return getCookies($responseHeaders);
        }

        function my_substr($string, $start, $end){
            $return = "";
            for($i=$start;$i<$end;$i++){
                $return = $return.$string[$i];
            }
            return $return;
        }

        function decodeCoinsPerPosition($coinsPerPositionDB){
            $coinsPerPosition = array();
            $pos = 0;
            while($pos<strlen ($coinsPerPositionDB)){
                $posEnd = strpos ($coinsPerPositionDB, "=", $pos);
                $key = my_substr($coinsPerPositionDB, $pos, $posEnd);
                $pos = $posEnd+1;
                $posEnd = strpos ($coinsPerPositionDB, "; ",$pos);
                $value = my_substr($coinsPerPositionDB, $pos, $posEnd);
                $pos = $posEnd+2;
                array_push($coinsPerPosition, $key , $value);
            }
            return $coinsPerPosition;
        }

        function isLeapYear($year){
            return ( $year%400 == 0 || ($year%4 == 0 && $year%100 != 0));
        }
    
        function getArrayDate($mday, $mon, $year){
            return array(
                "mday"  => $mday,
                "mon"   => $mon,
                "year"  => $year
            );
        }
    
        function getNextday($date){
            $mday = $date["mday"];
            $mon = $date["mon"];
            $year = $date["year"];
            if($mday<30 && $mon!=2) $date = getArrayDate($mday+1, $mon, $year);
            else if(($mday==28 || $mday == 29) && $mon==2){
                if(isLeapYear($year) && $mday!=29) $date = getArrayDate($mday+1, $mon, $year);
                else  $date = getArrayDate(1, 3, $year);
            }
            else{
                if($mday == 31 && ( $mon == 1 || $mon == 3 || $mon == 5 || $mon == 7 || $mon == 8 || $mon == 10)){
                    $date = getArrayDate(1, $mon+1, $year);
                }
                else if($mday == 31 && $date["mon"]==12) $date = getArrayDate(1, 1, $year+1);
                else if($mday == 30 && ($mon == 4 || $mon == 6 || $mon == 9 || $mon == 11)){
                    $date = getArrayDate(1, $mon+1, $year);
                }
                else $date = getArrayDate($mday+1, $mon, $year);
            }
            return $date;
        }

        function getNextHour($date){
            $hours = $date["hours"];
            if($hours==23){
                $date = getNextday($date);
                $hours = 1;
            }
            else $hours++;
            
            return array(
                "hours"     => $hours,
                "mday"      => $date["mday"],
                "mon"       => $date["mon"],
                "year"      => $date["year"]
            );
        }

        function getComWeek(){
            $file = getRequest('https://stats.comunio.es/club_pts_history', "");
        
            $html = new \DOMDocument();
            @$html->loadHTML($file);
            $alreadyDoneWeeks = $html->getElementById('matchday_end')->getElementsByTagName('option');
            $lastWeek = $alreadyDoneWeeks[$alreadyDoneWeeks->length-1];
            return $lastWeek->nodeValue;
        }

        function getActualWeek($connection){            
            $statement = $connection->prepare(
                'SELECT number from week WHERE id="Liga"');
            $statement->execute();

            $row=$statement->fetch();
        
            return $row['number'];
        }

        function canPay($connection){
            $comWeek = getComWeek();
            $actualWeek = getActualWeek($connection);
            return !($comWeek == $actualWeek);
        }

        function updateDatabase($connection){
            $statement = $connection->prepare(
                'SELECT number from week WHERE id="Liga"');
            $statement->execute();

            $row=$statement->fetch();
            $actualWeek = $row['number']+1;
            
            $statement = $connection->prepare(
                'UPDATE week SET number='.$actualWeek.
                'WHERE id="Liga'
            );
            $statement->execute();
        }


        //JUST FOR LINUX
        function setCron($date, $command){
            $date = getNextHour($date);
            $command = "echo \"00 ".$date["hours"]." ".$date["mday"]." ".$date["mon"]." * php bin/console app:".$command."\"> ./projects/comPay/bk/crontab";
            exec($command);
            exec("crontab ./projects/comPay/bk/crontab");
        }

        /*-------------COMMAND--------------------------------*/
        $io = new SymfonyStyle($input, $output);
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();


        $statement = $connection->prepare(
            'SELECT number from week WHERE id="Liga"');
        $statement->execute();

        $row=$statement->fetch();
        $actualWeek = $row['number']+1;
        
        $statement = $connection->prepare(
            'UPDATE week SET number='.$actualWeek.
            ' WHERE id="Liga"'
        );
        $statement->execute();
        try{
            if(canPay($connection, $io)){
                $statement = $connection->prepare(
                    "SELECT username, password, coinsPerPoint, coinsPerPosition
                            FROM com_data cd");
                $statement->execute();
                
                while($row=$statement->fetch()){
                    $username = $row['username'];
                    $password = $row['password'];
                    $coinsPerPoint = $row['coinsPerPoint'];
                    $coinsPerPositionDB = $row['coinsPerPosition'];
                    $coinsPerPosition = decodeCoinsPerPosition($coinsPerPositionDB);
                    $userAndPass = array(
                        "user"      => $username,
                        "pass"      => $password
                    );

                    $sessionCookies = login($userAndPass);
            
                    $ranking = getRanking($sessionCookies);
                    pay($ranking, $coinsPerPosition, $coinsPerPoint ,$sessionCookies);
                    
                    $command = $this->getApplication()->find('app:setCron');
                    $arguments = array(
                        "command"   => "app:setCron",
                        "--yell"    => true,
                    );
                    $greetInput = new ArrayInput($arguments);
                    $command->run($greetInput, $output);
                    $io->success('All the players have been paid');
                }
            }
            else{
                setCron(getdate(), "payPlayers");
                $io->error("It's not the time to pay");
            }
        }catch(\Error $e){
            setCron(getdate(), "payPlayers");
            $io->error('Error: There is a problem in the webpage.'/*.$e*/);
        }catch(\Exception $e){
            $io->error('Error: There is a problem with the database.'/*.$e*/);
        }
    }
}
?>