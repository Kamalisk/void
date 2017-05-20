<?


class VOID_MAP_VIEW extends VOID_MAP {
	public $map_width;
	public $map_height;
	public $sectors;
	private $player_id;
	private $player;
	
	function __construct($map, $player){
		$this->player_id = $player->id;
		$this->player = $player;
		$this->map_width = $map->map_width;
		$this->map_height = $map->map_height;
	}
	
	public function update_sectors($sectors){
		foreach($sectors as &$sector){
			$this->sectors['x'.$sector->x.'z'.$sector->z] = $sector->dump_sector($this->player);
		}
	}
}


class VOID_MAP {
	public $sectors = array();
	public $map_width = 0;
	public $map_height = 0;
	
	
	
	private $views;
	
	public function setup(){
		
	}
	public function generate($width, $height, $core){
		global $void_planet_classes;
		global $void_system_names;
		shuffle($void_system_names);
		
		$this->map_width = $width;
		$this->map_height = $height;
		for ($z = 0; $z < $height; $z++){
			for ($x = -floor($z/2); $x < $width - floor($z/2); $x++){
				if (rand(1,25) < 2){					
					continue;
				}
				$this->sectors['x'.$x.'z'.$z] = new VOID_SECTOR($x, $z);
				$current_sector =& $this->sectors['x'.$x.'z'.$z];
				if (rand(1,10) < 4){
					$current_sector->star = 1;
					$current_sector->system = new VOID_SYSTEM($x, $z);
					$name = array_pop($void_system_names);					
					$current_sector->system->set_name($name);
					
					$core->systems[$current_sector->system->id] =& $current_sector->system;
					if (mt_rand(1,6) < 3){
						$planet = new VOID_PLANET();
						$planet->name = "Awesome";
						$planet->class = $void_planet_classes[1];
						$this->sectors['x'.$x.'z'.$z]->add_planet($planet);	
					}
					
					while (count($current_sector->system->planets) < rand(2,3)){
						$planet = new VOID_PLANET();
						$planet->name = "Awesome";
						$planet->class = $void_planet_classes[mt_rand(2,6)];
						$this->sectors['x'.$x.'z'.$z]->add_planet($planet);
					}
				}else if (rand(1,30) < 2){
					$current_sector->set_type(6);
					continue;
				}else {
					if (rand(1,30) < 3){
						$current_sector->add_ruin();
					}
				}
				
				$current_sector->set_type(1);
				
				if (rand(1,11) < 3){
					$current_sector->set_type(2);
				}else {
					if (rand(1,15) < 2){
						$current_sector->set_type(3);
					}else if (rand(1,15) < 2){
						$current_sector->set_type(4);
					} else if (rand(1,15) < 2){
						$current_sector->set_type(5);
					}
				}
			}
		}
		
	}
	public function populate($core){
		
		global $player_colors;
		$colors = $player_colors;
				
		foreach($core->players as $player){
			if (!$player->player){
				continue;
			}
			
			$core->neutral_player->declare_war($player);
			
			if ($player->color){
				unset($colors[$player->color['color_id']]);
			}else {
				$color = array_shift($colors);
				$player->set_color($color);
			}
			//$this->views[$player->id] = new VOID_VIEW();
			while(true){
				$key = array_rand($this->sectors,1);
				if ($this->sectors[$key]->star == 1 && !$this->sectors[$key]->home){
					
					break;
				}
			}
			
			if (!$player->race){
				$player->race = $core->races[array_rand($core->races)];	
			}
			
			if (!$player->leader){
				$player->leader = $player->race->leaders[array_rand($player->race->leaders)];
			}
			
			if (!$player->empire){
				$player->empire = $player->race->empires[array_rand($player->race->empires)];
			}
			//print_r($player->race);
			//$color = array_shift($colors);
			//$player->set_color($color);
			
			global $void_planet_classes;
			$planet = new VOID_PLANET();
			$planet->name = "Awesome";
			$planet->class = $void_planet_classes[1];
			$this->sectors[$key]->add_planet($planet);
			
			$this->sectors[$key]->home = $player->id;
			$player->home = array('x'=> $this->sectors[$key]->x, 'z'=>$this->sectors[$key]->z);
			
			$this->sectors[$key]->system->colonise($player, $core, $planet->id);
			$this->sectors[$key]->system->influence_level = 2;
			//$this->sectors[$key]->system->influence_per_turn = mt_rand(5,10);
			
			//$this->sectors[$key]->system->add_order();
			//$this->sectors[$key]->system->add_order();
			//$this->sectors[$key]->system->add_order();
			
			//$this->sectors[$key]->system->food_per_turn = mt_rand(1,10);
			
			//$player->credits_per_turn = $this->sectors[$key]->system->get_credits_income();
			//$player->research_per_turn = $this->sectors[$key]->system->get_research_income();

			// create new fleet
			$fleet = new VOID_FLEET();
			$core->fleets[$fleet->id] = $fleet; 
			// add a ship
			$ship = new VOID_SHIP($core->ship_classes[1], $player);
			$fleet->add_ship($ship);
			$this->sectors[$key]->add_fleet($fleet);
			$fleet->update($core);
			
			$structure = new VOID_STRUCTURE($core->structure_classes[1]);
			$this->sectors[$key]->system->add_structure($structure);
						
			$this->sectors[$key]->system->resolve();						
			$this->sectors[$key]->system->update();
			$this->sectors[$key]->system->apply();

		}
		
		foreach($core->players as $player){
			$player->apply_resource_modifiers();
		}
		
		$player = $core->neutral_player;		
		$color = array_shift($colors);
		$player->set_color($color);
		for ($i = 0; $i < count($core->players)*2; $i++){
			while(true){
				$key = array_rand($this->sectors,1);
				if ($this->sectors[$key]->star == 1 && !$this->sectors[$key]->home){				
					break;
				}
			}
			$this->sectors[$key]->home = $player->id;
			$player->home = array('x'=> $this->sectors[$key]->x, 'z'=>$this->sectors[$key]->z);
			$this->sectors[$key]->system->colonise($player, $core);						
				
			// create new fleet
			$fleet = new VOID_FLEET();
			$core->fleets[$fleet->id] = $fleet; 
			// add a ship
			$ship = new VOID_SHIP($core->ship_classes[1], $player);
			$fleet->add_ship($ship);
			$this->sectors[$key]->add_fleet($fleet);
			$fleet->update($core);
			
			$structure = new VOID_STRUCTURE($core->structure_classes[1]);
			$this->sectors[$key]->system->add_structure($structure);
						
			$this->sectors[$key]->system->resolve();						
			$this->sectors[$key]->system->update();			
		}
		
		$this->update_map($core);
		
	}
	
