<?


class VOID_SYSTEM_VIEW {
	public $owner;
	public $planets;
	public $yours;
	
	public $influence_level;
	public $influence_per_turn;
	public $influence_pool;
	public $influence_growth_threshold;
	public $influence_growth_turns;
	public $influence_size;
	
	public $food_pool;
	public $food_per_turn;
	public $food_growth_threshold;
	public $growth_turns;
	
	public $population;
	
	public $research_per_turn;
	public $credits_per_turn;
	
	public $production_per_turn;
	
	public $id;
	
	public $build_queue;
	public $structures;
	
	public $available_structures;
	
	public $influenced_sectors;
	
	function __construct($system, $player_id){
		$this->id = $system->id;
		$this->yours = false;
		if ($system->owner){
			$this->owner = $system->owner->id;
			$this->influence_pool = $system->influence_pool;
			$this->influence_per_turn = $system->influence_per_turn;
			$this->influence_level = $system->influence_level;
			$this->food_per_turn = $system->food_per_turn;
			$this->production_per_turn = $system->production_per_turn;
			
			if ($this->owner == $player_id){
				$this->food_pool = $system->food_pool;
				$this->food_growth_threshold = $system->food_growth_threshold;
				$this->growth_turns = $this->food_per_turn ? ceil(($this->food_growth_threshold - $this->food_pool) / $this->food_per_turn) : " - ";
				$this->influence_growth_threshold = $system->influence_growth_threshold;
				$this->influence_growth_turns = $this->influence_per_turn ? ceil(($this->influence_growth_threshold - $this->influence_pool) / $this->influence_per_turn) : " - ";
				$this->influence_size = $system->influence_size;
				
				$this->yours = true;
				$this->build_queue = $system->build_queue->dump($system->production_per_turn);
				
				$this->available_structures = [];
				// generate a list of ids of the structures which can be built here
				foreach($system->owner->available_structure_classes as $structure){
					$available = true;
					if ($system->build_queue->exists($structure->id, "structure")){
						$available = false;
					}
					if ( !$system->owner->is_structure_available($structure->id) ){
						$available = false;
					} 
					if ($available){
						$this->available_structures[] = $structure->id;
					}
				}
			}
			if ($system->influenced_sectors){
				foreach($system->influenced_sectors as $sector){
					$this->influenced_sectors[] = $sector->id;
				}
			}
			
			
		}
		if ($system->planets){
			foreach($system->planets as &$planet){
				$this->planets[] = $planet->dump($player_id);
			}
		}
		if ($system->structures){
			foreach($system->structures as &$structure){
				$this->structures[] = $structure->dump($player_id);
			}
		}
		$this->population = $system->population;
		$this->credits_per_turn = $system->get_credits_income();
		$this->research_per_turn = $system->get_research_income();
		
		
	}
	
}


class VOID_SYSTEM {
	public $food_pool;
	public $food_per_turn;
	public $food_growth_threshold;
	
	public $production_per_turn;
	
	public $x;
	public $z;
	public $owner;
	public $planets;
	
	public $influence_level;
	public $influence_per_turn;
	public $influence_pool;
	public $influence_growth_threshold;
	public $influence_size;
	
	public $population;
	
	public $orders;
	
	public $build_queue;
	
	public $id;
	public $name;
	
	public $docked_fleet;
	public $structures;
	
	public $planet_index;
	
	public $influenced_sectors;
	
	public function __construct($x, $z){
		$this->x = $x;
		$this->z = $z;
		$this->id = "x".$x."z".$z;
		$this->influence_level = 0;
		$this->influence_per_turn = 0;
		$this->influence_pool = 0;
		$this->population = 0;
		$this->food_pool = 0;
		$this->food_growth_threshold = 5;
		$this->influence_growth_threshold = 5;
		$this->production_per_turn = 0;
		
		$this->planet_index = [];
		
		$this->build_queue = new VOID_QUEUE();
		$this->structures = [];
	}
	
	public function add_planet($planet){
		$this->planets[] = $planet;
		$this->planet_index[$planet->id] = $planet;
	}
	
	public function get_planet($pid){
		if (isset($this->planet_index[$pid])){
			return $this->planet_index[$pid];
		}
		return false;
	}
	
	public function add_structure($structure){
		// should check to see if it already exists etc..
		if ($structure->class->is_unique("empire")){
			$this->owner->update_available_structure_class($structure->class->id, true);
		}
		$this->structures[$structure->class->id] = $structure;
	}
	
	public function add_influenced_sector($sector){
		$this->influenced_sectors[$sector->id] = $sector;
	}
	
	public function update(){		
		$this->food_per_turn = $this->get_food_income();
		$this->production_per_turn = $this->get_production_income();
		$this->influence_growth_threshold = ($this->influence_level+1) * 3;	
		$this->influence_per_turn = $this->get_influence_income();
		$this->food_growth_threshold = pow(2, $this->population);
		
		if ($this->influence_level >= 10){
			$this->influence_size = 4;
		}else if($this->influence_level >= 7){
			$this->influence_size = 3;
		}else if($this->influence_level >= 3){
			$this->influence_size = 2;
		}else {
			$this->influence_size = 1;
		}
		
	}
	
