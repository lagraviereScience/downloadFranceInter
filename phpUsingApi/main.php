<?php


$jsonOrig = file_get_contents("https://www.radiofrance.fr/api/v2.0/path?value=franceinter/podcasts/le-jeu-des-1000?page=447");
$jsonDecoded = json_decode($jsonOrig, true);

//var_dump($jsonDecoded);
/*echo $jsonDecoded["content"]["expressions"]["next"];

if(is_null($jsonDecoded["content"]["expressions"]["next"]))
{
    echo "it's EMPTYY!!!";
}*/


echo getMaxPage("https://www.radiofrance.fr/api/v2.0/path?value=franceinter/podcasts/le-jeu-des-1000");

function checkEmpty(array $toCheck) : bool
{
    return is_null($toCheck["content"]["expressions"]["next"]);
}
    
function getMaxPage(string $url) : int
{
    $lowerBound = 1;
    $upperBound = 1;
    
    while(true){
        $content= json_decode(file_get_contents($url. "&page=" . $upperBound), true);
        
        if(checkEmpty($content)){
            break;
        }
        $lowerBound = $upperBound + 1;
        $upperBound <<= 1;
    }
    
    $ans = $lowerBound;
    
    while($lowerBound <= $upperBound){
        $mid = $lowerBound + (($upperBound - $lowerBound) >> 1);
        $content= json_decode(file_get_contents($url. "&page=" . $mid), true);
        //$myUrl = $url. "&p=" . $mid;
        
        if(checkEmpty($content)){
            $upperBound = $mid - 1;
        }else{
            $lowerBound = $mid + 1;
            $ans = $lowerBound;
        }
    }
    
    return $ans-1;
}
