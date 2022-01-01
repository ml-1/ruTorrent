<?php
require_once( 'cache.php' );

class WhichCache
{
	public $hash = "which.dat";
	private $filePath = array();
	
	public function getFilePath($exe)
	{
		if(!$this->isFilePathSet($exe))
		{
			if($this->setFilePath($exe))
			{
				$this->store();
				return($this->filePath[$exe]);
			}
			return false;
		}
		return($this->filePath[$exe]);
	}
	
	private function store()
	{
		$cache = new rCache();
		$cache->set($this);
	}
	
	private function isFilePathSet($exe)
	{
		return(isset($this->filePath[$exe]) && !empty($this->filePath[$exe]));
	}
	
	private function setFilePath($exe)
	{
		$this->filePath[$exe] = exec('command -v '.$exe);
		return(is_executable($this->filePath[$exe]));
	}
	
	private function pruneCache()
	{
		foreach ($this->filePath as $key => $value)
		{
			if(!is_executable($value))
			{
				unset($this->filePath[$key]);
			}
		}
	}
}

class WhichInstance
{
	public static function load($diagnostic)
	{
		$cache = new rCache();
		$which = new WhichCache();
		$cache->get($which);
		
		if($diagnostic)
			$which->pruneCache();
		
		return($which);
	}
}

function findEXE( $exe )
{
	global $pathToExternals;
	if(isset($pathToExternals[$exe]) && !empty($pathToExternals[$exe]))
		return(is_executable($pathToExternals[$exe]) ? $pathToExternals[$exe] : false);
	
	global $whichCache;
	return($whichCache->getFilePath($exe));
}

$whichCache = WhichInstance::load($do_diagnostic);
