<?php
require_once("classes/extractRadioFrance.class.php");
//https://www.franceinter.fr/emissions/le-jeu-des-1000-eu/archives-24-08-2020-26-06-2021
//https://www.franceinter.fr/emissions/le-jeu-des-1000-eu/archives-29-08-2011-29-06-2012?p=11

/**
* This is meant to extract data from France Inter's website as it was in 2021 (May)
* It will need to be deeply modified if they change the structure of the website!
* The OOP was probably overkill...but who knows...
* 
*/


//$myExtract = new extractFranceInter("https://www.franceinter.fr/emissions/le-jeu-des-1000-euros");


$myExtract = new extractRadioFrance("https://www.radiofrance.fr/franceinter/podcasts/le-jeu-des-1000");
//echo $myExtract->getUrlWithoutGetParameter();
/*if($myExtract->checkEmpty())
{
    echo "vide";
}
else
{
    echo "pas vide";
}*/

echo $myExtract->getMaxPage();
//echo $myExtract->getHtml();