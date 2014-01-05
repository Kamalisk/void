<?

include_once("void_data.php");
include_once("void_tech.php");
include_once("void_sector.php");
include_once("void_system.php");
include_once("void_fleet.php");
include_once("void_map.php");

function void_unique_id(){
	return uniqid("v".rand(100,999));
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
	
	public $tech;
	public $available_tech;
	public $current_tech;
	
	public $available_ship_classes;
	
	function __construct($id){
		$this->id = $id;
		$this->research_pool = 0;
		$this->credits_pool = 0;
		$this->current_tech = false;
	}
	
	public function set_color($color){
		$this->color = $color;
	}
	
	public function set_tech($tech_tree){
		$techs = $tech_tree->get_starting_tech();
		foreach($techs as &$tech){
			$this->tech[$tech->id] = new VOID_TECH_ITEM($tech);
			$this->add_new_ship_classes($tech);
		}
		$this->update_available_tech($tech_tree);
	}
	
	public function update_research($work, $tech_tree){
		if ($this->current_tech){
			echo "tech has arrived \n";
			$this->current_tech->progress = $this->current_tech->progress - $work;
			if ($this->current_tech->progress <= 0){
				$this->tech[$this->current_tech->class->id] = $this->current_tech;
				$this->add_new_ship_classes($this->current_tech->class);
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
	
}








class VOID_ORDER_VIEW {
	public $type;
	public $delta_x;
	public $delta_y;
	
}

class VOID_ORDER {
	public $type;
	public $delta_x;
	public $delta_z;
	public $owner;
	public $x;
	public $z;
	
	function __construct($type){
		$this->type = $type;
	}
	
	function add_params($params){
		if (isset($params['x'])){
			$this->x = $params['x'];
		}
		if (isset($params['z'])){
			$this->z = $params['z'];
		}
	}
	
	public function dump_view($player_id){
		if ($this->owner == $player_id){
			return new VOID_ORDER_VIEW($this, $player_id);
		}else {
			return false;
		}
	}
	
	
	
}


class VOID_VIEW {
	static $show = true;
	
	static function should_show(){
		if ($this->show){
			return true;
		}else {
			return false;
		}
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

class VOID_RESOURCES {
	public $food;
	public $production;
	public $research;
	public $credits;
	
	function __construct($a, $b, $c, $d){
		$this->food = $a;
		$this->production = $b;
		$this->research = $c;
		$this->credits = $d;
	}
	
}



class VOID {
	public $players = array();
	public $map;
	
	public $ship_classes = array();
	
	public $fleets;
	public $systems;

	public $tech_tree;	
	
	function __construct(){
		$this->map = new VOID_MAP();
	}
	
	public function setup($width, $height){
		
		$this->setup_tech_tree();
		
		$tech = $this->tech_tree->get_tech(1);
		
		// load ship classes from somewhere
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 1;
		$ship_class->name = "Scout";
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 2;
		$ship_class->name = "Colony";
		$ship_class->work_required = 50;
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 3;
		$ship_class->name = "Attack";
		$ship_class->work_required = 30;
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$tech = $this->tech_tree->get_tech(2);
		$tech->add_ship_class($ship_class);
		
		
		
		
		$starting_tech = $this->tech_tree->get_starting_tech();
		
		$this->map = new VOID_MAP();
		$this->players = [];
		for($i = 1; $i < 12; $i++){
			$this->players[$i] = new VOID_PLAYER($i);
			$this->players[$i]->name = "Player ".$i;
			$this->players[$i]->set_tech($this->tech_tree);
		}
		
		$this->map->generate($width, $height, $this);
		$this->map->populate($this);
		
		//$this->map->generate_views($this->players);		
	}

	public function setup_tech_tree(){
		// init tech tree

		$this->tech_tree = new VOID_TECH_TREE();
		$this->tech_tree->init();
		
		
	}


	public function load(){
		// load the game
		if (file_exists("test.map.void")){
			$this->map = unserialize(file_get_contents("test.map.void"));
		}
		if (file_exists("test.players.void")){
			$this->players = unserialize(file_get_contents("test.players.void"));
		}
		if (file_exists("test.ship_classes.void")){
			$this->ship_classes = unserialize(file_get_contents("test.ship_classes.void"));
		}
		if (file_exists("test.ship_classes.void")){
			$this->ship_classes = unserialize(file_get_contents("test.ship_classes.void"));
		}
	}
	public function save(){
		// store the game 
		file_put_contents("test.map.void", serialize($this->map));
		file_put_contents("test.players.void", serialize($this->players));
		file_put_contents("test.ship_classes.void", serialize($this->ship_classes));
	}
	
	public function dump_map($player_id){
		global $void_planet_classes;
		global $void_sector_classes;
		
		header("Content-type: application/json");
		$return = array();
		$return['map'] = $this->map->dump_map($player_id);
		$return['players'] = $this->players;
		$return['player'] = $this->players[$player_id];
		$return['planet_classes'] = $void_planet_classes;
		$return['sector_classes'] = $void_sector_classes;
		$return['ship_classes'] = $this->ship_classes;
		$return['tech_tree'] = $this->tech_tree->dump();
		//$this->map->players = $this->players;
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	// resolve all orders and game events
	// including moving, battle and research
	public function process_turn(){
		
		$temp_fleet_cache = array();
		$temp_sector_has_fleet = array();
		
		$temp_systems = array();
		
		$combat_sectors = [];
		
		// run through every fleet in the game and find the ones with orders
		// calculate 
		foreach($this->map->sectors as $key => &$sector){
			if ($sector->fleets){
				$combat_sectors[] =& $sector;
				foreach($sector->fleets as $player_id => &$player_fleets){
					foreach($player_fleets as &$fleet){
						$fleet->reset_movement_points();
						$temp_fleet_cache[] =& $fleet;
						
					}
				}
			}
			if ($sector->system && $sector->system->owner){
				$sector->system->process_orders($sector, $this);
				$temp_systems[] =& $sector->system;
			}
		}
		
		// loop until all fleets have used up all their movement capacity
		$continue = true;
		while ($continue){
			$continue = false;
			foreach($temp_fleet_cache as &$fleet){
				$order = $fleet->get_order();
				if ($order){
					if ($order->type == "move" && $fleet->movement_points > 0){
						//print_r($fleet);
						$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
						$sector2 = $this->map->get_sector($order->x, $order->z);
						if ($sector1 && $sector2){
							$fleet->movement_points--;
							//$sector2->add_fleet($fleet);
							//$sector1->remove_fleet($fleet);
							$fleet->move($order->x, $order->z, $this);
							$combat_sectors[] =& $sector2;
						}
						$continue = true;
					}else if ($order->type == "colonise"){
						$sector = $this->map->get_sector($fleet->x, $fleet->z);
						if ($sector->system){
							$sector->system->colonise($this->core);
							$sector->system->owner = $fleet->owner;
						}
					}
				}
			}
			
			// resolve combat
			foreach($combat_sectors as &$sector){
				$sector->resolve_combat();
			}
			$combat_sectors = [];
		}
		
		// find all planets with orders
		
		// process fleet move orders
		
		// if two fleets cross paths resolve a battle
		// if two fleets try to move to the same space, resolve a battle
		// if more than two fleets trigger this, pick 2 opposing ones randomly
		// first fleet to move into space cancels other moves 
		
		// resolve income from all systems
		foreach($this->players as &$player){
			$player->credits_per_turn = 0;
			$player->research_per_turn = 0;
			
		}
		
		foreach($temp_systems as &$system){
			$system->update();
			$player =& $this->players[$system->owner];
			$credits_per_turn = $system->get_credits_income();
			$player->credits_pool += $credits_per_turn;
			$player->credits_per_turn += $credits_per_turn;
			
			$research_per_turn = $system->get_research_income();
			$player->research_pool += $research_per_turn;
			$player->research_per_turn += $research_per_turn;			
		}
		
		foreach($this->players as &$player){
			
			$player->update_research($player->research_per_turn, $this->tech_tree);
		}
				
		$this->map->update_map();
		//$this->save();
	}
	
	public function handle_input($input, $player_id){
		if (isset($input['action'])){
			if ($input['action'] == "end_turn"){
				
				if (isset($_POST['fleet_orders'])){
					$fleet_orders = $_POST['fleet_orders'];
					foreach($fleet_orders as $key => $orders){
						if (isset($this->fleets[$key])){
							$this->fleets[$key]->reset_orders();
							foreach($orders as $order){
								if ($order['type'] == "move"){
									$this->fleets[$key]->add_order("move", array("x"=>$order['x'], "z"=>$order['z']));
								}else if ($order['type'] == "colonise"){
									$this->fleets[$key]->add_order("colonise", array("x"=>$order['x'], "z"=>$order['z']));
								}
							}
						}
					}
				}
				
				if (isset($_POST['system_orders'])){
					$system_orders = $_POST['system_orders'];
					//print_r($system_orders);
					//print_r($this->systems);
					foreach($system_orders as $key => $orders){
						
						if (isset($this->systems[$key])){
							//$this->systems[$key]->reset_orders();
							foreach($orders as $order){
								//echo $key."  -- \n";				
								//echo $order['target_id'];
								$target = $this->ship_classes[$order['target_id']];
								$this->systems[$key]->add_order("build", $target);
							}
						}
					}
				}
				
				if (isset($_POST['current_tech']) && $_POST['current_tech']){
					$player = $this->players[$player_id];
					if ($player->available_tech[$_POST['current_tech']]){
						$player->current_tech = $player->available_tech[$_POST['current_tech']];
					}
				}
				
				// if all players have ended their turn.
				// process a turn
				// (this might need to be in a regularly run cron job)
				// for now it is run every time someone ends their turn
				$this->process_turn();
			} else if ($input['action'] == "reset"){
				
					$this->map = new VOID_MAP();
				
					
				
					$this->setup(30,20);
					//$this->save();
			}
		}
	}
	
}

?>