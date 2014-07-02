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
	function set_modifier($type, $value, $max=0){
		//possibly handle custom function handlers for unique effects?
		if ($max == 0){
			$max = 0;
		}
		$this->modifiers[$type] = ["value"=>$value, "max"=>$max];
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
			$this->apply_modifier($system, $type, $modifier['value'], $modifier['max']);
		}
	}
	
	function apply_modifier($system, $type, $value, $max=0){
		switch($type){
			case "research":
			case "research_per_turn":
			case "research_per_population":{
				$resource = $system->research;
				break;
			}
			case "credits":
			case "credits_per_turn":
			case "credits_per_population":{
				$resource = $system->credits;
				break;
			}
			case "production":
			case "production_per_turn":
			case "production_per_population":{
				$resource = $system->credits;
				break;
			}
			case "food":
			case "food_per_turn":
			case "food_per_population":{
				$resource = $system->credits;
				break;
			}
			
		}
		
		switch($type){
			case "research":
			case "food":
			case "production":
			case "credits":{
				$resource->per_turn += $value;
				break;
			}
			case "research_percent":
			case "food_percent":
			case "production_percent":
			case "credits_percent":{
				$resource->percent += $value;
				break;
			}
			case "research_per_population":
			case "food_per_population":
			case "production_per_population":
			case "credits_per_population":{
				$value = $value * $system->population;
				if ($max && $value > $max){
					$value = $max;
				}
				$resource->per_turn += $system->population * $value;
				break;
			}
		}
		
		switch($type){
			case "influence":{
				$system->influence_per_turn += $value;
				break;
			}
			case "influence_per_population":{
				$system->influence_per_turn += $system->population * $value;
				break;
			}
			case "influence_percent":{
				$system->influence_modifier += $value;
				break;
			}
			case "morale":{
				$system->owner->apply_morale($value, "structures");
				break;
			}
			case "morale_per_population":{
				$value = $value * $system->population;
				if ($max && $value > $max){
					$value = $max;
				}
				$system->owner->apply_morale($value, "structures");
				break;
			}
		}
	}
	
}

?>