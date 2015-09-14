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
	public $name;
	
	public $build_queue;
	public $structures;
	
	public $available_structures;
	
	public $influenced_sectors;
	
	public $health;
	public $max_health;
	
	public $attack;
	public $defense;
	public $damage;
	
	function __construct($system, $player){
		$player_id = $player->id;
		$this->id = $system->id;
		$this->yours = false;
		$this->name = $system->name;
		if ($system->owner){
			$this->owner = $system->owner->id;
			$this->influence_pool = $system->influence->pool;
			$this->influence_per_turn = $system->influence->per_turn;
			$this->influence_level = $system->influence_level;
			$this->food_per_turn = $system->food->per_turn;
			$this->production_per_turn = $system->production->per_turn;
			
			$this->health = $system->health;
			$this->max_health = $system->max_health;
			
			$this->attack = $system->attack;
			$this->defense = $system->defense;
			$this->damage = $system->damage;
			
			if ($this->owner == $player_id){
				$this->food_pool = $system->food->pool;
				$this->food_growth_threshold = $system->food_growth_threshold;
				$this->growth_turns = $this->food_per_turn ? ceil(($this->food_growth_threshold - $this->food_pool) / $this->food_per_turn) : " - ";
				$this->influence_growth_threshold = $system->influence_growth_threshold;
				$this->influence_growth_turns = $this->influence_per_turn ? ceil(($this->influence_growth_threshold - $this->influence_pool) / $this->influence_per_turn) : " - ";
				$this->influence_size = $system->influence_size;
				
				$this->yours = true;
				$this->build_queue = $system->build_queue->dump($system->production->per_turn);
				
				$this->food = $system->food;
				$this->production = $system->production;
				$this->credits = $system->credits;
				$this->research = $system->research;
				$this->influence = $system->influence;
				
				$this->available_structures = [];
				// generate a list of ids of the structures which can be built here
				if (isset($system->owner->available_structure_classes)){
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
			}
			if ($system->influenced_sectors){
				foreach($system->influenced_sectors as $sector){
					$this->influenced_sectors[$sector->id] = $sector->id;
				}
			}
			
			
		}
		if ($system->planets){
			foreach($system->planets as $planet){
				$this->planets[] = $planet->dump($player);
			}
		}
		if ($system->structures){
			foreach($system->structures as $structure){
				$this->structures[] = $structure->dump($player_id);
			}
		}
		$this->population = $system->population;
		$this->credits_per_turn = $system->credits->per_turn;
		$this->research_per_turn = $system->research->per_turn;
		
		
	}
	
}


class VOID_SYSTEM {
	
	public $food;
	public $credits;
	public $research;
	public $production;
	public $influence;
	
	public $food_modifier = 0;
	public $production_modifier = 0;
	public $influence_modifier = 0;
	public $research_modifier = 0;
	public $credits_modifier = 0;
	
	public $credits_per_turn;
	public $research_per_turn;
	
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
	
	public $health;
	public $max_health;
	
	public $killer;
	
