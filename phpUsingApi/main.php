<?php
//$jsonOrig = file_get_contents("https://www.radiofrance.fr/api/v2.0/path?value=franceinter/podcasts/le-jeu-des-1000?page=447");
//$jsonDecoded = json_decode($jsonOrig, true);

$baseUrl = "https://www.radiofrance.fr/api/v2.0/path?value=franceinter/podcasts/le-jeu-des-1000";
$maxPage = getMaxPage($baseUrl);
//echo $maxPage . "\n";

for($i=1;$i<=$maxPage;$i++)
{
    $urlToDownload = $baseUrl . "&page=" . $i;
    $jsonOrig = file_get_contents($urlToDownload);
    $jsonDecoded = json_decode($jsonOrig, true);
    foreach($jsonDecoded["content"]["expressions"]["items"] as $items)
    {
        downloadManager($items["manifestations"][0]["url"]);
        //$date = extractDate($urlOfMp3);
        //echo $date . " - " . $urlOfMp3 . "\n";
    }
}

function downloadManager($url) : void
{
    if($url == "" or is_null($url))
    {
        return;
    }

    $date = extractDate($url);
    $year = substr($date, -4);

    if(!file_exists($year))
    {
        mkdir($year);
    }
    $destination= $year . "/" . $date . ".mp3";
    echo "$destination" . "\n"; 
    file_put_contents($destination, fopen($url, 'r'));
}

function extractDate(string $myString) : string
{
    $pattern = '/(\d{2}\.\d{2}\.\d{4})/';  // Regular expression pattern for dd.mm.yyyy format
    $matches = array();
    $date = "";

    if (preg_match($pattern, $myString, $matches)) {
        $date = $matches[0];
    }
    return $date;
}

function checkEmpty(array $toCheck) : bool
{
    return is_null($toCheck["content"]["expressions"]["next"]);
}
    
function getMaxPage(string $url) : int
{
    $lowerBound = 1;
    $upperBound = 300;
    $ans = -1;
    
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
        
        if(checkEmpty($content)){
            $upperBound = $mid - 1;
        }else{
            $lowerBound = $mid + 1;
            $ans = $lowerBound;
        }
    }
    
    return $ans-1;
}

