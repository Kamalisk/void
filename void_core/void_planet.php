<?php

class VOID_PLANET_VIEW {
	public $name;
	public $class_id;
	public $population;
	public $terraformed;
	
	public $id;
	
	public $terraformable;
	
	function __construct($planet, $player){
		$this->name = $planet->name;
		$this->class_id = $planet->class['id'];
		$this->terraformed = $planet->terraformed;
		$this->population = $planet->population;	
		$this->id = $planet->id;				
		$this->terraformable = false;
		if ($player->has_property_index("terraformable", $this->class_id)){
			$this->terraformable = true;
		}
	}
	
	
	
}

class VOID_PLANET {
	public $name = "";
	public $class = "";
	
	public $population;
	public $max_population;
	public $terraformed;
	
	public $development; 
	
	public $id;
	
	function __construct(){
		$this->id = void_unique_id();
		$this->population = 0;
		$this->terraformed = 0;
		$this->development = 0;
	}
	
	public function terraform(){
		$this->terraformed = 1;
	}
	
	public function colonise(){
		$this->terraform();
		$this->development = 1;
	}
	
	
	public function update(){
		// update the development value
		// the development value affects the output of the planet when first terraformed
		// 
		if ($this->development < 1){
			$this->development += $this->class['develop_per_turn'];
		}else {
			$this->development = 1;
		}
	}
	
	public function dump($player){
		$view = new VOID_PLANET_VIEW($this, $player);
		return $view;
	}
	
	public function get_food_output(){
		return $this->class['output']['food'];
	}
	public function get_research_output(){
		return $this->class['output']['research'];
	}
	public function get_production_output(){
		return $this->class['output']['production'];
	}
	public function get_credits_output(){
		return $this->class['output']['credits'];
	}
	
}

class VOID_PLANET_CLASS {
	public $name = "";
	public $max_population = "";
	public $resources;
	
	function __construct(){
		$this->resources = new VOID_RESOURCES();
	}
	
}


?>