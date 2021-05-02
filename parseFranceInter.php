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
		//$this->getBaseUrl();
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
	
}


class extractFranceInter extends pageLoader {
	
	private $archiveUrlByYear = array();
	
	/**
	* returns the maximum page ID...from website's paging system
	*/
	public function getMaxPage() : int
	{
		///html/body/main/section/div/div/div[4]/div[2]/ul[1]/li[13]/a
		///html/body/main/section/div/div/div[4]/div[2]/ul[1]/li[13]
		//$lastPage = $this->myXPath->query("/html/body/main/section/div/div/div[4]/div[2]/ul[1]/li[13]");
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
			$currentYear=0000;
			foreach($pageToExtract as $node)
			{
				if(preg_match("/\d{2}.\d{2}.\d{4}/", $node->getAttribute("data-url"), $fileName))
				{
					
					$fileName = $fileName[0];
					$fileName = explode(".", $fileName);
					$newYear = intval($fileName[2]);
					if($newYear != $currentYear)
					{
						$currentYear = $newYear;
						mkdir($currentYear);
					}
					
					$fileName = $fileName[2] . "." . $fileName[1] . "." . $fileName[0];
					$fileName = $currentYear . "/". $fileName . ".mp3";
					
					
					if(file_put_contents($fileName, fopen($node->getAttribute("data-url"), 'r')))
					//if(1)
					{
						echo $fileName . " - File downloaded successfully \n";
					}
					else
					{
						echo "File downloading failed. \n";
					}
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
		for($i = 1; $i <=  $this->getMaxPage();$i++)
		{
			$this->extractor($i);
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

$myExtract = new extractFranceInter("https://www.franceinter.fr/emissions/le-jeu-des-1000-euros");



