<?

include_once("void_data.php");
include_once("void_player.php");
include_once("void_tech.php");
include_once("void_sector.php");
include_once("void_system.php");
include_once("void_fleet.php");
include_once("void_map.php");

function void_unique_id(){
	return uniqid("v".rand(100,999));
}


class VOID_LOG {
	static $game_id;
	
	static function init($player_id){
		file_put_contents("log/test.player".$player_id.".log", "");
	}
	
	static function write($player_id, $text){
		if ($player_id){
			file_put_contents("log/test.player".$player_id.".log", $text."\n", FILE_APPEND);
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
		VOID_LOG::$game_id = 1;
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
		$this->ship_classes[$ship_class->id] = $ship_class;
		
		$tech = $this->tech_tree->get_tech(2);
		$tech->add_ship_class($ship_class);
		
		
		
		
		$starting_tech = $this->tech_tree->get_starting_tech();
		
		$this->map = new VOID_MAP();
		$this->players = [];
		for($i = 1; $i < 3; $i++){
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
	
	public function dump_status($player_id){
		global $void_planet_classes;
		global $void_sector_classes;
		
		header("Content-type: application/json");
		$return = array();		
		$return['players'] = $this->players;
		$return['player'] = $this->players[$player_id];		
		//$this->map->players = $this->players;
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	public function reset_player_state(){
		foreach($this->players as &$player){
			$player->done = false;
		}
	}
	public function are_players_finished(){
		foreach($this->players as &$player){
			if (!$player->done){
				return false;
			}
		}
		return true;
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
						if ($sector->system && $fleet->get_special("colony")){
							$sector->system->colonise($this->players[$fleet->owner], $this->core);
							if ($fleet->remove_special("colony")){
								// delete the fleet!
								$sector->clean_up();
							}
							//$sector->system->owner = $fleet->owner;
						}
					}else {
						 $fleet->put_order($order);
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
				
					
				
					$this->setup(30,20);
					//$this->save();
			}
		}
	}
	
}

?>