	public function __construct($x, $z){
		$this->x = $x;
		$this->z = $z;
		$this->id = "x".$x."z".$z;
		$this->influence_level = 1;
		$this->influence_per_turn = 0;
		$this->influence_pool = 0;
		$this->population = 0;
		$this->food_pool = 0;
		$this->food_growth_threshold = 10;
		$this->influence_growth_threshold = 10;
		$this->production_per_turn = 0;
		
		$this->planet_index = [];
		
		$this->build_queue = new VOID_QUEUE();
		$this->structures = [];
		
		$this->influenced_sectors = [];				
		
		$this->food = new VOID_RESOURCE("food");
		$this->credits = new VOID_RESOURCE("credits");
		$this->research = new VOID_RESOURCE("research");
		$this->production = new VOID_RESOURCE("production");
		$this->influence = new VOID_RESOURCE("influence");
		
		$this->ship_production = new VOID_RESOURCE("ship_production");
		
		$this->health = 100;
		$this->max_health = 100;
		$this->killer = false;
		
		$this->attack = 50;
		$this->defense = 50;
		$this->damage = 50;
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
	
	public function set_name($name){		
		if ($name){
			$this->name = $name;
		}else {
			$this->name = "";
		}
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
	
	// handle system growth
	public function upkeep(){
		// for now disable planet growth for pirates
		if ($this->owner->player == false){
			return;
		}
		
		$this->influence->upkeep();
		
		// check if a colony ship is being built
		$item = $this->build_queue->get_front();
		if ($item && $item->type == "ship" && $item->data->get_special("colony")){
			// possibly add food to production
			//$this->food_pool += $this->food_per_turn;	
		}else {
			$this->food_pool += $this->food_per_turn;
			$this->food->upkeep();
		}
	}
	
	// check if the system has reached certain thresholds 
	public function resolve(){				
		if ($this->influence->pool >= $this->influence_growth_threshold ){
			$this->influence->pool = 0;
			$this->influence_level++;
			$this->influence_growth_threshold = pow(6, $this->influence_level) * $this->influence_level;
			VOID_LOG::write($this->owner->id, "System at (".$this->x.",".$this->z.") has expanded it's influence.");
		}

		if ($this->food->pool >= $this->food_growth_threshold ){
			$this->population++;			
			$this->food->pool = 0;
			$this->food_growth_threshold = pow(2, $this->population) * 10;
			VOID_LOG::write($this->owner->id, "System at (".$this->x.",".$this->z.") has grown.");
		}
		
		if ($this->influence_level >= 10){
			$this->influence_size = 4;
		}else if($this->influence_level >= 7){
			$this->influence_size = 3;
		}else if($this->influence_level >= 3){
			$this->influence_size = 2;
		}else {
			$this->influence_size = 1;
		}
		$this->influence_size = $this->influence_level;
	}
	
	// recalcuate static income from this system
	public function update(){
		if (!$this->killer){
			$this->health = $this->health + 10;
			if ($this->health > $this->max_health){
				$this->health = $this->max_health;
			}
		}
		if ($this->health <= 0){
			$this->owner = false;
			$this->health = 10;
			if ($this->killer){
				$this->owner = $this->killer;
			}			
		}		
		$this->killer = false;
		$this->food->reset();
		$this->production->reset();
		$this->research->reset();		
		$this->credits->reset();
		$this->influence->reset();
		
		$this->attack = 0;
		$this->defense = 0;
		$this->max_health = 100;
		
		$this->food->per_turn = $this->get_food_income();
		$this->production->per_turn = $this->get_production_income();
		$this->ship_production->per_turn = $this->get_production_income();
		$this->credits->per_turn = $this->get_credits_income();
		$this->research->per_turn = $this->get_research_income();
		$this->influence->per_turn = $this->get_influence_income();
							
		$this->owner->apply_morale(-$this->population, "population");
		$this->owner->apply_morale(-3, "system");
		
		//VOID_DEBUG::write("system update: ".$this->credits_per_turn);
		// resolve all structures 
		if ($this->structures){
			foreach($this->structures as $structure){
				$structure->apply($this);
			}
		}
		
	}
	
	// apply any % modifiers and morale and such
	public function apply(){
		if (!$this->owner){
			return false;
		}
		// add in modifiers from player global effects 
		$this->credits->add_percent($this->owner->credits->percent);
		$this->research->add_percent($this->owner->research->percent);
		$this->production->add_percent($this->owner->production->percent);
		$this->food->add_percent($this->owner->food->percent);
		$this->influence->add_percent($this->owner->influence->percent);
		
		// apply % modifiers
		$this->credits->apply();
		$this->owner->update_resource("credits",$this->credits->per_turn,"system");
		
		$this->research->apply();		
		$this->owner->update_resource("research",$this->research->per_turn,"system");
		
		$this->food->apply();
		// severe food penalty for morale
		if ($this->owner->morale->per_turn < 0){
			//$this->food_per_turn = $this->food_per_turn / 10;
		}
		$this->owner->update_resource("food",$this->food->per_turn,"system");
		
		$this->production->apply();
		$this->owner->update_resource("production",$this->production->per_turn,"system");
		
		$this->influence->apply();
		$this->owner->update_resource("influence",$this->influence_per_turn,"system");
		
		
	}
	
	
	public function update_planets(){
		foreach($this->planets as $planet){
			if ($planet->terraformed){
				$this->update();
			}
		}
	}
	
	public function get_food_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as $planet){
			if ($planet->terraformed){
				$output += $planet->get_food_output() * $this->population * $planet->development;
			}			
		}
		
		if ($this->influenced_sectors){
			foreach($this->influenced_sectors as $sector){
				if ($sector->upgrade && $sector->owner->id == $this->owner->id){
					$modifier = $sector->upgrade->get_modifier("food");
					$output += $modifier;
				}
			}
		}
		/*
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("food");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		*/
		
		return ceil($output);
	}
	
	public function get_influence_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = $this->population;		
		
