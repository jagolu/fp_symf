<?php
    $url=file_get_contents('http://www.laliga.es/laliga-santander');
    $html = new DomDocument;
    $dom = new DOMDocument('1.0', 'iso-8859-1');
    $teams = new DOMNodeList;

    @$html->loadHTML($url);

    $dom = $html->getElementById("equipos");

    $teams = $dom->getElementsByTagName("a");

    for($i=0;$i<$teams->length;$i++){
        $teamName = $teams->item($i)->firstChild->nodeValue;
        echo $teamName.'</br>';
        //Aqui habria que insertarlos en la BD
    }
?>