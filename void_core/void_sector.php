<?

class VOID_UPGRADE_CLASS {
	public $name;
	public $id;
	
	// list of requirements to build it. such as a nebula
	public $requirements;
	
	public $modifiers;
	
	function __construct(){
		
	}
	
	function get_modifier($type){
		if (isset($this->modifiers[$type])){
			return $this->modifiers[$type];
		}
	}
	function set_modifier($type, $value){
		//possibly handle custom function handlers for unique effects?
		$this->modifiers[$type] = $value;
	}
	
	function add_requirement($req){
		$this->requirements[$req] = $req;
	}
	
	function requirements_met($sector){
		if (isset($this->requirements[$sector->class['id']])){
			return true;
		}
		return false;
	}
}



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
	public $id;
	
	public $type;
	public $type_id;
	public $movement_cost;
	public $owner;
	
	public $influence_level;
	public $your_influence;
	
	public $populated;
	
	public $class_id;
	
	public $upgrade_id;
	
	public $ruin;
	
	//public $space_dock;
	
	function __construct($sector, $player){
		$player_id = $player->id;
		$this->name = $sector->name;
		$this->unknown = $sector->unknown;
		$this->x = $sector->x;
		$this->z = $sector->z;
		$this->movement_cost = 2;
		$this->id = $sector->id;
		if (isset($sector->state[$player_id])){
			
			if ($sector->upgrade){
				$this->upgrade_id = $sector->upgrade->id;
			}else {
				$this->upgrade_id = false;
			}
			
			$this->movement_cost = $sector->class['movement_cost'];
			$this->class_id = $sector->class['id'];
			if ($sector->owner){
				$this->owner = $sector->owner->id;
			}
			$this->type = $sector->type;
			$this->type_id = $sector->class['id'];
			if ($sector->system){
				$this->system = $sector->system->dump($player);
			}
			//$this->fleets = $sector->fleets;
			$this->star = $sector->star;
			$this->friendly = $sector->friendly;
			$this->enemy = $sector->enemy;
			if ($sector->owner && $sector->owner->id == $player_id){
				$this->friendly = 1;
			}else {
				$this->friendly = 0;
			}
			if ($sector->owner && $sector->owner->id != $player_id){
				$this->enemy = 1;
			}else {
				$this->enemy = 0;
			}
			
			foreach($sector->fleets as $key => $player_fleets){
				foreach($player_fleets as $key => $fleet){
					if ($fleet->owner->id == $player_id){
						$this->your_fleets[] = $fleet->dump_view($player_id);
					}else {
						$this->enemy_fleets[] = $fleet->dump_view($player_id);
					}
				}
			}
			
			
			if ($sector->owner && $sector->owner->id == $player_id){
				if (isset($sector->system)){
					$this->populated = true;
					$this->space_dock = array();
					$this->space_dock['capacity'] = 10;
					if ($sector->system && $sector->system->docked_fleet){
						$this->space_dock['fleet'] = $sector->system->docked_fleet->dump_view($player_id);
					}
				}
				
			}
			
			if ($sector->ruin){
				$this->ruin = $sector->ruin;
			}
		}else if (isset($sector->fog_state[$player_id])){
			// return the state as fog of war saw it
			$this->fog = 1;
			if (isset($sector->fog_state[$player_id]['owner'])){
				$this->owner = $sector->fog_state[$player_id]['owner']->id;
			}else {
				$this->owner = "";
			}

			$this->star = $sector->star;
			$this->type = $sector->type;
			$this->class_id = $sector->class['id'];
			$this->movement_cost = $sector->class['movement_cost'];
			//$this = $sector->fog_state[$player_id]['view'];
		}else {
			$this->unknown = 1;
		}
	}
}

class VOID_SECTOR {
	public $name = "";
	
	public $id;
	
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
	public $upgrade;
	public $ruin;
	
	
	function __construct($x, $z){
		$this->x = $x;
		$this->z = $z;
		$this->id = "x".$x."z".$z;
		// default type is "space" ie. empty
		$this->type = "space";
		$this->home = 0;
		$this->fog_state = [];
		$this->set_type(1);
		$this->upgrade = false;
		$this->ruin = false;
	}
	
