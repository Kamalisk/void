<?


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
	
	function __construct(){

	}	
}


class VOID_QUEUE {
	public $items;

	function __construct(){
		$this->items = [];
	}
	public function add($item){
		$this->items[] =& $item;
	}
	public function remove(){
		
	}
	public function pop(){
		return array_shift($this->items);
	}
	public function swap(){
		
	}
	public function get_front(){
		return reset($this->items);
	}
	public function progress($work){
		$item = $this->get_front();
		if ($item){
			$item->progress = $item->progress - $work;
			if ($item->progress <= 0){
				return $this->pop();
			}
		}
		return false;
	}
	public function dump($work=0){
		return new VOID_QUEUE_VIEW($this, $work);
	}
}

class VOID_QUEUE_VIEW {
	public $items;
	function __construct($queue, $work=0){
		$this->items = array();
		foreach($queue->items as &$item){
			$this->items[] = $item->dump($work);
		}
	}
	
}

class VOID_QUEUE_ITEM {
	public $id;
	public $data;
	public $progress;
	public $target;
	public $type;
	
	function __construct(){
		// generate a "random" unique id for the queue
		$this->id = uniqid(rand(100,999));
		
	}
}

class VOID_SYSTEM_QUEUE_ITEM extends VOID_QUEUE_ITEM {
	public $work;
	
	public function dump($work){
		return new VOID_SYSTEM_QUEUE_ITEM_VIEW($this, $work);
	}
}
class VOID_SYSTEM_QUEUE_ITEM_VIEW {
	public $type;
	public $id;
	public $target_id;
	public $turns;
	
	function __construct($item, $work=0){
		$this->id = $item->id;
		$this->type = $item->type;
		$this->target_id = $item->data->id;
		if ($work){
			$this->turns = ceil($item->progress / $work);
		}
	}
}




class VOID_SYSTEM_VIEW {
	public $owner;
	public $planets;
	
	public $influence_level;
	public $influence_per_turn;
	public $influence_pool;
	public $influence_growth_threshold;
	public $influence_growth_turns;
	
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
	
	function __construct($system, $player_id){
		$this->id = $system->id;
		if ($system->owner){
			$this->owner = $system->owner->id;
			$this->influence_pool = $system->influence_pool;
			$this->influence_per_turn = $system->influence_per_turn;
			$this->influence_level = $system->influence_level;
			$this->food_per_turn = $system->food_per_turn;
			$this->production_per_turn = $system->production_per_turn;
			$this->food_pool = $system->food_pool;
			$this->food_growth_threshold = $system->food_growth_threshold;
			$this->growth_turns = $this->food_per_turn ? ceil(($this->food_growth_threshold - $this->food_pool) / $this->food_per_turn) : " - ";
			$this->influence_growth_threshold = $system->influence_growth_threshold;
			$this->influence_growth_turns = $this->influence_per_turn ? ceil(($this->influence_growth_threshold - $this->influence_pool) / $this->influence_per_turn) : " - ";
			if ($this->owner == $player_id){
				$this->build_queue = $system->build_queue->dump($system->production_per_turn);
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
	
	public $population;
	
	public $orders;
	
	public $build_queue;
	
	public $id;
	
	public $docked_fleet;
	public $structures;
	
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
		
		$this->build_queue = new VOID_QUEUE();
		$this->structures = [];
	}
	
	public function add_planet($planet){
		$this->planets[] = $planet;
	}
	
	public function add_structure($structure){
		// should check to see if it already exists etc..
		$this->structures[] = $structure;
	}
	
	public function update(){
		
		$this->food_per_turn = $this->get_food_income();
		if ($this->owner->morale < 0){
			$this->food_per_turn = $this->food_per_turn / 10;
		}
		$this->production_per_turn = $this->get_production_income();
		
		$this->influence_growth_threshold = ($this->influence_level+1) * 3;
		
		// first update per turn values 
		$this->influence_pool += $this->influence_per_turn;
		if ($this->influence_pool >= $this->influence_growth_threshold ){
			$this->influence_pool = 0;
			$this->influence_level++;
			$this->influence_growth_threshold = ($this->influence_level+1) * 3;
		}
		
		$this->food_growth_threshold = pow(2, $this->population);
		$this->food_pool += $this->food_per_turn;
		if ($this->food_pool >= $this->food_growth_threshold ){
			$this->population++;
			$this->food_growth_threshold = pow(2, $this->population);
			$this->food_per_turn = $this->get_food_income();
			$this->food_pool = 0;
			$this->influence_per_turn = $this->population;
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
		return $output;
	}
	
	public function get_morale(){
		$morale = 0;
		$morale = $morale - $this->population;
		$morale = $morale - 3;
		// apply any buildings or empire modifiers here!
		return $morale;
	}
	
	
	public function dump($player_id){
		$view = new VOID_SYSTEM_VIEW($this, $player_id);
		return $view;
	}
	
	public function colonise($owner, $core){
		
		$key = array_rand($this->planets);
		$planet =& $this->planets[$key];
		$planet->colonise();
		$this->owner = $owner;
		$this->population = 1;
		$this->influence_level = 1;
		$this->influence_per_turn = $this->population;
		$this->food_per_turn = $this->get_food_income();
		$this->production_per_turn = $this->get_production_income();
		$this->docked_fleet = new VOID_FLEET();
		$this->docked_fleet->capacity = 10;
		$core->fleets[$this->docked_fleet->id] = $this->docked_fleet;
		VOID_LOG::write($owner->id, "System was colonised");
	}
	
	public function add_order($type, $target){
		$order = new VOID_SYSTEM_QUEUE_ITEM();		
		$order->data = $target;		
		$order->type = "build";
		$order->progress = $target->work_required;
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
			if ($item->type == "build"){
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
			}
		}
		
		// subtract production from work left to do
		
		// if work is finished. dispatch built item
		
		// if production left over, apply it to the next item
		
		// only 1 thing can be built per turn regardless though
	}
	
}




class VOID_PLANET_VIEW {
	public $name;
	public $class_id;
	public $population;
	public $terraformed;
	function __construct($planet, $player_id){
		$this->name = $planet->name;
		$this->class_id = $planet->class['id'];
		$this->terraformed = $planet->terraformed;
		$this->population = $planet->population;
	}
}

class VOID_PLANET {
	public $name = "";
	public $class = "";
	
	public $population;
	public $max_population;
	public $terraformed;
	
	public $development; 
	
	function __construct(){
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
	
	public function dump($player_id){
		$view = new VOID_PLANET_VIEW($this, $player_id);
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


?>