<?php

class pageLoader {
	public $urlSource;
    protected $urlWithoutGetParameter;

	protected $html;
	protected $myXPath;
	protected $myDom;
	protected $monthCorrespondence = array();
	
	

	
	public function __construct($url)
	{
		$this->urlSource = $url;
        $this->urlWithoutGetParameter = $url = strtok($url, '?');
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

	public final function getHtml() : string
	{
		return $this->html;
	}

	public final function getUrlWithoutGetParameter() : string
	{
		return $this->urlWithoutGetParameter;
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