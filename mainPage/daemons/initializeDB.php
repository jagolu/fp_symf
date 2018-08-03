<?php
    $conn = new mysqli("localhost", "admin", "j14g07l95-Ax92z", "app_porras");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    //Clean the tables
    $sql = "DELETE FROM player WHERE id_player>0";
    mysqli_query($conn, $sql);
    $sql = "DELETE FROM teams WHERE id_team>0";
    mysqli_query($conn, $sql);

    //Delete all images   
    $dir = '../img/';
    $handle = opendir($dir);
    $ficherosEliminados = 0;
    while ($file = readdir($handle)) {
        if (is_file($dir.$file)) {
            unlink($dir.$file);
        }
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
        $sql = "INSERT INTO teams (id_team, nombre,  pixeles) VALUES ($i+1, '".$equipos[$i]."', '".$pix."')";  //INSERT
        mysqli_query($conn, $sql);
    }

    //Get players
    $id_player = 1;
    for($i=0;$i<count($hrefs);$i++){
        $teamPage = new DomDocument;
        $teamPageURL = file_get_contents($hrefs[$i]);
        @$teamPage->loadHTML($teamPageURL);
        
        $wholeSquad = $teamPage->getElementById("plantilla");
        $squad = $wholeSquad->childNodes;
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
                                    $folder = '../img/'.$id_player.'.jpg';
                                    file_put_contents($folder, file_get_contents($img));
                                }
                                $sql = "INSERT INTO player (id_player, id_team, name, position, active) 
                                        VALUES ($id_player, $i+1, '".$namePlayer."', '".$position."', 1)";  //
                                mysqli_query($conn, $sql);
                                $id_player++;
                            }
                        }
                    }
                }
            }   
        }
    }
?>