	public function apply(){
		$this->influence_pool += $this->influence_per_turn;
		if ($this->influence_pool >= $this->influence_growth_threshold ){
			$this->influence_pool = 0;
			$this->influence_level++;
			$this->influence_growth_threshold = ($this->influence_level+1) * 3;
		}
		$this->food_pool += $this->food_per_turn;
		if ($this->food_pool >= $this->food_growth_threshold ){
			$this->population++;			
			$this->food_pool = 0;
			VOID_LOG::write($this->owner->id, "System at (".$this->x.",".$this->z.") has grown.");
			$this->update();
		}		
	}
	
	
	public function update_planets(){
		foreach($this->planets as &$planet){
			if ($planet->terraformed){
				$this->update();
			}
		}
	}
	
	public function get_food_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as &$planet){
			if ($planet->terraformed){
				$output += $planet->get_food_output() * $this->population * $planet->development;
			}			
		}
		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("food");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		if ($this->owner->morale < 0){
			$output = $output / 10;
		}		
		return ceil($output);
	}
	
	public function get_influence_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = $this->population;		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("influence");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		
		return $output;
	}
	
	public function get_credits_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as &$planet){
			if ($planet->terraformed){
				$output += $planet->get_credits_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("credits");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		
		return $output;
	}
	
	public function get_research_income(){
		// returns the research income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as &$planet){
			if ($planet->terraformed){
				$output += $planet->get_research_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("research");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		
		return $output;
	}
	public function get_production_income(){
		// returns the production income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as &$planet){
			if ($planet->terraformed){
				$output += $planet->get_production_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("production");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		
		return $output;
	}
	
	public function get_morale(){
		$morale = 0;
		$morale = $morale - $this->population;
		$morale = $morale - 3;
		// apply any buildings or empire modifiers here!
		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("morale");
				if ($modifier){
					$morale += $modifier;
				}
			}
		}
		
		return $morale;
	}
	
	
	public function dump($player_id){
		$view = new VOID_SYSTEM_VIEW($this, $player_id);
		return $view;
	}
	
	public function colonise($owner, $core, $pid=""){
		if ($pid){
			$planet = $this->get_planet($pid);
			if (!$planet){
				return false;
			}
		}else {
			$key = array_rand($this->planets);
			$planet =& $this->planets[$key];
		}
				
		if (!$this->owner){
			$this->population = 1;
			$this->influence_level = 1;
		}
						
		$planet->colonise();
		$this->owner = $owner;				
		
		$this->update();
		$this->docked_fleet = new VOID_FLEET();
		$this->docked_fleet->capacity = 10;
		$core->fleets[$this->docked_fleet->id] = $this->docked_fleet;
		VOID_LOG::write($owner->id, "System was colonised");
	}
	
	public function add_order($type, $target){
		$order = new VOID_SYSTEM_QUEUE_ITEM();		
		$order->data = $target;		
		$order->type = $type;
		$order->progress = $target->work_required;
		if ($type == "structure" && $target->is_unique("empire") ){
			$this->owner->update_available_structure_class($target->id, true);
		}
		$this->build_queue->add($order);
	}
	public function delete_order(){
		
	}
	public function switch_order(){
		
	}
	
	public function process_orders($sector, $core){
		global $ship_classes;
		// if a construction order get the systems production and see if the item is done yet
		$this->production_per_turn = $this->get_production_income();
		
		// apply the progress, if an item is returned, the progress is done, otherwise it is not
		$item = $this->build_queue->progress($this->production_per_turn);
		if ($item){
			// create the created item. 
			if ($item->type == "ship"){
				$ship_class = $item->data;
				$ship = new VOID_SHIP($ship_class, $this->owner->id);
				if ($fleet = $sector->get_primary_fleet($this->owner->id) ){
					//print_r($fleet);
					if ($fleet->add_ship($ship)){
						//echo "did we get here?";
					}else {
						if ($this->docked_fleet){
							$this->docked_fleet->add_ship($ship);
						}
					}
				}else {
					
					$fleet = new VOID_FLEET();
					$core->fleets[$fleet->id] = $fleet;
					$fleet->add_ship($ship);
					$sector->add_fleet($fleet);
				}
				VOID_LOG::write($this->owner->id, "A ship (".$ship->class->name.") was built at (".$this->x.",".$this->z.")");
			}else if ($item->type == "structure"){
				$structure_class = $item->data;
				$structure = new VOID_STRUCTURE($structure_class, $this->owner->id);
				$this->add_structure($structure);
				VOID_LOG::write($this->owner->id, "A structure (".$structure->class->name.") was built at (".$this->x.",".$this->z.")");
			}
		}
		
		// subtract production from work left to do
		
		// if work is finished. dispatch built item
		
		// if production left over, apply it to the next item
		
		// only 1 thing can be built per turn regardless though
	}
	
}



?>