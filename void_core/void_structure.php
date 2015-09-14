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
	
	function apply($system){
		$this->class->apply($system);
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
	
	function __construct($id=""){
		if ($id){
			$this->id = $id;
		}else {
			$this->id = void_unique_id();
		}
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
	function set_modifier($type, $category, $value, $max=0, $scope="system"){
		if ($this->work_required){
			$this->rush_cost = $this->work_required * 3; 
		}
		//possibly handle custom function handlers for unique effects?
		if ($max == 0){
			$max = 0;
		}
		//$this->modifiers[$type] = ["value"=>$value, "max"=>$max];
		$modifier = new VOID_MODIFIER($type, $category, $value, $max);
		$modifier->set_scope($scope);
		$this->modifiers[$modifier->get_modifier_id()] = $modifier;
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
	
	function apply($system){
		foreach($this->modifiers as $type => $modifier){
			$modifier->apply($system, "system");
		}
	}
	
	function apply_modifier($system, $modifier){
		$type = $modifier->type;
		$value = $modifier->value;
		$max = $modifier->max;
		$subtype = $modifier->category;
		
		if ($modifier->scope == "player"){
			$target = $system->owner;
		}else {
			$target = $system;
		}
		
		switch($type){
			case "research":{
				$resource = $target->research;
				break;
			}
			case "credits":{
				$resource = $target->credits;
				break;
			}
			case "production":{
				$resource = $target->credits;
				break;
			}
			case "food":{
				$resource = $target->credits;
				break;
			}
			case "influence":{
				$resource = $target->influence;
				break;
			}
			case "morale":{
				$resource = $target->morale;
				break;
			}
			
		}
		
		if ($resource){
			switch($subtype){
				case "per_turn":{
					$resource->add_per_turn($value, "structures");
					break;
				}
				case "percent":{
					$resource->add_percent($value, "structures");
					break;
				}
				case "per_population":{
					$value = $value * $system->population;
					if ($max && $value > $max){
						$value = $max;
					}
					$resource->add_per_turn($system->population * $value, "structures");
					break;
				}
			}
		}
		
	}
	
}

?>