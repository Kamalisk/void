<?

class VOID_SECTOR_STATE {
	public $visible = false;
	public $influence = 0;
	public $sensor_power = 0;
}

class VOID_SECTOR_VIEW {
	public $name = "";
	public $unknown = 0;
	public $fog = 0;
	
	
	
	public $your_fleets = array();
	public $enemy_fleets = array();
	public $allied_fleets = array();
	
	public $star = 0;
	public $friendly = 0;
	public $enemy = 0;
	
	public $system;
	
	public $move_cost = 1;
	
	public $x;
	public $z;
	
	public $type;
	public $movement_cost;
	public $owner;
	
	public $influence_level;
	public $your_influence;
	
	public $populated;
	
	public $class_id;
	
	//public $space_dock;
	
	function __construct($sector, $player_id){
		$this->name = $sector->name;
		$this->unknown = $sector->unknown;
		$this->x = $sector->x;
		$this->z = $sector->z;
		
		if (isset($sector->state[$player_id])){
			$this->class_id = $sector->class['id'];
			$this->owner = $sector->owner;
			$this->type = $sector->type;
			if ($sector->system){
				$this->system = $sector->system->dump($player_id);
			}
			//$this->fleets = $sector->fleets;
			$this->star = $sector->star;
			$this->friendly = $sector->friendly;
			$this->enemy = $sector->enemy;
			if ($sector->owner && $sector->owner == $player_id){
				$this->friendly = 1;
			}else {
				$this->friendly = 0;
			}
			if ($sector->owner && $sector->owner != $player_id){
				$this->enemy = 1;
			}else {
				$this->enemy = 0;
			}
			
			foreach($sector->fleets as $key => &$player_fleets){
				foreach($player_fleets as $key => &$fleet){
					if ($fleet->owner == $player_id){
						$this->your_fleets[] = $fleet->dump_view($player_id);
					}else {
						$this->enemy_fleets[] = $fleet->dump_view($player_id);
					}
				}
			}
			
			
			if ($sector->owner && $sector->owner == $player_id){
				if (isset($sector->system)){
					$this->populated = true;
					$this->space_dock = array();
					$this->space_dock['capacity'] = 10;
					if ($sector->system && $sector->system->docked_fleet){
						$this->space_dock['fleet'] = $sector->system->docked_fleet->dump_view($player_id);
					}
				}
				
			}
		}else if (isset($sector->fog_state[$player_id])){
			// return the state as fog of war saw it
			$this->fog = 1;
			$this->owner = $sector->fog_state[$player_id]['owner'];
			$this->star = $sector->star;
			$this->type = $sector->type;
			$this->class_id = $sector->class['id'];
			//$this = $sector->fog_state[$player_id]['view'];
		}else {
			$this->unknown = 1;
		}
	}
}

class VOID_SECTOR {
	public $name = "";
	
	public $fleets = array();
	public $unknown = 0;
	public $star = 0;
	public $friendly = 0;
	public $enemy = 0;
	public $x;
	public $z;
	public $state; // contains the state of the sector including various player data
	public $fog_state; // contains the state as they last saw it 
	public $owner;
	
	public $system;
	
	public $system_owner; // owner of the planets in the system
	public $type;
	public $movement_cost;
	
	public $home;
	
	public $class;
	
	function __construct($x, $z){
		$this->x = $x;
		$this->z = $z;
		// default type is "space" ie. empty
		$this->type = "space";
		$this->home = 0;
		$this->fog_state = [];
		
	}
	
	// adds a planet to the system
	// $planet = a void_planet object
	public function add_planet($planet){
		
		if (!$this->system){
			$this->system = new VOID_SYSTEM($this->x, $this->z);
		}
		$this->system->add_planet($planet);		
	}
	
	public function add_ship($ship){
		if (!isset($this->fleets[$ship->owner])){
			$fleet = new VOID_FLEET();
			$fleet->x = $this->x;
			$fleet->z = $this->z;
			$this->fleets[$ship->owner][$fleet->id] = $fleet;			
		}
		$key = array_rand($this->fleets[$ship->owner]);
		$this->fleets[$ship->owner][$key]->add_ship($ship);
	}
	
	public function get_primary_fleet($player_id){
		if (isset($this->fleets[$player_id])){
			foreach($this->fleets[$player_id] as &$fleet){
				if ($fleet->in_transit == false){
					return $fleet;
				}
			}
		}
		return false;
	}
	
	
	public function get_fleet($id, $player_id){
		if (isset($this->fleets[$player_id])){
			foreach($this->fleets[$player_id] as &$fleet){
				if ($fleet->id == $id){
					return $fleet;
				}
			}
		}
		return false;
	}
	
	public function get_fleets($player_id){
		if (isset($this->fleets[$player_id])){
			return $this->fleets[$player_id];
		}
		return false;
	}
	
	public function get_combat_fleets(){
		$fleets = array();
		foreach ($this->fleets as $player_id => &$player_fleets){
			foreach ($player_fleets as $fleet_id => &$fleet){
				if (!$fleet->in_transit){
					$fleets[] = $fleet;
				}
			}
		}
		if (count($fleets) < 2){
			return false;
		}
		return $fleets;
	}
	
