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
    $equipos = []; //Esta variable luego la borramos

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
            $type = $ato->getNamedItem("type");
            $href = $ato->getNamedItem("href");
            if($type!=null && $type->value =="text/css"){
                $cssUrl = $href->value;
                break;
            }
        }
    }
    
    unset($links);

    $css = file_get_contents($cssUrl);
    for($i=0;$i<count($teamsCSSClass);$i++){
        $start= strpos($css, $teamsCSSClass[$i]) + strlen($teamsCSSClass[$i])+1;
        $subarray="";
        for($j=$start;$css[$j]!='}';$j++){
            $subarray = $subarray . $css[$j];
        }
        $start = strpos($subarray, "background-position:")+strlen("background-position:");
        $pix="";
        for($j=$start;$j<strlen($subarray);$j++){
            $pix = $pix.$subarray[$j];
        }
        $sql = "INSERT INTO equipos (nombre,  pixeles) VALUES ('".$equipos[$i]."', '".$pix."')";
        mysqli_query($conn, $sql);
        /*echo $equipos[$i].'</br>';
        echo "
            <span style=\"display: block; 
            background-image: url(https://statics.laliga.es/img/sprite-escudos-2019-v1.png); 
            background-position:".$pix."; 
            background-size: 40px 1720px; 
            width: 40px; 
            height: 40px;\"></span></br>";*/

    }
?>