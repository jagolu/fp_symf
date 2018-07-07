<?php
    $url=file_get_contents('http://www.laliga.es/laliga-santander');
    $html = new DomDocument;
    $teams = new DOMNodeList;
    $links = new DOMNodeList;
    $teamsCSSClass = [];

    @$html->loadHTML($url);

    $teams = $html->getElementById("equipos")->getElementsByTagName("a");

    //Aqui sacamos los equipos
    for($i=0;$i<$teams->length;$i++){
        $team = new DomNode;
        $team = $teams->item($i); //Obtenemos un equipo
        $teamName = $team->nodeValue; //Obtenemos el nombre del equipo
        $teamsCSSClass[] = $team->firstChild->firstChild->firstChild->attributes->getnamedItem("class")->value; //Obtenemos la clase CSS del equipo

        //Aqui metemos UNICAMENTE el nombre del equipo en la BD
        echo $teamName.'</br>';
        echo '       '.$teamsCSSClass[$i].'</br></br>';
    }

    unset($url);
    unset($teams);

    //Ahora vamos a obtener la ruta del fichero CSS
    echo '</br></br></br>';
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

    echo $cssUrl;
?>