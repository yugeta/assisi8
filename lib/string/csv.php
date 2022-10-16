<?php
namespace lib\string;

class csv{
	function getCsv2Hash($csvFile){
		if(!is_file($csvFile)){return null;}
		$records = array();
		$buf = file_get_contents($csvFile);
		$lines = str_getcsv($buf, "\r\n");
		foreach ($lines as $line) {
			$cells = str_getcsv($line);
			for($i=0; $i<count($cells); $i++){
				$cells[$i] = str_replace('&' , "&amp;" , $cells[$i]);
				$cells[$i] = str_replace('<' , "&lt;" , $cells[$i]);
				$cells[$i] = str_replace('>' , "&gt;" , $cells[$i]);
				$cells[$i] = str_replace(',' , "&sbquo;" , $cells[$i]); //&#44;
				$cells[$i] = str_replace('\r' , "" , $cells[$i]);
				$cells[$i] = str_replace(PHP_EOL , "<br>" , $cells[$i]);
				$cells[$i] = preg_replace('/^<br>(.*?)/' , "" , $cells[$i]);
			}
			$records[] = implode("," , $cells);
		}
		return $records;
	}
	function getCsv2Hash_sjis($csvFile){
		if(!is_file($csvFile)){return null;}
		$records = array();
		$buf = file_get_contents($csvFile);
		$buf = mb_convert_encoding($buf , "SJIS" , "UTF-8");
		$lines = str_getcsv($buf, "\r\n");
		foreach ($lines as $line) {
			$cells = str_getcsv($line);
			for($i=0; $i<count($cells); $i++){
				$cells[$i] = str_replace('&' , "&amp;" , $cells[$i]);
				$cells[$i] = str_replace('<' , "&lt;" , $cells[$i]);
				$cells[$i] = str_replace('>' , "&gt;" , $cells[$i]);
				$cells[$i] = str_replace(',' , "&sbquo;" , $cells[$i]); //&#44;
				$cells[$i] = str_replace('\r' , "" , $cells[$i]);
				$cells[$i] = str_replace(PHP_EOL , "<br>" , $cells[$i]);
				$cells[$i] = preg_replace('/^<br>(.*?)/' , "" , $cells[$i]);
			}
			$records[] = implode("," , $cells);
		}
		return $records;
	}

	function convData($buf){
		$lines = str_getcsv($buf, "\r\n");
		$newData = array();
		foreach ($lines as $line) {
			$cells = str_getcsv($line);
			for($i=0; $i<count($cells); $i++){
				$cells[$i] = str_replace('&'    , "&amp;" , $cells[$i]);
				$cells[$i] = str_replace('<'    , "&lt;" , $cells[$i]);
				$cells[$i] = str_replace('>'    , "&gt;" , $cells[$i]);
				$cells[$i] = str_replace(','    , "&sbquo;" , $cells[$i]); //&#44;
				$cells[$i] = str_replace('\r\n' , '\n' , $cells[$i]);
				$cells[$i] = str_replace('\r'   , "" , $cells[$i]);
				$cells[$i] = str_replace('\n'   , "<br>" , $cells[$i]);
				$cells[$i] = preg_replace('/^<br>(.*?)/' , "" , $cells[$i]);
			}
			array_push($newData , $cells);
		}
		return $newData;
	}

	function loadFile($file){
		if(!is_file($file)){return null;}
		$buf = file_get_contents($file);
		$enctype =  mb_detect_encoding($buf);
		switch($enctype){
			case "UTF-8":
				break;

			case "SJIS":
				$buf = mb_convert_encoding($buf , "UTF-8" , "SJIS");
				break;
		}
		$buf = str_replace("\r\n","\n",$buf);
		$buf = str_replace("\r"  ,"\n",$buf);
		$lines = str_getcsv($buf, "\n");
		$newData = array();
		foreach ($lines as $line) {
			array_push($newData , str_getcsv($line));
		}
		return $newData;
	}

	public static function download(){

	}

}
