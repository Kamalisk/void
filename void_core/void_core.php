<?

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

include_once("void_data.php");
include_once("void_player.php");
include_once("void_tech.php");
include_once("void_sector.php");
include_once("void_system.php");
include_once("void_fleet.php");
include_once("void_map.php");
include_once("void_queue.php");
include_once("void_structure.php");
include_once("void_planet.php");

function void_unique_id(){
	return uniqid("v".rand(100,999));
}

class VOID_DEBUG {
	static $log;
	
	static function write($text){
		self::$log = self::$log.print_r($text,1)."\n\n";
	}
	
	static function dump(){
		return self::$log;
	}
}

class VOID_LOG {
	static $game_id;
	static $turn;
	
	static function init($player_id){
		file_put_contents("log/test.player".$player_id.".log", "");
	}
	
	static function write($player_id, $text){
		if ($player_id){
			file_put_contents("log/test.player".$player_id.".log", $text."\n", FILE_APPEND);
		}
	}
	
	static function get($player_id){
		if ($player_id){
			$contents = file_get_contents("log/test.player".$player_id.".log");
			return VOID_LOG::parse($contents);
		}
	}
	
	static function parse($contents){
		$entries = explode("\n", $contents);
		array_pop($entries);
		return $entries;
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
	public $planet_id;
	public $upgrade_id;
	
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
		if (isset($params['planet_id'])){
			$this->planet_id = $params['planet_id'];
		}
		if (isset($params['upgrade_id'])){
			$this->upgrade_id = $params['upgrade_id'];
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
	public $players = [];
	public $map;
	
	public $ship_classes = [];
	public $structure_classes = [];
	public $upgrade_classes = [];
	public $fleets;
	public $systems;

	public $tech_tree;	
	
	function __construct(){
		$this->map = new VOID_MAP();
		VOID_LOG::$game_id = 1;		
	}
	
	public function setup($width, $height){
		
		$this->setup_tech_tree();
		
		$this->ship_classes = [];		
		$this->structure_classes = [];
		$this->upgrade_classes = [];
		
		$tech = $this->tech_tree->get_tech(1);
		
		// load ship classes from somewhere
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 1;
		$ship_class->name = "Scout";
		$ship_class->movement_capacity = 3;
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 2;
		$ship_class->name = "Colony";
		$ship_class->add_special("colony");
		$ship_class->work_required = 50;
		$ship_class->weapon_count = 0;
		$ship_class->weapon_damage = 0;
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 3;
		$ship_class->name = "Attack";
		$ship_class->work_required = 30;
		$ship_class->weapon_count = 3;
		$ship_class->weapon_damage = 15;
		$tech = $this->tech_tree->get_tech(2);
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 4;
		$ship_class->name = "Speedy";
		$ship_class->work_required = 30;
		$ship_class->weapon_count = 1;
		$ship_class->weapon_damage = 15;
		$ship_class->movement_capacity = 4;
		$tech = $this->tech_tree->get_tech(6);
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 5;
		$ship_class->name = "Speedy Colony";
		$ship_class->work_required = 40;
		$ship_class->add_special("colony");
		$ship_class->weapon_count = 0;
		$ship_class->weapon_damage = 0;
		$ship_class->movement_capacity = 4;
		$tech = $this->tech_tree->get_tech(3);
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$ship_class = new VOID_SHIP_CLASS();
		$ship_class->id = 6;
		$ship_class->name = "Constructor";
		$ship_class->work_required = 40;
		$ship_class->add_special("construct");
		$ship_class->weapon_count = 0;
		$ship_class->weapon_damage = 0;
		$ship_class->movement_capacity = 6;
		$tech = $this->tech_tree->get_tech(3);
		$tech->add_ship_class($ship_class);
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 1;
		$structure_class->name = "Captial";
		$structure_class->set_unique("empire");
		$structure_class->set_modifier("food", 10);
		$structure_class->set_modifier("morale", 10);
		$structure_class->set_modifier("production", 10);
		$structure_class->set_modifier("credits", 10);
		$structure_class->set_modifier("influence", 10);
		$structure_class->set_modifier("research", 10);
		$tech = $this->tech_tree->get_tech(1);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 2;
		$structure_class->name = "Farm";
		$structure_class->work_required = 30;
		$structure_class->set_modifier("food", 5);
		$structure_class->set_modifier("credits", -1);
		$tech = $this->tech_tree->get_tech(3);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 3;
		$structure_class->name = "Bank";		
		$structure_class->work_required = 30;
		$structure_class->set_modifier("credits", 5);
		$tech = $this->tech_tree->get_tech(4);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 4;
		$structure_class->name = "Happy Place";
		$structure_class->work_required = 30;
		$structure_class->set_modifier("morale", 10);
		$structure_class->set_modifier("credits", -1);
		$tech = $this->tech_tree->get_tech(4);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 5;
		$structure_class->name = "Factory";
		$structure_class->work_required = 60;
		$structure_class->set_modifier("production", 10);
		$structure_class->set_modifier("credits", -1);
		$tech = $this->tech_tree->get_tech(2);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 6;
		$structure_class->name = "Research Lab";
		$structure_class->work_required = 30;
		$structure_class->set_modifier("research", 10);
		$structure_class->set_modifier("credits", -1);
		$tech = $this->tech_tree->get_tech(6);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 6;
		$structure_class->name = "Galactic Radio Station";
		$structure_class->work_required = 60;
		$structure_class->set_modifier("influence", 10);
		$structure_class->set_modifier("credits", -10);
		$tech = $this->tech_tree->get_tech(5);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		$structure_class = new VOID_STRUCTURE_CLASS();
		$structure_class->id = 7;
		$structure_class->name = "80s Cartoon Museum";
		$structure_class->set_unique("world");
		$structure_class->work_required = 300;
		$structure_class->set_modifier("influence", 20);		
		$tech = $this->tech_tree->get_tech(5);
		$tech->add_structure_class($structure_class);
		$this->structure_classes[$structure_class->id] = $structure_class;
		
		
		$power_class = new VOID_UPGRADE_CLASS();
		$power_class->id = 1;
		$power_class->name = "Space Nebula Shop";
		$power_class->work_required = 40;
		$tech = $this->tech_tree->get_tech(4);
		$tech->add_upgrade_class($power_class);
		$this->upgrade_classes[$power_class->id] = $power_class;
		
		
		$starting_tech = $this->tech_tree->get_starting_tech();
		
		$this->map = new VOID_MAP();
		$this->players = [];
		for($i = 1; $i < 12; $i++){
			$this->players[$i] = new VOID_PLAYER($i);
			$this->players[$i]->name = "Player ".$i;
			$this->players[$i]->set_tech($this->tech_tree);
			$this->players[$i]->done = false;
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
			//$this->ship_classes = unserialize(file_get_contents("test.ship_classes.void"));
		}
	}
	public function save(){
		// store the game 
		file_put_contents("test.map.void", serialize($this->map));
		file_put_contents("test.players.void", serialize($this->players));
		file_put_contents("test.ship_classes.void", serialize($this->ship_classes));
	}
	
	public function dump_map($player_id, $first=false){
		global $void_planet_classes;
		global $void_sector_classes;
		
		header("Content-type: application/json");
		$return = array();
		$return['debug'] = VOID_DEBUG::dump();
		$return['map'] = $this->map->dump_map($player_id);
		$return['players'] = [];
		foreach($this->players as $player){
			$return['players'][$player->id] = $player->dump($player_id);
		}
		$return['players'][$player_id] = $this->players[$player_id];
		$return['player'] = $this->players[$player_id];
		$return['logs'] = VOID_LOG::get($player_id);
		if ($first){
			$return['planet_classes'] = $void_planet_classes;
			$return['sector_classes'] = $void_sector_classes;
			$return['ship_classes'] = $this->ship_classes;
			$return['upgrade_classes'] = $this->upgrade_classes;
			$return['structure_classes'] = $this->structure_classes;
			$return['tech_tree'] = $this->tech_tree->dump();			
		}
		//$this->map->players = $this->players;
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	public function dump_status($player_id){
		global $void_planet_classes;
		global $void_sector_classes;
		
		header("Content-type: application/json");
		$return = array();		
		$return['players'] = [];
		foreach($this->players as $player){
			$return['players'][$player->id] = $player->dump($player_id);
		}
		$return['players'][$player_id] = $this->players[$player_id];
		$return['player'] = $this->players[$player_id];
		$return['debug'] = VOID_DEBUG::dump();	
		//$this->map->players = $this->players;
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	public function reset_player_state(){
		foreach($this->players as $player){
			$player->done = false;
			//$player->done = false;
		}
	}
	public function are_players_finished(){
		return true;
		// for now return true, so turn can be processed quickly for testing		
		foreach($this->players as $player){
			if (!$player->done){
				return false;
			}
		}
		return true;
	}
	
	// resolve all orders and game events
	// including moving, battle and research
	public function process_turn(){
		
		// reset player values 
		foreach($this->players as $player){			
			VOID_LOG::init($player->id);
			$player->sector_count = 0;
			$player->tech_count = 0;
			$player->sources = [];
		}
		
		// income and upkeep
		foreach($this->players as $player){			
			// update resources on the values calculate previously
			$player->apply_resources();
		}
		
		// resolve tech tree
		foreach($this->players as $player){
			$player->update_research($player->research_per_turn, $this->tech_tree);
		}
		
		// reset players per turn to 0
		foreach($this->players as $player){
			$player->reset_per_turn();
		}		
		
		$temp_fleet_cache = array();
		$temp_sector_has_fleet = array();
		
		$temp_systems = array();
		
		$combat_sectors = [];
		
		// run through every fleet in the game and find the ones with orders
		foreach($this->map->sectors as $key => $sector){
			if ($sector->fleets){
				$combat_sectors[] = $sector;
				foreach($sector->fleets as $player_id => $player_fleets){
					foreach($player_fleets as $fleet){
						$fleet->reset_movement_points();
						$temp_fleet_cache[] = $fleet;						
					}
				}
			}
			if ($sector->system && $sector->system->owner){				
				$temp_systems[] = $sector->system;
				
				$sector->system->upkeep();
				
				// construction
				// here for now at least				
				$sector->system->process_orders($sector, $this);			
			}
		}

		
		// combat + fleet orders
		
		// if two fleets cross paths resolve a battle
		// if two fleets try to move to the same space, resolve a battle
		// if more than two fleets trigger this, pick 2 opposing ones randomly
		// first fleet to move into space cancels other moves 
		
		// loop until all fleets have used up all their movement capacity
		$continue = true;
		while ($continue){
			$continue = false;
			foreach($temp_fleet_cache as $fleet){
				$order = $fleet->get_order();
				if ($order){
					if ($order->type == "move" && $fleet->movement_points > 0){
						//print_r($fleet);
						$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
						$sector2 = $this->map->get_sector($order->x, $order->z);
						if ($sector1 && $sector2){
							
							if ($fleet->movement_points >= $sector2->movement_cost){								
								$fleet->movement_points = $fleet->movement_points - $sector2->movement_cost;
								//$sector2->add_fleet($fleet);
								//$sector1->remove_fleet($fleet);
								$fleet->move($order->x, $order->z, $this);
								$combat_sectors[] = $sector2;
							}else {
								$fleet->movement_points = 0;
								if ($sector2->movement_cost > $fleet->movement_capacity){
									$fleet->reset_orders();									
								}else {
									$fleet->put_order($order);
								}
							}
						}
						$continue = true;
					}else if ($order->type == "colonise"){
						$sector = $this->map->get_sector($fleet->x, $fleet->z);
						if ($sector->system && $fleet->get_special("colony")){							
							$sector->system->colonise($this->players[$fleet->owner], $this, $order->planet_id);							
							$fleet->movement_points = 0;							
							$sector->system->resolve();
							$sector->system->update();							
							if ($fleet->remove_special("colony")){
								// delete the fleet!								
								$sector->clean_up();
							}							
							//$sector->system->owner = $fleet->owner;
						}
					}else if ($order->type == "construct"){						
						$sector = $this->map->get_sector($fleet->x, $fleet->z);
						VOID_DEBUG::write($order);
						if ($fleet->get_special("construct") && $order->upgrade_id){							
							$upgrade = $this->upgrade_classes[$order->upgrade_id];
							$sector->add_upgrade($upgrade);							
							$fleet->movement_points = 0;														
							if ($fleet->remove_special("construct")){
								// delete the fleet!								
								$sector->clean_up();
							}														
						}
					}else {
						$fleet->movement_points = 0;
						$fleet->put_order($order);
					}
				}
			}
			
			// resolve combat
			foreach($combat_sectors as $sector){
				$sector->resolve_combat();
			}
			$combat_sectors = [];
		}
		
		// resolve building orders and growth
		foreach($temp_systems as $system){
			$system->resolve();
		}				
		
		
		
		foreach($temp_systems as $system){
			$system->update();
		}
		foreach($temp_fleet_cache as $fleet){
			$fleet->update($this);
		}		
		
		//check for victory and update victory totals	

		$this->map->update_map($this);
		$this->reset_player_state();
		//$this->save();
	}
	
	public function handle_input($input, $player_id){
		if (isset($input['action'])){
			$player = $this->players[$player_id];
			
			
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
									$this->fleets[$key]->add_order("colonise", array("x"=>$order['x'], "z"=>$order['z'], "planet_id" => $order['planet_id']));
								}else if ($order['type'] == "construct"){									
									$this->fleets[$key]->add_order("construct", array("x"=>$order['x'], "z"=>$order['z'], "upgrade_id" => $order['upgrade_id']));
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
								if ($order['type'] == "ship"){
									$target = $this->ship_classes[$order['target_id']];
								}else if ($order['type'] == "structure"){
									$target = $this->structure_classes[$order['target_id']];
								}
								
								$this->systems[$key]->add_order($order['type'], $target);
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
				
				$player->done = true;
				
				// if all players have ended their turn.
				// process a turn
				// (this might need to be in a regularly run cron job)
				// for now it is run every time someone ends their turn
				if ($this->are_players_finished()){
					$this->process_turn();
				}else {
					
				}				
			} else if ($input['action'] == "reset"){
					$this->map = new VOID_MAP();
					$this->setup(60,40);
					//$this->save();
			}
		}
	}
	
}

?>