<?

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

include_once("password_hash.php");
include_once("void_user.php");
include_once("void_modifier.php");
include_once("void_debug.php");
include_once("void_log.php");
include_once("void_lobby.php");
include_once("void_data.php");
include_once("void_player.php");
include_once("void_tech.php");
include_once("void_sector.php");
include_once("void_system.php");
include_once("void_fleet.php");
include_once("void_map.php");
include_once("void_queue.php");
include_once("void_structure.php");

include_once("void_resource.php");
include_once("void_planet.php");
include_once("void_order.php");

function void_unique_id(){
	return uniqid("v".rand(100,999));
}



// upkeep()
// handle costs from last turn
// resolve()
// handle building and any orders and such
// update()
// update static values
// apply()
// apply any % modifiers and other things which need to be done after the fact 



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
	public $lobby;
	
	public $players = [];
	
	public $state = "lobby";
	public $map;
	
	public $ship_classes = [];
	public $structure_classes = [];
	public $upgrade_classes = [];
	public $power_classes = [];
	public $races = [];
	public $leaders = [];
	public $empires = [];
	
	public $fleets;
	public $systems;
	public $opinions;
	
	public $tech_tree;	
	
	public $game_id;
	public $game_path;
	
	function __construct($id=1){
		$this->lobby = new VOID_LOBBY();
		$this->map = new VOID_MAP();
		VOID_LOG::$game_id = $id;	
		$this->game_id = $id;
		$this->game_path = "games/".$this->game_id."/";
		if (!file_exists($this->game_path)){
			mkdir($this->game_path);
		}
		
	}
	
	public function post_load(){
		VOID_LOG::$game_id = $this->game_id;	
		VOID_LOG::$game_path = $this->game_path;	
	}
	
	public function join_game($name){		
		return $this->lobby->add_player($name);
	}
	
	public function start_game(){
		$this->state = "game";
		$this->players = [];
		foreach($this->lobby->players as $player){
			$this->add_player($player);			
		}
		$this->start(8, 8);
	}
	
	public function init(){
		
		$this->players = [];
		$this->state = "lobby";
		
		// temporarily create the players here
		/*
		for($i = 1; $i < 8; $i++){
			$this->add_player("");			
		}
		*/
	}
	
	public function setup(){
		
		$this->setup_tech_tree();
		
		$this->ship_classes = [];		
		$this->structure_classes = [];
		$this->upgrade_classes = [];
		$this->power_classes = [];
		$this->opinions = [];
		$this->races = [];
		$this->leaders = [];
		$this->empires = [];
		
		// contains most of the raw data for classes
		include("void_setup.php");
		
		//$this->start($width, $height);
		//$this->map->generate_views($this->players);		
	}
	
	public function start($width, $height){
		$this->setup();
		$starting_tech = $this->tech_tree->get_starting_tech();
		$this->map = new VOID_MAP();
		foreach($this->players as $player){
			$player->reset();
			$player->update();
			$player->set_tech($this->tech_tree);
			$player->done = false;
		}
		
		$i = 999;
		// add the neutral players 
		$this->players[$i] = new VOID_PLAYER($i);
		$this->neutral_player = $this->players[$i];
		$this->neutral_player->player = false;
		$this->players[$i]->set_tech($this->tech_tree);
		$this->players[$i]->done = true;
		$this->neutral_player->name = "Bavarian Space Pirates";
		
		$this->map->generate($width, $height, $this);
		$this->map->populate($this);
	}

	public function add_player($data){
		$id = count($this->players)+1;
		//$player = new VOID_PLAYER($id);
		$player = clone $data;				
		$this->players[$player->id] = $player;
		return $player;
	}


	public function setup_tech_tree(){
		// init tech tree

		$this->tech_tree = new VOID_TECH_TREE();
		$this->tech_tree->init();
		
		
	}


	public function load(){
		// load the game
		if (file_exists($this->game_data_path."map.void")){
			$this->map = unserialize(file_get_contents($this->game_data_path."map.void"));
		}
		if (file_exists($this->game_data_path."players.void")){
			$this->players = unserialize(file_get_contents($this->game_data_path."players.void"));
		}
		if (file_exists($this->game_data_path."ship_classes.void")){
			$this->ship_classes = unserialize(file_get_contents($this->game_data_path."ship_classes.void"));
		}
		if (file_exists("test.ship_classes.void")){
			//$this->ship_classes = unserialize(file_get_contents("test.ship_classes.void"));
		}
	}
	public function save(){
		// store the game 
		file_put_contents($this->game_data_path."map.void", serialize($this->map));
		file_put_contents($this->game_data_path."players.void", serialize($this->players));
		file_put_contents($this->game_data_path."ship_classes.void", serialize($this->ship_classes));
	}
	
	public function dump_map($player_id, $first=false){
		global $void_planet_classes;
		global $void_sector_classes;				
		
		// don't send any map data when the game is in lobby mode!
		if ($this->state == "lobby"){
			return false;
		}
		if (!isset($this->players[$player_id])){
			return false;
		}
		header("Content-type: application/json");
		$return = array();
		$return['debug'] = VOID_DEBUG::dump();
		$return['state'] = $this->state;
		$return['map'] = $this->map->dump_map($this->players[$player_id]);
		$return['players'] = [];
		foreach($this->players as $player){
			$return['players'][$player->id] = $player->dump($player_id);
		}
		if ($player_id){
			$return['players'][$player_id] = $this->players[$player_id];
			$return['player'] = $this->players[$player_id];
			$return['logs'] = VOID_LOG::get($player_id);
		}
		
		if ($first){
			$return['planet_classes'] = $void_planet_classes;
			$return['sector_classes'] = $void_sector_classes;
			$return['ship_classes'] = $this->ship_classes;
			$return['upgrade_classes'] = $this->upgrade_classes;
			$return['structure_classes'] = $this->structure_classes;
			$return['power_classes'] = $this->power_classes;
			$return['tech_tree'] = $this->tech_tree->dump();			
		}
		$return['debug'] = VOID_DEBUG::dump();
		
		//$this->map->players = $this->players;
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	public function dump_status($player_id){
		global $void_planet_classes;
		global $void_sector_classes;
		header("Content-type: application/json");
		// if the game is in lobby mode. only return basic player data (currently returns too much I think)
		if ($this->state == "lobby"){
			$return = array();		
			$return['players'] = [];
			$return['game_id'] = $this->game_id;
			$return['state'] = $this->state;
			foreach($this->lobby->players as $player){
				$return['players'][$player->id] = $player->dump($player_id);
			}
			if ($player_id && isset($this->lobby->players[$player_id])){				
				$return['player'] = $this->lobby->players[$player_id];
			}
			$return['races'] = $this->races;
			$return['colors'] = $this->lobby->colors;
		}else {			
			$return = array();
			$return['game_id'] = $this->game_id;
			$return['players'] = [];
			$return['state'] = $this->state;
			foreach($this->players as $player){
				$return['players'][$player->id] = $player->dump($player_id);
			}
			if ($player_id){
				$return['players'][$player_id] = $this->players[$player_id];
				$return['player'] = $this->players[$player_id];
			}
			$return['debug'] = VOID_DEBUG::dump();	
			//$this->map->players = $this->players;
		}
		echo json_encode($return, JSON_NUMERIC_CHECK);
	}
	
	public function reset_player_state(){
		foreach($this->players as $player){
			if ($player->player){
				$player->done = false;
			}
			//$player->done = false;
		}
	}
	public function are_players_finished(){
		return true;
		// for now return true, so turn can be processed quickly for testing		
		foreach($this->players as $player){
			if (!$player->done && $player->player){
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
			
			$player->upkeep();
		}
		
		// resolve tech tree
		foreach($this->players as $player){
			$player->update_research($this->tech_tree);
		}
		
		// reset players per turn to 0
		foreach($this->players as $player){
			$player->reset_per_turn();
		}		
		
		$temp_fleet_cache = array();
		$temp_sector_has_fleet = array();
		
		$temp_systems = array();
		
		$combat_sectors = [];
		$temp_fleets_with_orders = [];
		// run through every fleet in the game and find the ones with orders
		foreach($this->map->sectors as $key => $sector){
			if ($sector->fleets){
				$combat_sectors[] = $sector;
				foreach($sector->fleets as $player_id => $player_fleets){
					foreach($player_fleets as $fleet){
						$fleet->reset_movement_points();
						$temp_fleet_cache[] = $fleet;	
						if ($fleet->get_order()){
							$temp_fleets_with_orders [] = $fleet;
						}
					}
				}
			}
			if ($sector->system && $sector->system->owner){				
				$temp_systems[] = $sector->system;				
				$sector->system->upkeep();
				if($sector->system->docked_fleet){
					$temp_fleet_cache[] = $sector->system->docked_fleet;
					if ($sector->system->docked_fleet->get_order()){
						$temp_fleets_with_orders [] = $sector->system->docked_fleet;
					}
				}							
			}
		}
		
		
		// ORDERS 
		
		while (count($temp_fleets_with_orders) > 0){
			
			// check and resolve any moves which cause battles 
			$shooty = [];
			$hitty = [];
			$collisions = [];
			$fleets_to_check = [];
			foreach($temp_fleets_with_orders as $fleet){
				// for now lets go with one fleet per sector and see what happens
				
				// check if any move orders would start a battle or collide
				$order = $fleet->get_order();
				if ($order && $order->type == "move" && $fleet->movement_points > 0){
					$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
					$sector2 = $this->map->get_sector($order->x, $order->z);
					if ($sector1 && $sector2){												
						// check if an enemy is there to attack
						$enemy_fleet = $sector2->get_fleet();
						if ($enemy_fleet){
							$shooty[] = ["shooter"=>$fleet, "target"=>$enemy_fleet];
							if (!isset($hitty[$enemy_fleet->id])){
								$hitty[$enemy_fleet->id] = [];
							}
							$hitty[$enemy_fleet->id][] = $fleet;
							$fleets_to_check[] = $fleet;
							$fleets_to_check[] = $enemy_fleet;
							$fleet->reset_orders();
						}
						$collisions[$sector2->id][] = $fleet;						
					}
				}				
			}												
			
			// do damage to whatever you target
			foreach($shooty as $shoot){
				$shoot['shooter']->fire($shoot['target']);				
				// if ship was told to shoot. ignore retaliation.
				unset($hitty[$shoot['shooter']->id]);
				$sector1 = $this->map->get_sector($shoot['shooter']->x, $shoot['shooter']->z);
				$sector2 = $this->map->get_sector($shoot['target']->x, $shoot['target']->z);
				$this->players[$shoot['shooter']->owner->id]->combat_zones[$sector1->id] = $sector1->get_direction($sector2, $this);	
				$this->players[$shoot['target']->owner->id]->combat_zones[$sector2->id] = $sector2->get_direction($sector1, $this);
			}
			
			// retaliate against ships which attacked you
			// currently use full strength against all
			foreach($hitty as $fleet_id => $fleet){
				foreach($fleet as $target){
					$this->fleets[$fleet_id]->fire($target);
				}
			}
			
			foreach($fleets_to_check as $fleet){
				if ($fleet->clean_up()){
					$this->map->get_sector($fleet->x, $fleet->z)->clean_up();
					$fleet->reset_orders();
				}
			}
									
			foreach($collisions as $sector_id => $fleets){
				if (count($fleets) > 1){
					foreach($fleets as $fleet){
						VOID_LOG::write($fleet->owner, "[movement] Fleet collided with another fleet");
						$fleet->reset_orders();	
					}
				}				
			}
			
			foreach($temp_fleets_with_orders as $fleet){
				// do the actual fleet moves
				$order = $fleet->get_order();
				if ($order && $order->type == "move" && $fleet->movement_points > 0){					
					$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
					$sector2 = $this->map->get_sector($order->x, $order->z);
					if ($sector1 && $sector2){
						if ($fleet->movement_points >= $sector2->movement_cost){								
							$fleet->movement_points = $fleet->movement_points - $sector2->movement_cost;
							if ($fleet->move($order->x, $order->z, $this)){
								$fleet->complete_order();
							}else {
								$fleet->reset_orders();
								$fleet->movement_points = 0;	
							}
						}else {
							$fleet->movement_points = 0;							
							if ($sector2->movement_cost > $fleet->movement_capacity){								
								$fleet->reset_orders();
							}
						}
					}
					//VOID_LOG::write($fleet->owner, "Fleet moved");
				}else if ($order && $order->type == "colonise"){
					$sector = $this->map->get_sector($fleet->x, $fleet->z);
					if ($sector->system && $fleet->get_special("colony")){							
						$sector->system->colonise($this->players[$fleet->owner->id], $this, $order->planet_id);							
						$fleet->movement_points = 0;
						$sector->system->resolve();
						$sector->system->update();							
						if ($fleet->remove_special("colony")){
							// delete the fleet!								
							$sector->clean_up();
						}							
						//$sector->system->owner = $fleet->owner;
					}
					$fleet->complete_order();
				}else if ($order && $order->type == "transfer"){						
					$from_fleet = $fleet;
					$to_fleet = $this->fleets[$order->fleet_id];
					$from_fleet->transfer_ship($order->ship_id, $to_fleet);
					$fleet->complete_order();
					$from_fleet->movement_points = 0;
					$to_fleet->movement_points = 0;
				}else if ($order && $order->type == "siege"){						
					$sector = $this->map->get_sector($fleet->x, $fleet->z);
					if ($sector->system){
						$system = $sector->system;
						if (!$fleet->siege($system)){
							$fleet->complete_order();
						}
					}else {
						$fleet->complete_order();
					}
					$fleet->movement_points = 0;
				}else if ($order && $order->type == "construct"){
					$sector = $this->map->get_sector($fleet->x, $fleet->z);
					if ($fleet->get_special("construct") && $order->upgrade_id){							
						$upgrade = $this->upgrade_classes[$order->upgrade_id];
						if ($upgrade->requirements_met($sector)){								
							$sector->add_upgrade($upgrade);							
							$fleet->movement_points = 0;														
							if ($fleet->remove_special("construct")){
								// delete the fleet!								
								$sector->clean_up();
							}
						}											
					}
					$fleet->complete_order();
				}else {
					$fleet->movement_points = 0;
					//$fleet->complete_order();
				}	
			}
			
			// if fleet moved next to enemy fleet, prevent all further movement
			foreach($temp_fleets_with_orders as $fleet){
				$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
				if ($sector1 && $sector1->is_enemy_adjacent($fleet->owner->id, $this)){
					$fleet->movement_points = 0;
					VOID_LOG::write($fleet->owner->id, "[movement] Fleet stopped due to enemy contact");
				}
			}
			
			// reload the temp fleet variable with fleets if they have more orders
			$new_fleets_with_orders = [];
			foreach($temp_fleets_with_orders as $fleet){
				$sector1 = $this->map->get_sector($fleet->x, $fleet->z);
				if ($fleet->movement_points == 0 || !$fleet->get_order()){
					
				}else {
					$new_fleets_with_orders[] = $fleet;
				}
				
			}
			
			$temp_fleets_with_orders = $new_fleets_with_orders;
			
			// if 2 friendly ships have conflict, stack them
			
			// any fleets which haven't attacked yet, will do damage to fleet in same sector
			
			
		}
		
		

		
		// resolve player orders (such as declare war)
		foreach($this->players as $player){			
			foreach($player->get_orders_by_type("war") as $order){				
				if (isset($order['player_id']) && isset($this->players[$order['player_id']]) ){
					$player->declare_war($this->players[$order['player_id']]);
				}
			}
			$player->clear_orders();
		}
		
		foreach($temp_systems as $system){
			$system->process_orders($this);
		}
		
		foreach($this->players as $player){
			$player->resolve();
		}
		
		// resolve building orders and growth
		foreach($temp_systems as $system){
			$system->resolve();
		}
		
		$this->map->update_map($this);
		foreach($temp_fleet_cache as $fleet){
			$fleet->resolve($this);
		}		
		
		
		foreach($this->players as $player){
			$player->update();
		}
		foreach($temp_systems as $system){
			$system->update();
		}
		foreach($temp_fleet_cache as $fleet){
			if (!$fleet->docked){
				$this->map->get_sector($fleet->x, $fleet->z)->clean_up();
			}
			$fleet->update($this);
		}		
		
		
		foreach($temp_systems as $system){
			$system->apply();
		}
		foreach($this->players as $player){
			$player->apply();
		}
		
		//check for victory and update victory totals	

		
		$this->reset_player_state();
		//$this->save();
	}
	
	public function handle_input($input, $player_id, $user){
		if (isset($input['action'])){
			if ($player_id && isset($this->players[$player_id])){
				$player = $this->players[$player_id];
			}else if ($player_id && isset($this->lobby->players[$player_id])) {
				$player = $this->lobby->players[$player_id];
			}
						
			if ($input['action'] == "end_turn"){
				
				if (isset($_POST['player_orders'])){
					$player_orders = $_POST['player_orders'];
					foreach($player_orders as $key => $order){
						if ($order['type'] == "war"){
							$player->add_order("war", ["player_id"=>$order['target']]);
						}
					}
					
				}
				
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
								}else if ($order['type'] == "siege"){									
									$this->fleets[$key]->add_order("siege", array("x"=>$order['x'], "z"=>$order['z']));
								}else if ($order['type'] == "construct"){									
									$this->fleets[$key]->add_order("construct", array("x"=>$order['x'], "z"=>$order['z'], "upgrade_id" => $order['upgrade_id']));
								}
							}
						}
					}
				}
				
				if (isset($_POST['fleet_transfers'])){
					$fleet_transfers = $_POST['fleet_transfers'];
					foreach($fleet_transfers as $key => $transfer){
						// reset all orders again. no transfer and orders allowed
						$this->fleets[$key]->reset_orders();
						if ($transfer['fleet_id']){
							$this->fleets[$transfer['fleet_id']]->reset_orders();
							if ($transfer['ship_id']){
								$this->fleets[$key]->add_order("transfer", array("ship_id"=>$transfer['ship_id'], "fleet_id"=>$transfer['fleet_id']));								
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
							foreach($orders as $order_key => $order){
								$queue_item = $this->systems[$key]->build_queue->get($order_key);
								if ($queue_item){
									// updating an existing item
									if (isset($order['purchased']) && $order['purchased']){
										$queue_item->purchase($this->systems[$key]->owner);
									}
								}else {
									// adding a new one
									if ($order['type'] == "ship"){
										$target = $this->ship_classes[$order['target_id']];
									}else if ($order['type'] == "structure"){
										$target = $this->structure_classes[$order['target_id']];
									}
									
									$queue_item = $this->systems[$key]->add_order($order['type'], $target);
									if (isset($order['purchased']) && $order['purchased']){
										$queue_item->purchase($this->systems[$key]->owner);
									}
								}
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
			} else if ($input['action'] == "join_game"){
				$player = $this->join_game($user);
				return $player->id;
			} else if ($input['action'] == "select"){				
				$race = $this->races[$input['race_id']];
				$empire = $this->empires[$input['empire_id']];
				$leader = $this->leaders[$input['leader_id']];
				$this->lobby->select_race($player_id, $race, $empire, $leader);
			} else if ($input['action'] == "color"){
				global $player_colors;
				//$this->lobby->set_color($player_id, $input['color_id']);				
			} else if ($input['action'] == "reset_lobby"){
				$user->reset_game($this->game_id);
				$this->setup();
				$this->lobby->reset();				
			} else if ($input['action'] == "start"){				
				$user->start_game($this->game_id);
				$this->start_game();
			} else if ($input['action'] == "reset"){
				//$this->map = new VOID_MAP();
				$this->init();
				//$this->setup(30,20);
				//$this->save();
			}
		}
	}
	
}


/*
		// other orders
		
		// intercept
		// will attack any enemy fleet that moves into or through an adjacent hex. attack damage distributed over all fleets if several targets
		
		// defend 
		// apply an attack and defense modifier to adjacent fleets
		
		// attack only order
		// attack the given sector and fleet. If the fleet moves, it gets attacked before moving. the fleet does not move.
		// similar to intercept but more specific. 
		
		// move
		// moves one space at a time towards the destination
		// if moves to a space adjacent to an enemy, move ends. always gets one movement.
		
		// if two fleets try to move onto each other, neither moves and battle occurs.
		
		// if for some reasons 2 FRIENDLY fleets are forced to exist in the same space, one is assumed to be in transit, and is unable to act until the other 
		// fleet has moved or is destroyed. It gets attacked freely. 
		
		// ****************  !!!!!!!!!!!!!!!**********************
		// if territory expands around a fleet that should not be there, it will move to the nearest space it can (or maybe the owner has to move it)		
		// if open borders ends, and fleets are where they should not be. DO NOTHING. War is declared if fleets are left there
		
		
		// first compile list of combat moves
		
		// resolve combat
		
		// move ships that have no conflict
		
		// enemy + friendly conflict = battle zone
		
		// friendly + friendly conflict = one fleet is in "statis"
		
		// combat + fleet orders
		
		// fleet transfers happen first
		// cancel all other orders of fleets which perform transfers

		// if two fleets cross paths resolve a battle
		// if two fleets try to move to the same space, resolve a battle
		// if more than two fleets trigger this, pick 2 opposing ones randomly
		// first fleet to move into space cancels other moves 
		
		// loop until all fleets have used up all their movement capacity
		$continue = true;
		while ($continue && false){
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
								$fleet->complete_order();
							}else {
								$fleet->movement_points = 0;
								if ($sector2->movement_cost > $fleet->movement_capacity){
									$fleet->complete_order();
									$fleet->reset_orders();									
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
						$fleet->complete_order();
					}else if ($order->type == "transfer"){						
						$from_fleet = $fleet;
						$to_fleet = $this->fleets[$order->fleet_id];
						$from_fleet->transfer_ship($order->ship_id, $to_fleet);
						$fleet->complete_order();
						$continue = true;	
					}else if ($order->type == "construct"){
						$sector = $this->map->get_sector($fleet->x, $fleet->z);						
						if ($fleet->get_special("construct") && $order->upgrade_id){							
							$upgrade = $this->upgrade_classes[$order->upgrade_id];
							if ($upgrade->requirements_met($sector)){								
								$sector->add_upgrade($upgrade);							
								$fleet->movement_points = 0;														
								if ($fleet->remove_special("construct")){
									// delete the fleet!								
									$sector->clean_up();
								}
							}											
						}
						$fleet->complete_order();
					}else {
						$fleet->movement_points = 0;						
					}
				}
			}
			
			// resolve combat
			foreach($combat_sectors as $sector){
				$sector->resolve_combat();
			}
			$combat_sectors = [];
		}

*/

?>