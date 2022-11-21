<?php

class extractFranceInter extends pageLoader {
	
	private $archiveUrlByYear = array();

    /**
	* Automatically start downloading when you construc the object
	*/
	public function __construct($url)
	{
		parent::__construct($url);
		//This loop is based on page number, so $i indicate which page on France Inter's website we are accessing and subsequently downloading
		//for($i = 1; $i <=  $this->getMaxPage();$i++)
		{
			//$this->extractor($i);
		}
	}
	
	/**
	* returns the maximum page ID...from website's paging system
	*/
	public function getMaxPage() : int
	{
		$className="pager-item last";
		$lastPage= $this->myXPath->query("//*[contains(@class, '$className')]");
				
		return $maxPages=substr($lastPage[0]->childNodes[0]->getAttribute('href'), -3);
	}


	
	
	/**
	* 
	* Param: pageId: int, id number of a page in paging system 
	* return: void
	*/
	public function extractor($pageId = NULL)
	{
		if(!is_null($pageId))
		{
			$pageToExtract = new pageLoader($this->urlSource . "?p=". $pageId);
			$className="replay-button playable";
			$pageToExtract= $pageToExtract->myXPath->query("//*[contains(@class, '$className')]");
			
			//For each detect node (button with the right attribute) we try to download
			foreach($pageToExtract as $node)
			{
				//We enforce this format for the fileName -> yyyy.mm.dd.mp3
				$fileName= NULL;
				
				//currentYear must be of the form yyyy
				$currentYear=NULL;
				
				//Detecting new format for audio files naming
				if(preg_match("/\d{2}\.\d{2}\.\d{4}/", $node->getAttribute("data-url"), $fileName))
				{
	
					$fileName = $fileName[0];
					$fileName = explode(".", $fileName);
					$newYear = intval($fileName[2]);
					if($newYear != $currentYear)
					{
						$currentYear = $newYear;
					}
					
					$fileName = $fileName[2] . "." . $fileName[1] . "." . $fileName[0] . ".mp3";
				}
				
				//Detecting old format for audio files naming
				else
				{
					//getting the last part of the path in order to extract correct fileName
					$fileName = basename($node->getAttribute("data-diffusion-path"));
					$fileName = str_replace("le-jeu-des-1000-eu-", "", $fileName);
										
					if($fileName != "")
					{
						$fileName = explode("-", $fileName);
						if(is_array($fileName) && count($fileName)==3)
						{
							$newYear = intval($fileName[2]);
							if($newYear != $currentYear)
							{
								$currentYear = $newYear;
							}
							$fileName = $fileName[2] . "." . $this->monthCorrespondence[$fileName[1]] . "." . $fileName[0] . ".mp3";
						}
					}
				}
				
				
				//Getting ready for download. Performing some last verification
				//Year must not be null -> we create directories based on the year, so it better be valid
				//fileName must not be null -> we create the file from that variable
				//strlen($fileName)> 6 -> just checking that the filename is of the right lenght
				//fileName has to end with "mp3"...which also means that we do not handle other file formats for now, as it is the only format provided by France Inter
				if(!is_null($currentYear) && !is_null($fileName) && strlen($fileName)>6 && substr($fileName, -3) == "mp3")
				{
					if(!file_exists($currentYear))
					{
						mkdir($currentYear);
					}
					$fileName = $currentYear . "/". $fileName;
					echo $this->download($fileName, $node->getAttribute("data-url"));
				}
			}
		}
	}
	

	
	
	/**
	* Ended up being useless
	*/
	public function extractArchivesUrlByYear()
	{
		foreach ($this->myXPath->query('/html/body/main/div[3]/div[1]/div/ul[1]') as $node)
		{
			$links = $node->getElementsByTagName('a');
			$counter = 0;
			$retArray = array();
			foreach ($links as $link)
			{
				$retArray[$counter] = $this->getBaseUrl() . $link->getAttribute('href');
				$counter++;
			}
		}
		return $retArray;
	}


	public function test1() : void
	{
		$className="Card Audio list";
		//$className="link";
		$ex = $this->myXPath->query("//*[contains(@class, '$className')]");
		//$ex = $this->myXPath->query("//*[contains(@data-testid, '$className')]");
		var_dump($ex);
		foreach($ex as $node)
		{
			var_dump($node);
			$res = new DomXPath($node);
			exit(0);
		}
	}
}