	public function add_fleet($fleet){
		$fleet->x = $this->x;
		$fleet->z = $this->z;
		if (isset($this->fleets[$fleet->owner]) && count($this->fleets[$fleet->owner]) > 0){
			$fleet->in_transit = true;
		}
		$this->fleets[$fleet->owner][$fleet->id] = $fleet;
	}
	public function remove_fleet($fleet){
		unset($this->fleets[$fleet->owner][$fleet->id]);
		if (count($this->fleets[$fleet->owner]) <= 0){
			unset($this->fleets[$fleet->owner]);
		}
	}
	
	
	public function add_fleet_order($player_id, $x, $z){
		if ($this->fleets[$player_id]){
			$this->fleets[$player_id]->add_order("move", array('x'=>$x, 'z'=>$z));
		}
	}
	
	public function resolve_combat(){
		//first resolve all in-transit shots
		
		// then resolve combat between any not in-transit fleets
		$fleets = $this->get_combat_fleets();
		if ($fleets){
			$combat = new VOID_COMBAT($fleets);
			$combat->resolve();
			$this->clean_up();
		}
	}
	
	public function clean_up(){
		foreach($this->fleets as $player_id => $fleets){
			foreach($fleets as $key => $fleet){
				if (count($fleet->ships) <= 0){
					unset($this->fleets[$player_id][$key]);
					if (count($this->fleets[$player_id]) <= 0){
						unset($this->fleets[$player_id]);
					}
				}
			}
		}
	}
	
	
	public function reset_state(){
		$this->state = [];
		
	}
	
	public function add_state($player_id, $property, $value){
		if (!isset($this->state[$player_id])){
			$this->state[$player_id] = new VOID_SECTOR_STATE();
		}
		$this->state[$player_id]->$property = $value;
	}
	
	// dump the sector data as an array for output to the client
	// dump from the perspective of a specific player
	public function dump_sector($player_id){
		if (false && isset($this->fog_state[$player_id]) && $this->fog_state[$player_id]['view']){
			return $this->fog_state[$player_id]['view'];
		} else {
			$view = new VOID_SECTOR_VIEW($this, $player_id);
			return $view;
		}
	}
	
	public function get_neighbours(){
		$hex_neighbours = array(
			array($this->x+1,  $this->z+0),  array($this->x+1, $this->z-1),  array( $this->x+0, $this->z-1),
			array($this->x-1,  $this->z+0),  array($this->x-1, $this->z+1),  array( $this->x+0, $this->z+1) 
		);
		return $hex_neighbours;
	}
	
	public function set_type($class_id){
		global $void_sector_classes;
		$this->class = $void_sector_classes[$class_id];
		$this->type = $this->class['type'];
		
		global $void_sector_types;
		//if (isset($void_sector_types[$type])){
			$this->movement_cost = 1;//$void_sector_types[$type]['movement_cost'];
		//}
	}
	
	public function set_system_owner($player_id){
		$this->system->owner = $player_id;
	}
	
	public function update_owner(){
		if (isset($this->system->owner)){
			$this->owner = $this->system->owner;
			return true;
		}
		$current_highest = false;
		if ($this->owner && $this->state[$this->owner]){
			$highest_influence = $this->state[$this->owner]->influence;
		}else {
			$highest_influence = 1;
		}
		foreach($this->state as $player_id => &$state){
			if ($state->influence > $highest_influence && ($state->influence / $highest_influence) > 1.5){
				$current_highest = $player_id;
				$highest_influence = $state->influence;
			}else if ($state->influence && $highest_influence && ( $highest_influence / $state->influence) < 1.5){
				$current_highest = false;
			}
		}
		//echo " -- ".$this->owner."  (".$this->x.", ".$this->z.") ";
		if ($current_highest){
			//echo "wtf ".$current_highest." ---";
			$this->owner = $current_highest;
		}
		//echo "\n";
	}
	
	public function update_system(){
		if ($this->system){
			$this->system->update(); 
		}
	}
	
	public function update_fog(){
		foreach($this->state as $player_id => &$state){
			$this->fog_state[$player_id]['owner'] = $this->owner;
			$this->fog_state[$player_id]['view'] = $this->dump_sector($player_id);
		}
	}
	
}


class VOID_COMBAT {
	public $fleets;
	
	public $ship_index;
	public $target_index;
	
	function __construct($fleets){
		$this->fleets = $fleets;
		
		$players = [];
		foreach($this->fleets as &$fleet){
			$players[$fleet->owner] = $fleet->owner;
		}
		
		foreach($players as $player){
			VOID_LOG::write($player, "Combat occured in sector X");
		}
		
		foreach($this->fleets as &$fleet){
			foreach($fleet->ships as &$ship){
				$this->ship_index[$ship->id] = $ship;
				foreach($players as $player_id){
					if ($player_id != $ship->owner){
						$this->target_index[$player_id][] =& $ship;
					}
				}
			}
		}
		
		
		
	}
	
	public function resolve(){
		foreach($this->ship_index as $ship){
			$array_index = array_rand($this->target_index[$ship->owner]);
			$ship->fire($this->target_index[$ship->owner][$array_index]);
		}
		
		foreach($this->fleets as $fleet){
			$fleet->clean_up();
		}
		
		
	}
	
}



?>