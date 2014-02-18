<?php

class VOID_STRUCTURE {
	public $class;
	public $planet_id;
	
	function __construct($class){
		$this->planet_id = 0;
		$this->class = $class;
	}
	
	function dump($player_id){
		return new VOID_STRUCTURE_VIEW($this, $player_id);
	}
	
}

class VOID_STRUCTURE_VIEW {
	public $class_id;	
	public $name;
	
	function __construct($structure, $player_id){
		$this->class_id = $structure->class->id;
		$this->name = $structure->class->name;
	}		
}

class VOID_STRUCTURE_CLASS {
	public $id;
	public $name;
	
	public $food_per_turn = 0;
	public $research_per_turn = 0;
	public $credits_per_turn = 0;
	public $production_per_turn = 0;
	
	public $upkeep = 0;
	
	public $empire_unique;	
	public $world_unique;
	
	public $modifiers;
	
	public $work_required;
	
	public $rush_cost;
	
	function __construct(){
		$this->work_required = 10;
		$this->empire_unique = false;
		$this->world_unique = false;
		$this->rush_cost = 20;
		$this->modifiers = [];
	}
	
	function get_modifier($type){
		if (isset($this->modifiers[$type])){
			return $this->modifiers[$type];
		}
	}
	function set_modifier($type, $value){
		//possibly handle custom function handlers for unique effects?
		$this->modifiers[$type] = $value;
	}
	
	function set_unique($type="empire"){
		if ($type == "world"){
			$this->world_unique = true;
		}else {
			$this->empire_unique = true;
		}
	}
	
	function is_unique($type){
		if ($type == "empire"){
			return $this->empire_unique;
		}else if ($type == "world"){
			return $this->world_unique;
		}
	}
	
}

?>