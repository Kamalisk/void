<?php

class VOID_DEBUG {
	static $log;
	
	static function write($text){
		self::$log = self::$log.print_r($text,1)."\n\n";
	}
	
	static function dump(){
		return self::$log;
	}
	
	static function mem_check($obj){
		$before = memory_get_usage();
		clone($obj);
		$after = memory_get_usage();
		if ($after - $before > 1000){
			self::write("memory change: ".($after - $before));
		}
	}
	
}


?>