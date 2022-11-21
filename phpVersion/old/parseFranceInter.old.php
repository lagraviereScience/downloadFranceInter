<?php
//https://www.franceinter.fr/emissions/le-jeu-des-1000-eu/archives-24-08-2020-26-06-2021
//https://www.franceinter.fr/emissions/le-jeu-des-1000-eu/archives-29-08-2011-29-06-2012?p=11

/**
* This is meant to extract data from France Inter's website as it was in 2021 (May)
* It will need to be deeply modified if they change the structure of the website!
* The OOP was probably overkill...but who knows...
* 
*/


class pageLoader {
	public $urlSource;
	protected $html;
	protected $myXPath;
	protected $myDom;
	protected $monthCorrespondence = array();
	
	
	public final function getHtml() : string
	{
		return $this->html;
	}
	
	public function __construct($url)
	{
		$this->urlSource = $url;
		$this->myDom = new DomDocument();
		$this->readUrlSource();
		$this->prepareDom();
		$this->prepareXPath();
		
		
		/*Month in French conversion...because of accents, weird locales and the fact the REMOTE website can be of a different language 
		than the system using the script -> we enforce months in French: all lower case, no accents.*/
		$this->monthCorrespondence["janvier"] = "01";
		$this->monthCorrespondence["fevrier"] = "02";
		$this->monthCorrespondence["mars"] = "03";
		$this->monthCorrespondence["avril"] = "04";
		$this->monthCorrespondence["mai"] = "05";
		$this->monthCorrespondence["juin"] = "06";
		$this->monthCorrespondence["juillet"] = "07";
		$this->monthCorrespondence["aout"] = "08";
		$this->monthCorrespondence["septembre"] = "09";
		$this->monthCorrespondence["octobre"] = "10";
		$this->monthCorrespondence["novembre"] = "11";
		$this->monthCorrespondence["decembre"] = "12";
	}
	
	
	/**
	* Extract base url return the form http(s)://host/
	*/
	public function getBaseUrl($url = NULL) : string
	{
		if(is_null($url))
		{
			$url = $this->urlSource;
		}
		$url = parse_url($url);
		return $url["scheme"] . "://". $url["host"] . "/";
	}
	
	/*
	* Read the html content from url
	*/
	public function readUrlSource()
	{
		$this->html = file_get_contents($this->urlSource);
	}
	
	/*
	* Cleans html and loads it in DOM object
	*/
	public function prepareDom()
	{
		$this->html = tidy_repair_string($this->html);
		@$this->myDom->loadHtml($this->html);
	}
	
	public function prepareXPath()
	{
		$this->myXPath = new DomXPath($this->myDom);
	}
	
	/**
	* Download from a $source (URL) to a $destination
	* params: destination -> expects path or filename
	* params: source -> expected URL in our context but could anything
	* return: string, message -> displays what happened for the 
	*/
	public final function download($destination, $source) : string
	{
		$message="Wrong parameters for downloading file";
		if(!is_null($destination) && !is_null($source))
		{
			//performing the download with open()...just for fun
			if(file_put_contents($destination, fopen($source, 'r')))
			{
				$message =  $destination . " - File downloaded successfully \n";
				//echo $message;
			}
			else
			{
				$message = "File downloading failed. \n";
				//echo $message;
			}
		}
		return $message;
	}
}


class extractFranceInter extends pageLoader {
	
	private $archiveUrlByYear = array();
	
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
}

//$myExtract = new extractFranceInter("https://www.franceinter.fr/emissions/le-jeu-des-1000-euros");


$myExtract = new extractFranceInter("https://www.radiofrance.fr/franceinter/podcasts/le-jeu-des-1000");
echo $myExtract->getHtml();