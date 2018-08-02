<?php
    $conn = new mysqli("localhost", "admin", "j14g07l95-Ax92z", "app_porras");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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

    //Aqui sacamos los equipos
    for($i=0;$i<$teams->length;$i++){
        $team = new DomNode;
        $team = $teams->item($i); //Obtenemos un equipo
        $teamName = $team->nodeValue; //Obtenemos el nombre del equipo
        $badClass = $team->firstChild->firstChild->firstChild->attributes->getnamedItem("class")->value; //Obtenemos el CSS de la clase del equipo
        $class = "";
        for($j=0;$j<strlen($badClass);$j++){   //Sustituimos el espacio de llamada por el punto de la clase en el css
            if($badClass[$j] == ' ') $class= $class . ".";
            else $class = $class . (string)$badClass[$j];
        }
        
        $teamsCSSClass []= $class;
        $equipos [] = $teamName; 
        //Sacamos la url del equipo 
        $hrefs [] = $team->attributes->getNamedItem('href')->value;
        //Fin de sacar la url del equipo
    }

    unset($url);
    unset($teams);

    //Ahora vamos a obtener la ruta del fichero CSS
    $cssUrl;

    $links = $html->getElementsByTagName("link");

    for($i=0;$i<$links->length;$i++){
        $ato = new DomNamedNodeMap;
        $link=$links->item($i);
        $ato = $link->attributes;
        if($ato!=null){
            $type = new DomNamedNodeMap;
            $href = new DomNamedNodeMap;
            $type = $ato->getNamedItem("type");  //Buscamos el type (para que sea type="text/css")
            $href = $ato->getNamedItem("href"); //Buscamos el enlace al fichero css
            if($type!=null && $type->value =="text/css"){
                $cssUrl = $href->value;
                break;
            }
        }
    }
    
    unset($links);

    $css = file_get_contents($cssUrl);
    for($i=0;$i<count($teamsCSSClass);$i++){
        $start= strpos($css, $teamsCSSClass[$i]) + strlen($teamsCSSClass[$i])+1;  //Buscamos en el fichero CSS la clase CSS del equipo en cuestion
        $subarray="";
        for($j=$start;$css[$j]!='}';$j++){   //Guardamos lo que hay entre corchetes en un string
            $subarray = $subarray . $css[$j];
        }
        $start = strpos($subarray, "background-position:")+strlen("background-position:");  //Dentro del string buscamos lo que nos interesa
        $pix="";
        for($j=$start;$j<strlen($subarray);$j++){   //Obtenemos los valores de background-position
            $pix = $pix.$subarray[$j];
        }
        //$sql = "INSERT INTO teams (nombre,  pixeles) VALUES ('".$equipos[$i]."', '".$pix."')";  //Insertamos dichos valores en la BD
        //mysqli_query($conn, $sql);
    }

    //Obtener jugadores
    for($i=0;$i<count($hrefs);$i++){
        $teamPage = new DomDocument;
        $teamPageURL = file_get_contents($hrefs[$i]);
        @$teamPage->loadHTML($teamPageURL);
        
        $wholeSquad = $teamPage->getElementById("plantilla");
        $squad = $wholeSquad->childNodes;
        for($j=0;$j<$squad->length;$j++){
            $canBe = 0; //If is 0 we don't insert on the DB, if 1 the player is a goalkeeper, if is 2 is pitch player
            for($k=0;$k<$squad[$j]->childNodes->length;$k++){
                $nodeChild = $squad[$j]->childNodes[$k];
                if($nodeChild->hasChildNodes()){
                    if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue = 'Porteros'){
                        $canBe = 1;
                    }
                    if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue = 'Defensas'){
                        $canBe = 2;
                    }
                    if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue = 'Centrocampistas'){
                        $canBe = 2;
                    }
                    if($nodeChild->getAttribute ("class") == 'titulo_posicion' && $nodeChild->nodeValue = 'Delanteros'){
                        $canBe = 2;
                    }
                    if($nodeChild->getAttribute ("class") == 'posiciones' && ($canBe == 1 || $canBe == 2)){
                        //Recorremos los jugadores de cada posicion
                        for($t=0;$t<$nodeChild->childNodes->length;$t++){
                            $grandChild = $nodeChild->childNodes[$t];
                            if($grandChild->hasChildNodes()){
                                $grandChildSon = $grandChild->childNodes[1];
                                $namePlayer = $grandChild->childNodes[3]->nodeValue;
                                if($grandChildSon->hasChildNodes()){
                                    $img = $grandChildSon->childNodes[1]->getAttribute("src");
                                }
                                echo $namePlayer;
                                echo '<img src="'.$img . '" width="75" height="75"></br></br></br>';
                            }
                        }
                    }
                }
            }   
            echo '</br></br>';
        }
    }
?>