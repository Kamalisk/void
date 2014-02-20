<?php

class VOID_PLAYER_VIEW {
	public $name;
	public $id;
	public $color;
	public $done;
	
	public $sector_count;
	public $tech_count;
	
	public $met;
	
	function __construct($player, $player_id){
		$this->name = $player->name;
		$this->id = $player->id;
		$this->color = $player->color;
		$this->done = $player->done;
		
		$this->sector_count = $player->sector_count;
		$this->tech_count = $player->tech_count;
		
		if ($player->has_met($player_id)){
			$this->met = true;
		}else {
			$this->met = false;
		}
	}
}

class VOID_PLAYER {
	public $name;
	public $id;
	public $home;
	public $resources;
	public $color;
	
	public $research_pool;
	public $research_per_turn;
	public $credits_pool;
	public $credits_per_turn;
	
	public $morale;
	
	public $tech;
	public $available_tech;
	public $current_tech;
	
	public $available_ship_classes;
	public $available_structure_classes;
	public $available_upgrade_classes;
	
	public $built_structures;
	
	public $done;
	
	public $met_players;
	
	public $sector_count;
	public $tech_count;
	
	public $sources;
	
	public $player;
	
	function __construct($id){
		$this->id = $id;
		$this->research_pool = 0;
		$this->credits_pool = 0;
		$this->morale = 0;
		$this->current_tech = false;
		$this->met_players = [];
		$this->sector_count = 0;
		$this->tech_count = 0;
		$this->sources = [];
		$this->player = true;
		VOID_LOG::init($id);
	}
	
	public function dump($player_id){
		$view = new VOID_PLAYER_VIEW($this, $player_id);
		return $view;
	}
	
	public function apply_resources(){
		$this->credits_pool += $this->credits_per_turn;
		$this->research_pool += $this->research_per_turn;
	}
	
	public function update_resource($type, $value, $source=""){
		if ($source ==""){
			$source = "general";
		}
		if (!isset($this->sources[$type][$source] )){
			$this->sources[$type][$source] = 0;
		}
		if ($type == "credits"){			
			$this->credits_per_turn = $this->credits_per_turn + $value;				
		}else if ($type == "research"){
			$this->research_per_turn = $this->research_per_turn + $value;
		}else if ($type == "morale"){
			$this->morale = $this->morale + $value;
		}else {
			return false;
		}
		// update list of sources 
		$this->sources[$type][$source] += $value;
	}
	
	public function reset_per_turn(){
		$this->credits_per_turn = 0;
		$this->research_per_turn = 0;
		$this->morale = 0;
	}
	
	public function set_color($color){
		$this->color = $color;
	}
	
	public function set_tech($tech_tree){
		$techs = $tech_tree->get_starting_tech();
		foreach($techs as &$tech){
			$this->tech[$tech->id] = new VOID_TECH_ITEM($tech);
			$this->add_new_ship_classes($tech);
			$this->add_new_structure_classes($tech);
			$this->add_new_upgrade_classes($tech);
		}
		$this->update_available_tech($tech_tree);
	}
	
	public function update_research($work, $tech_tree){
		if ($this->current_tech){
			
			$this->current_tech->progress = $this->current_tech->progress - $work;
			if ($this->current_tech->progress <= 0){
				VOID_LOG::write($this->id, "Research has completed on ".$this->current_tech->class->name);
				$this->tech[$this->current_tech->class->id] = $this->current_tech;
				$this->add_new_ship_classes($this->current_tech->class);
				$this->add_new_structure_classes($this->current_tech->class);
				$this->add_new_upgrade_classes($this->current_tech->class);
				unset($this->available_tech[$this->current_tech->class->id]);
				$this->current_tech = false;
				$this->update_available_tech($tech_tree);
			}
		}
		
	}
	
	
	public function add_new_ship_classes($tech){
		foreach($tech->ship_classes as &$class){
			$this->available_ship_classes[$class->id] = $class;
		}
	}
	public function add_new_structure_classes($tech){
		foreach($tech->structure_classes as &$class){
			$this->available_structure_classes[$class->id] = $class;
		}
	}
	public function add_new_upgrade_classes($tech){
		foreach($tech->upgrade_classes as &$class){
			$this->available_upgrade_classes[$class->id] = $class;
		}
	}
	
	public function update_available_structure_class($id, $add=true){
		if ($add == false){
			$this->built_structures[$id] = false;
		}else {
			$this->built_structures[$id] = true;
		}		
	}	

	public function is_structure_available($id){
		if (isset($this->built_structures[$id]) && $this->built_structures[$id]){
			return false;
		}
		return true;
	}
	
	public function update_available_tech($tech_tree){
		foreach($tech_tree->items as &$tech){
			if (isset($this->tech[$tech->id])){
				continue;
			}
			$count = 0;
			foreach($tech->requirements as $req){
				if (isset($this->tech[$req])){
					$count++;
				}
			}
			if ($count >= $tech->get_req_count()){
				$this->available_tech[$tech->id] = new VOID_TECH_ITEM($tech);
				if (!$this->current_tech){
					$this->current_tech = $this->available_tech[$tech->id];
				}
			}
		}
	}
	
	public function update_morale($morale){		
		$this->morale = $morale;
	}
	public function apply_morale($morale, $source=""){
		$this->update_resource("morale", $morale, $source);		
	}
	
	public function add_met_player($player_id){
		if (isset($this->met_players[$player_id])){
			return false;
		}
		$this->met_players[$player_id] = $player_id;
		return true;
	}
	
	public function has_met($player_id){
		if (isset($this->met_players[$player_id])){
			return true;
		}
		return false;
	}
	
}


?>