		if ($this->influenced_sectors){
			foreach($this->influenced_sectors as $sector){
				if ($sector->upgrade && $sector->owner->id == $this->owner->id){
					$modifier = $sector->upgrade->get_modifier("influence");
					$output += $modifier;
				}
			}
		}
		/*
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("influence");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		*/
		return $output;
	}
	
	public function get_credits_income(){
		// returns the credit income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as $planet){
			if ($planet->terraformed){
				$output += $planet->get_credits_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->influenced_sectors){
			foreach($this->influenced_sectors as $sector){
				if ($sector->upgrade && $sector->owner->id == $this->owner->id){
					$modifier = $sector->upgrade->get_modifier("credits");
					$output += $modifier;
				}
			}
		}
		/*
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("credits");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		*/
		return $output;
	}
	
	public function get_research_income(){
		// returns the research income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as $planet){
			if ($planet->terraformed){
				$output += $planet->get_research_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->influenced_sectors){
			foreach($this->influenced_sectors as $sector){
				if ($sector->upgrade && $sector->owner->id == $this->owner->id){
					$modifier = $sector->upgrade->get_modifier("research");
					$output += $modifier;
				}
			}
		}
		/*
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("research");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		*/
		return $output;
	}
	public function get_production_income(){
		// returns the production income from this system 
		// apply tax rate here
		$output = 0;
		foreach($this->planets as $planet){
			if ($planet->terraformed){
				$output += $planet->get_production_output() * $this->population * $planet->development;
			}
		}
		
		if ($this->influenced_sectors){
			foreach($this->influenced_sectors as $sector){
				if ($sector->upgrade && $sector->owner->id == $this->owner->id){
					$modifier = $sector->upgrade->get_modifier("production");
					$output += $modifier;
				}
			}
		}
		/*
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("production");
				if ($modifier){
					$output += $modifier;
				}
			}
		}
		*/
		return $output;
	}
	
	public function apply_morale(){				
		$this->owner->apply_morale(-$this->population, "population");
		$this->owner->apply_morale(-3, "system");		
		if ($this->structures){
			foreach($this->structures as $structure){
				$modifier = $structure->class->get_modifier("morale");
				if ($modifier){
					$this->owner->apply_morale($modifier, "structures");
				}
			}
		}				
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
	
	
	public function dump($player){		
		$view = new VOID_SYSTEM_VIEW($this, $player);
		return $view;
	}
	
	public function colonise($owner, $core, $pid=""){
		if ($this->owner && $this->owner->id != $owner->id){
			return false;
		}
		
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
			$this->owner = $owner;
			$structure = new VOID_STRUCTURE($core->structure_classes["colony_hub"]);
			$this->add_structure($structure);
		}else {
			if (!$this->owner->has_property_index("terraformable", $planet->class['id'])){
				return false;
			}
		}
						
		$planet->colonise();
		$this->owner = $owner;
				
		$this->docked_fleet = new VOID_FLEET();
		$this->docked_fleet->capacity = 10;
		$this->docked_fleet->docked = true;
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
		return $order;
	}
	public function delete_order(){
		
	}
	public function switch_order(){
		
	}
	
	public function process_orders($core){
		$sector = $core->map->get_sector($this->x, $this->z);
		global $ship_classes;
		// if a construction order get the systems production and see if the item is done yet
		
		// apply the progress, if an item is returned, the progress is done, otherwise it is not
		$item = $this->build_queue->progress($this->production->per_turn);
		if ($item){
			// create the created item. 
			if ($item->type == "ship"){
				$ship_class = $item->data;
				$ship = new VOID_SHIP($ship_class, $this->owner);
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
				
				VOID_LOG::write($this->owner->id, "[ship] A ship (".$ship->class->name.") was built at (".$this->x.",".$this->z.")");
			}else if ($item->type == "structure"){
				$structure_class = $item->data;
				// check for world uniqueness
				if ($structure_class->world_unique){
					// make sure no one else can build it now.
					foreach($core->players as $player){
						$player->update_available_structure_class($structure_class->id, true);
					}
					// if 2 people finish at the same time, for now let both build
					// at the end of process turn, purge items from queues they cannot build.
				}
				$structure = new VOID_STRUCTURE($structure_class, $this->owner->id);				
				$this->add_structure($structure);
				VOID_LOG::write($this->owner->id, "[structure] A structure (".$structure->class->name.") was built at (".$this->x.",".$this->z.")");
			}
		}
		
		// subtract production from work left to do
		
		// if work is finished. dispatch built item
		
		// if production left over, apply it to the next item
		
		// only 1 thing can be built per turn regardless though
	}
	
}



?>