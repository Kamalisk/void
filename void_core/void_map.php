<?


class VOID_MAP_VIEW extends VOID_MAP {
	public $map_width;
	public $map_height;
	public $sectors;
	private $player_id;
	
	function __construct($map, $player_id){
		$this->player_id = $player_id;
		$this->map_width = $map->map_width;
		$this->map_height = $map->map_height;
	}
	
	public function update_sectors($sectors){
		foreach($sectors as &$sector){
			$this->sectors['x'.$sector->x.'z'.$sector->z] = $sector->dump_sector($this->player_id);
		}
	}
}


class VOID_MAP {
	public $sectors = array();
	public $map_width = 0;
	public $map_height = 0;
	
	
	
	private $views;
	
	public function setup($width, $height){
		
	}
	public function generate($width, $height, $core){
		global $void_planet_classes;
		
		$this->map_width = $width;
		$this->map_height = $height;
		for ($z = 0; $z < $height; $z++){
			for ($x = -floor($z/2); $x < $width - floor($z/2); $x++){
				$this->sectors['x'.$x.'z'.$z] = new VOID_SECTOR($x, $z);
				$current_sector =& $this->sectors['x'.$x.'z'.$z];
				if (rand(1,10) < 3){
					$current_sector->star = 1;
					$current_sector->system = new VOID_SYSTEM($x, $z);
					$core->systems[$current_sector->system->id] =& $current_sector->system;
					while (count($current_sector->system->planets) < rand(3,6)){
						$planet = new VOID_PLANET();
						$planet->name = "Awesome";
						$planet->class = $void_planet_classes[mt_rand(1,5)];
						$this->sectors['x'.$x.'z'.$z]->add_planet($planet);
					}
				}
				$current_sector->set_type(1);
				if (rand(1,10) < 2){
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
		
		$colors = [
			["background" => "rgba(0,155,0,0.2)", "border"=>"rgba(0,155,0,1)"],
			["background" => "rgba(155,0,0,0.2)", "border" => "rgba(155,0,0,1)"],
			["background" => "rgba(0,0,155,0.2)", "border" => "rgba(0,0,155,1)"],
			["background" => "rgba(155,155,0,0.2)", "border" => "rgba(155,155,0,0.6)"],
			["background" => "rgba(155,0,155,0.2)", "border" => "rgba(155,0,155,0.6)"],
			["background" => "rgba(0,155,155,0.2)", "border" => "rgba(0,155,155,0.6)"],
			["background" => "rgba(155,255,155,0.2)", "border" => "rgba(155,255,155,0.6)"],
			["background" => "rgba(0,255,255,0.2)", "border" => "rgba(0,255,255,0.6)"],
			["background" => "rgba(255,255,0,0.2)", "border" => "rgba(255,255,0,0.6)"],
			["background" => "rgba(255,205,0,0.2)", "border" => "rgba(255,205,0,0.6)"],
			["background" => "rgba(205,255,0,0.2)", "border" => "rgba(205,255,0,0.6)"],
			["background" => "rgba(255,255,0,0.2)", "border" => "rgba(255,255,0,0.6)"],
			["background" => "rgba(255,255,0,0.2)", "border" => "rgba(255,255,0,0.6)"],
		];
		
		foreach($core->players as &$player){
			$color = array_shift($colors);
			$player->set_color($color);
			//$this->views[$player->id] = new VOID_VIEW();
			while(true){
				$key = array_rand($this->sectors,1);
				if ($this->sectors[$key]->star == 1 && !$this->sectors[$key]->home){
					
					break;
				}
			}
			$this->sectors[$key]->home = $player->id;
			$player->home = array('x'=> $this->sectors[$key]->x, 'z'=>$this->sectors[$key]->z);
			$this->sectors[$key]->set_system_owner($player->id);
			$this->sectors[$key]->system->colonise($player, $core);
			//$this->sectors[$key]->system->influence_per_turn = mt_rand(5,10);
			
			//$this->sectors[$key]->system->add_order();
			//$this->sectors[$key]->system->add_order();
			//$this->sectors[$key]->system->add_order();
			
			//$this->sectors[$key]->system->food_per_turn = mt_rand(1,10);
			
			$player->credits_per_turn = $this->sectors[$key]->system->get_credits_income();
			$player->research_per_turn = $this->sectors[$key]->system->get_research_income();
			
			// create new fleet
			$fleet = new VOID_FLEET();
			$core->fleets[$fleet->id] = $fleet; 
			// add a ship
			$ship = new VOID_SHIP($core->ship_classes[1], $player->id);
			$fleet->add_ship($ship);
			$this->sectors[$key]->add_fleet($fleet);
			
			//$key = array_rand($this->sectors,1);
			//$ship = new VOID_SHIP($ship_classes[1], $player->id);
			//$this->sectors[$key]->add_ship($ship, $core);
			
		}
		$this->update_map();
	}
	
	// run through all objects on the map
	// calculate who controls each hex
	// update vision for all players
	public function update_map(){
		
		// first reset the map state for all players
		foreach($this->sectors as &$sector){
			$sector->reset_state();
		}
		
		// run through each sector and apply 
		// sensor range and influence to all sectors
		foreach($this->sectors as &$sector){
			if ($sector->system){
				// you always "own" your home system
				$sector->owner = $sector->system->owner;
				$sector->add_state($sector->system->owner, "friendly", 1);
				$sector->add_state($sector->system->owner, "unknown", 0);
				$sector->add_state($sector->system->owner, "influence", $sector->system->influence_level);
				$sector->add_state($sector->system->owner, "sensor_power", 1);
				
				$neighbours = $sector->get_neighbours();
				if ($sector->system->influence_level >= 10){
					$influence_size = 4;
				}else if($sector->system->influence_level >= 7){
					$influence_size = 3;
				}else if($sector->system->influence_level >= 3){
					$influence_size = 2;
				}else {
					$influence_size = 1;	
				}
				
				for ($ring = 1; $ring < $influence_size; $ring++){					
					foreach($void_ranges[$ring] as $range){
						$x = $sector->x + $range['x'];
						$z = $sector->z + $range['z'];
					
						if (isset($this->sectors['x'.$x.'z'.$z])){
							$n =& $this->sectors['x'.$x.'z'.$z];
							$n->add_state($sector->system->owner, "sensor_power", 1);
							$n->add_state($sector->system->owner, "influence", $sector->system->influence_level/$ring);
						}
						
					}
				}
				
				
			}
			global $void_ranges;
			$fleets =& $sector->fleets;
			if ($fleets){
				foreach($fleets as &$player_fleets){
					foreach($player_fleets as &$fleet){
						$sector->add_state($fleet->owner, "unknown", 0);
						for ($ring = 1; $ring < 3; $ring++){
							
							foreach($void_ranges[$ring] as $range){
								$x = $sector->x + $range['x'];
								$z = $sector->z + $range['z'];
							
								if (isset($this->sectors['x'.$x.'z'.$z])){
									$n =& $this->sectors['x'.$x.'z'.$z];
									$n->add_state($fleet->owner, "sensor_power", 1);
									
								}
								
							}
						}
					}
				}
			}
		}
		
		// run through all sectors and calculate who "owns" each sector
		foreach($this->sectors as &$sector){
			
			$sector->update_owner();			
			$sector->update_fog();
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
		if ($this->sectors['x'.$x.'z'.$z]){
			return $this->sectors['x'.$x.'z'.$z];
		}
		return false;
	}
	
	public function dump_map($player_id){
		$map_view = new VOID_MAP_VIEW($this, $player_id);
		$map_view->update_sectors($this->sectors);
		return $map_view;
	}
}




?>