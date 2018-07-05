<?php
    $html=file_get_contents('http://www.laliga.es/laliga-santander');
    $dom = new DomDocument;

    @$dom->loadHTML($html);


    $doc = new DOMXpath($dom);
    $doc->query("//*[@id='equipos']");
    
    
    //echo $doc->saveHTML();

    //var_dump(count($teams));

    /*foreach ($teams as $team){
        $result=var_dump(count($team->getElementByClassName('nombre')));
        echo "hola";
        echo $result;
    }*/

?>