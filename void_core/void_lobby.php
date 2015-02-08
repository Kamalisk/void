<?

class VOID_LOBBY {
	public $players;
	public $settings;
	
	public $races = [];
	public $leaders = [];
	public $empires = [];
	
	public $colors = [];
	
	function __construct(){
		global $player_colors;
		$this->colors = $player_colors;
	}
	
	public function add_player($user){
		$id = count($this->players)+1;
		$player = new VOID_PLAYER($user->id);
		$player->name = $user->name;
		// for now set a random race and such?		
		$this->players[$player->id] = $player;
		return $player;
	}
	public function select_race($player_id, $race, $empire, $leader){		
		$this->players[$player_id]->race = $race;
		$this->players[$player_id]->empire = $empire;
		$this->players[$player_id]->leader = $leader;
	}
	public function set_color($player_id, $color_id){
		foreach($this->colors as $key => &$color){
			if (isset($color['selected'])){
				if ($color['selected'] == $player_id){
						$color['selected'] = false;	
				}
				if($key == $color_id && $color['selected'] && $color['selected'] != $player_id){
					return false;
				}
			}
		}
		$this->colors[$color_id]['selected'] = $player_id;
	}
	public function reset(){
		global $player_colors;
		$this->players = [];
		$this->colors = $player_colors;
		
	}
}

?>