	// run through all objects on the map
	// calculate who controls each hex
	// update vision for all players
	public function update_map($core){
		global $void_ranges;
		// first reset the map state for all players
		foreach($this->sectors as &$sector){
			$sector->reset_state();
		}
		
		// run through each sector and apply 
		// sensor range and influence to all sectors
		foreach($this->sectors as &$sector){
			if ($sector->system && isset($sector->system->owner) && $sector->system->owner ){
				// you always "own" your home system
				
				$sector->owner = $sector->system->owner;				
				$sector->add_state($sector->system->owner->id, "friendly", 1);
				$sector->add_state($sector->system->owner->id, "unknown", 0);
				$sector->add_state($sector->system->owner->id, "influence", $sector->system->influence_level);
				$sector->add_state($sector->system->owner->id, "sensor_power", 1);
				
				$neighbours = $sector->get_neighbours();
				
				$influence_size = $sector->system->influence_level;
				
				for ($ring = 1; $ring <= $influence_size; $ring++){					
					foreach($void_ranges[$ring] as $range){
						$x = $sector->x + $range['x'];
						$z = $sector->z + $range['z'];
					
						if (isset($this->sectors['x'.$x.'z'.$z])){
							$n =& $this->sectors['x'.$x.'z'.$z];							
							$n->add_state($sector->system->owner->id, "sensor_power", 1);
							if ($ring < $influence_size ){
								$sector->system->add_influenced_sector($n);
								$n->add_state($sector->system->owner->id, "influence", $sector->system->influence_level/$ring);
							}
						}
						
					}
				}
				
				
			}

			$fleets =& $sector->fleets;
			if ($fleets){
				foreach($fleets as &$player_fleets){
					foreach($player_fleets as $fleet){
						$fleet_owner = $core->players[$fleet->owner->id];
						if ($fleet_owner->has_power("vision")){
							$vision_bonus = $fleet_owner->get_power_value("vision");
						}else {
							$vision_bonus = 0;
						}
						$sector->add_state($fleet->owner->id, "unknown", 0);
						for ($ring = 1; $ring <= $fleet->get_vision_range() + $vision_bonus; $ring++){
							
							foreach($void_ranges[$ring] as $range){
								$x = $sector->x + $range['x'];
								$z = $sector->z + $range['z'];
							
								if (isset($this->sectors['x'.$x.'z'.$z])){
									$n =& $this->sectors['x'.$x.'z'.$z];
									$n->add_state($fleet->owner->id, "sensor_power", 1);
									
								}
								
							}
						}
					}
				}
			}
		}
		
		// run through all sectors and calculate who "owns" each sector
		foreach($this->sectors as &$sector){			
			$sector->update_owner($core);			
			$sector->update_fog($core);
		}
		
		
		// update meeting of new players 
		foreach($this->sectors as $sector){			
			$player_ids = $sector->get_vision();
			foreach($player_ids as $player_id){
				if ($sector->owner){
					if ($core->players[$player_id]->add_met_player($sector->owner->id)){
						VOID_LOG::write($player_id, "[diplomacy] You have met ".$sector->owner->name);
					}
				}else if ($sector->get_fleet_owners()){
					foreach($sector->get_fleet_owners() as $owner_id){
						if ($core->players[$player_id]->add_met_player($owner_id)){
							VOID_LOG::write($player_id, "[diplomacy] You have met ".$core->players[$owner_id]->name);
						}
					} 
				}
			}
		}

		
	}
	
	
	
	
	/*
	// generate the "views" that each player sees
	public function generate_views($players){
		foreach($players as &$player){
			$this->generate_view($player);
		}
	}
	
	// generate a "view" for a player
	private function generate_view($player){
		//$this->views[$player->id] = new VOID_VIEW();
		//foreach($this->sectors as &$sector){
		//	$this->views[$player->id]->sectors[] = $sector;
		//}
	}
	*/
	
	public function get_sector($x, $z){
		if (isset($this->sectors['x'.$x.'z'.$z])){
			return $this->sectors['x'.$x.'z'.$z];
		}
		return false;
	}
	
	public function dump_map($player){
		$map_view = new VOID_MAP_VIEW($this, $player);
		$map_view->update_sectors($this->sectors);
		return $map_view;
	}
}




?>