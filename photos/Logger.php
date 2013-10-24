<?php

//set timezone to ensure that it is working correctly
date_default_timezone_set('America/Chicago');

class Logger{
	private $className;
	
	const DEBUG = "DEBUG";
	const INFO = "INFO";
	const WARN = "WARN";
	const ERROR = "ERROR";

	public function __construct($name){
		$this->className = $name;
	}
	
	public function debug($message){
		$this->show_log(self::DEBUG, $message);
	}
	
	public function info($message){
		$this->show_log(self::INFO, $message);
	}
	
	public function warn($message){
		$this->show_log(self::WARN, $message);
	}		
	
	public function error($message){
		$this->show_log(self::ERROR, $message);
	}		

	//2013-03-06 00:30:09,502 [At[3]:com.circus.business.SnapshotJob] INFO  com.circus.business.SnapshotJob - Next Run time for SnapshotJob : Thu Mar 07 00:30:00 CST 2013
	private function show_log($level, $message){
		$now = new DateTime("now");
		//echo "{$now->format('Y-m-d H:i:s,u')} [TFLP.{$this->className}]  {$level} -  {$message}\n";
		error_log ("{$now->format('Y-m-d H:i:s,u')} [TFLP.{$this->className}]  {$level} -  {$message}\n");
	}	
}
?>