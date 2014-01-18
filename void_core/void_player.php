<?php


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
	
	public $done;
	
	function __construct($id){
		$this->id = $id;
		$this->research_pool = 0;
		$this->credits_pool = 0;
		$this->morale = 0;
		$this->current_tech = false;
		VOID_LOG::init($id);
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
		}
		$this->update_available_tech($tech_tree);
	}
	
	public function update_research($work, $tech_tree){
		if ($this->current_tech){
			echo "tech has arrived \n";
			$this->current_tech->progress = $this->current_tech->progress - $work;
			if ($this->current_tech->progress <= 0){
				VOID_LOG::write($this->id, "Research has completed on ".$this->current_tech->class->name);
				$this->tech[$this->current_tech->class->id] = $this->current_tech;
				$this->add_new_ship_classes($this->current_tech->class);
				$this->add_new_structure_classes($this->current_tech->class);
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
	public function apply_morale($morale){
		$this->morale = $this->morale + $morale;
	}
	
	
}


?>