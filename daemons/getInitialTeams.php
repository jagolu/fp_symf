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
        $sql = "INSERT INTO equipos (nombre,  pixeles) VALUES ('".$equipos[$i]."', '".$pix."')";  //Insertamos dichos valores en la BD
        mysqli_query($conn, $sql);
    }
?>