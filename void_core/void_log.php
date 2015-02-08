<?

class VOID_LOG {
	static $game_id;
	static $turn;
	static $game_path;
	
	static function init($player_id){
		file_put_contents(self::$game_path."player".$player_id.".log", "");
	}
	
	static function write($player_id, $text){
		if ($player_id){
			file_put_contents(self::$game_path."player".$player_id.".log", $text."\n", FILE_APPEND);
		}
	}
	
	static function get($player_id){
		if ($player_id){
			$contents = file_get_contents(self::$game_path."player".$player_id.".log");
			return VOID_LOG::parse($contents);
		}
	}
	
	static function parse($contents){
		$entries = explode("\n", $contents);
		array_pop($entries);
		return $entries;
	}
}

?>