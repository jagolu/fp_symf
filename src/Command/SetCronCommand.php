<?php
namespace App\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetCronCommand extends ContainerAwareCommand
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
        ->setName('app:setCron')
        // the short description shown while running "php bin/console list"
        ->setDescription('Set the new cron')
        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Set the cron to pay the next week')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output){
        function my_substr($string, $start, $end){
            $return = "";
            for($i=$start;$i<$end;$i++){
                $return = $return.$string[$i];
            }
            return $return;
        }
    
        function decodeDate($stringDate){
            $pos = 0;
            $data = [];
            for($i=0;$i<2;$i++){
                $posEnd = strpos($stringDate, '-', $pos);
                $data [] = my_substr($stringDate, $pos, $posEnd);
                $pos = $posEnd+1;
            }
            $data [] = my_substr($stringDate, $pos, strlen($stringDate));
            return array(
                "mday"  => intval($data[0]),
                "mon"   => intval($data[1]),
                "year"  => intval($data[2])
            );
        }
    
        function getLastGameDate(){
            $url=file_get_contents('https://www.laliga.es/laliga-santander');
    
            $html = new DomDocument;
            @$html->loadHTML($url);
        
            $matchs = $html->getElementById("box-resultados-competicion");
            $littleSpans = $matchs->getElementsByTagName('span');
        
            $lastGame=null;
        
            foreach($littleSpans as $i){
                if($i->hasAttribute('class') && $i->getAttribute('class')=='dia'){
                    $lastGame = $i->nodeValue;
                }
            }
            return decodeDate($lastGame);
        }
    
        function todayIsBefore($today, $matchDate){
            if($today["year"]==$matchDate["year"]){
                if($today["mon"]==$matchDate["mon"]){
                    if($today["mday"]==$matchDate["mday"]) return false;
                    else if($today["mday"]<$matchDate["mday"]) return true;
                    else return false;
                }
                else if($today["mon"]<$matchDate["mon"]) return true;
                else return false;
            }
            else if ($today["year"]<$matchDate["year"]) return true;
            else return false;
        }
    
        function showDate($date){
            return $date["mday"].'-'.$date["mon"].'-'.$date["year"];
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

        function getLeagueWeek(){
            $file = getRequest('https://www.laliga.es/laliga-santander', "");
        
            $html = new \DOMDocument();
            @$html->loadHTML($file);
            $thisWeek = $html->getElementById('fecha_jornada')->textContent;
            $startJORNADA = strpos($thisWeek, 'Jornada ', 0);
            $start = $startJORNADA+strlen('Jornada ');
            $end = strpos($thisWeek, ' ', $start)-1;
            $numberWeek = my_substr( $thisWeek, $start, $end);
            return $numberWeek;
        }

        function getActualWeek($connection){            
            $statement = $connection->prepare(
                'SELECT number from week WHERE id="Liga"');
            $statement->execute();

            $row=$statement->fetch();
        
            return $row['number'];
        }

        function canCron(){
            $leagueWeek = getLeagueWeek();
            $actualWeek = getActualWeek();
            return ($leagueWeek == $actualWeek);
        }

        //JUST FOR LINUX
        function setCron($date, $command, $nextHour=false){
            if($nextHour) $date = getNextHour($date);
            else array_push($date, "hours" , 21);
            $command = "echo \"00 ".$date["hours"]." ".$date["mday"]." ".$date["mon"]." * php bin/console app:".$command."\"> ./projects/comPay/bk/crontab";
            exec($command);
            exec("crontab ./projects/comPay/bk/crontab");
        }
    /*-------------------------------------COMMAND------------------------------------*/    
        
        $io = new SymfonyStyle($input, $output);
        $today = getdate();
        try{
            if(canCron()){
                $lastGame = getLastGameDate();
            
                if(todayIsBefore($today, $lastGame)) setCron($lastGame, "payPlayers");
                else setCron($lastGame, "setCron");

                $io->success('The cron has been set');    
            }
            else{
                setCron(getdate(), "setCron");
                $io->error('Cant cron right now');
            }
        }
        catch (\Error $e){
            setCron(getdate(), "setCron");
            $io->error('There is a problem in the webpage.'.$e);
        }
    }
}
?>