	public function add_upgrade($upgrade){
		$this->upgrade = $upgrade;		
	}
	
	
	public function add_ruin($type=""){
		$ruins = [
			["name"=>"Desolate Ancient Cruiser", "effect"=>"research"],
			["name"=>"Desolate Ancient Cruiser", "effect"=>"credits"]
		];
		if ($type){
			
		}else {
			// add a random ruin
			$this->ruin = $ruins[array_rand($ruins)];
		}
	}
	
	public function resolve_ruin($core, $object){
		if ($this->ruin){
			
			if ($this->ruin['effect'] == "research"){
				// grant the player a research boost 
				if ($object->current_tech){
					$object->current_tech->progress = $object->current_tech->progress - 50;
					if ($object->current_tech->progress <= 0){
						$object->current_tech->progress = 1;
					}
					VOID_LOG::write($object->id, "[explore] Analysing the derelict Ancient ship has revealed technological secrets");
				}
			}else if ($this->ruin['effect'] == "credits"){
				// grant the player a credits boost 
				$object->credits->pool += 50;				
				VOID_LOG::write($object->id, "[explore] The derelict Ancient ship was beyond repair but full of raw materials to sell on the black market");
			}
		}
		$this->ruin = false;
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
			foreach($this->fleets[$player_id] as $fleet){
				if ($fleet->in_transit == false){
					return $fleet;
				}
			}
		}
		return false;
	}
	
	
	public function get_direction($target_sector, $core){
		global $void_hex_adjacent;						
		foreach($void_hex_adjacent as $key => $range){
			$x = $this->x + $range[0];
			$z = $this->z + $range[1];
			$sector = $core->map->get_sector($x,$z);
			if ($sector && $sector->x == $target_sector->x && $sector->z == $target_sector->z){
				return $key;
			}
		}
		return false;
	}
	
	public function get_adjacent_sectors($core){
		global $void_ranges;				
		$ajacent_sectors = [];
		foreach($void_ranges[1] as $range){
			$x = $this->x + $range['x'];
			$z = $this->z + $range['z'];
			$sector = $core->map->get_sector($x,$z);
			if ($sector){
				$adjacent_sectors[] = $sector;
			}
		}
		return $adjacent_sectors;
	}
	
	public function is_enemy_adjacent($player_id, $core){
		$adj = $this->get_adjacent_sectors($core);
		foreach($adj as $sector){
			$enemy_fleets = $sector->get_enemy_fleets($player_id);
			if ($enemy_fleets){
				return true;
			}
		}
		return false;
	}
	
	
	public function get_fleet($id="", $player_id=""){
		if ($id && $player_id){
			if (isset($this->fleets[$player_id])){
				foreach($this->fleets[$player_id] as $fleet){
					if ($fleet->id == $id){
						return $fleet;
					}
				}
			}
			return false;
		}else {
			if (count($this->fleets) > 0){
				$temp_player = reset($this->fleets);
				return reset($temp_player);
			}else {
				return false;
			}
		}
	}
	
	public function get_fleets($player_id){
		if (isset($this->fleets[$player_id])){
			return $this->fleets[$player_id];
		}
		return false;
	}
	
	public function get_fleet_owners(){
		$return = [];
		foreach ($this->fleets as $player_id => $player_fleets){
			$return[] = $player_id;
		}
		return $return;
	}
	
	
	
	
	public function get_enemy_fleets($player_id){
		$fleets = array();
		foreach ($this->fleets as $fleet_owner_id => $player_fleets){
			if ($fleet_owner_id != $player_id){
				foreach ($player_fleets as $fleet_id => $fleet){
					if (!$fleet->in_transit){
						$fleets[] = $fleet;
					}
				}
			}
		}
		if (count($fleets) <= 0){
			return false;
		}
		return $fleets;
	}
	
	
	public function get_combat_fleets(){
		$fleets = array();
		foreach ($this->fleets as $player_id => $player_fleets){
			foreach ($player_fleets as $fleet_id => $fleet){
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
		// need to add an "in-lieu" state to buffer fleets.
		// once movement is over, move fleets into the correct sector
		
		if (count($this->fleets) > 0){
			if (isset($this->fleets['lieu'])){
				throw new Exception("CANNOT HAVE 2 FLEETS IN LIEU");
			}
			$this->fleets['lieu'] = $fleet;
		}else {
			$this->fleets[$fleet->owner->id][$fleet->id] = $fleet;
		}
		$fleet->x = $this->x;
		$fleet->z = $this->z;
		
		if (isset($this->fleets[$fleet->owner->id]) && count($this->fleets[$fleet->owner->id]) > 0){
			//$fleet->in_transit = true;
		}
		
		return true;
	}
	
	public function resolve_lieu(){
		// move fleets from limbo into this sector
		if (isset($this->fleets['lieu'])){
			$this->fleets[$this->fleets['lieu']->owner->id][$this->fleets['lieu']->id] = $this->fleets['lieu'];
			unset($this->fleets['lieu']);
		}
	}
	
	public function remove_fleet($fleet){
		
		//VOID_LOG::write($fleet->owner, print_r($this->fleets, 1) );
		unset($this->fleets[$fleet->owner->id][$fleet->id]);
		if (count($this->fleets[$fleet->owner->id]) <= 0){
			unset($this->fleets[$fleet->owner->id]);
		}
		//VOID_LOG::write($fleet->owner, print_r($this->fleets, 1) );
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
			$combat = new VOID_COMBAT($fleets, $this);
			$combat->resolve();
			$this->clean_up();
		}
	}
	
	public function clean_up(){
		foreach($this->fleets as $player_id => $fleets){
			foreach($fleets as $key => $fleet){
				if (count($fleet->ships) <= 0 && !$fleet->docked){
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
		if ($property == "influence"){
			if ($this->state[$player_id]->$property){
				if ($value >= $this->state[$player_id]->$property){
					$this->state[$player_id]->$property = $value;
				}
			}else {
				$this->state[$player_id]->$property = $value;
			}
		}else {
			$this->state[$player_id]->$property = $value;
		}		
	}
	
	public function get_vision(){
		$player_ids = [];
		foreach($this->state as $key => $state){
			if ($state->sensor_power){
				$player_ids[] = $key;
			}
		}
		return $player_ids;
	}
	
	// dump the sector data as an array for output to the client
	// dump from the perspective of a specific player
	public function dump_sector($player){
		$player_id = $player->id;
		if (!isset($this->state[$player_id]) && isset($this->fog_state[$player_id]) && isset($this->fog_state[$player_id]['view']) ){
			$fog = $this->fog_state[$player_id]['view'];
			$fog->fog = true;
			$fog->your_fleets = [];
			$fog->enemy_fleets = [];
			return $fog;
		} else {
			$view = new VOID_SECTOR_VIEW($this, $player);
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
		$this->movement_cost = $void_sector_classes[$class_id]['movement_cost'];
	}
	
	public function set_system_owner($player){
		$this->system->owner = $player;
	}
	
	public function update_owner($core){
		if (isset($this->system->owner) && $this->system->owner){
			$this->owner->sector_count++;
			$this->owner = $this->system->owner;
			return true;
		}
		$current_highest = false;
		
		// wtf error here, undefined index, impossible??
		if ($this->owner && isset($this->state[$this->owner->id])){
			$highest_influence = $this->state[$this->owner->id]->influence;
		}else {
			$highest_influence = 1;
		}
		foreach($this->state as $player_id => $state){
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
			$this->owner = $core->players[$current_highest];			
		}
		if ($this->owner){
			$this->owner->sector_count++;
		}
		//echo "\n";
	}
	
	public function update_system(){
		if ($this->system){
			$this->system->update(); 
		}
	}
	
	public function update_fog($core){
		foreach($this->state as $player_id => $state){
			$this->fog_state[$player_id]['owner'] = $this->owner;
			$this->fog_state[$player_id]['view'] = $this->dump_sector($core->players[$player_id]);
		}
	}
	
}


class VOID_COMBAT {
	public $fleets;
	
	public $ship_index;
	public $target_index;
	
	public $sector;
	
	function __construct($fleets, $sector){
		$this->fleets = $fleets;
		$this->sector = $sector;
		$players = [];
		foreach($this->fleets as $fleet){
			$players[$fleet->owner] = $fleet->owner;
		}
		
		foreach($players as $player){
			VOID_LOG::write($player, "[combat] Combat occured in (".$sector->x.",".$sector->z.")");
		}
		
		foreach($this->fleets as $fleet){
			foreach($fleet->ships as $ship){
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
			if ($fleet->clean_up()){
				VOID_LOG::write($fleet->owner, "[combat] A fleet was lost at (".$this->sector->x.",".$this->sector->z.")");
			}
		}
		
		
	}
	
}



?>