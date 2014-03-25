<?


class VOID_FLEET {
	public $x;
	public $z;
	public $orders = array();
	public $ships = array();
	public $owner;
	public $id;
	
	public $movement_points;
	public $movement_capacity;
	
	public $in_transit;
	
	public $capacity;
	
	public $docked;	
	
	public $done;
	public $damage;
	
	function __construct(){
		$this->id = void_unique_id();
		$this->in_transit = false;
		$this->capacity = 3;
		$this->docked = false;
		$this->movement_points = 2;
		$this->movement_capacity = 2;
		$this->done = false;
	}
	
	public function add_ship($ship){
		if (count($this->ships) < $this->capacity){
			$this->ships[] = $ship;
			$this->owner = $ship->owner;
			$this->update_fleet_stats();
			return true;
		}else {
			return false;
		}
	}
	
	public function remove_ship($ship_to_remove){
		foreach($this->ships as $key => $ship){			
			if ($ship->id == $ship_to_remove->id){
				unset($this->ships[$key]);
			}
		}
	}
	
	public function update_fleet_stats(){
		$lowest_movement_points = 0;
		if (count($this->ships)){			
			foreach($this->ships as $ship){
				if (!$lowest_movement_points){
					$lowest_movement_points = $ship->class->movement_capacity;
				}
				if ($ship->class->movement_capacity < $lowest_movement_points){
					$lowest_movement_points = $ship->class->movement_capacity;
				}
			}			
		}
		$this->movement_capacity = $lowest_movement_points;
	}
	
	public function reset_orders(){
		$this->orders = array();
	}
	
	public function add_order($type, $params){
		$order = new VOID_ORDER($type);
		$order->add_params($params);
		$this->orders[] = $order;
	}
	
	public function dump_view($player_id){
		if (count($this->ships) || $this->docked){
			$view = new VOID_FLEET_VIEW($this, $player_id);
			return $view;
		}else {
			return false;
		}
	}
	
	public function get_ship($id){
		foreach($this->ships as $ship){
			if ($ship->id == $id){
				return $ship;
			}
		}
		return false;
	}
	
	public function transfer_ship($ship_id, $fleet){
		$ship_to_move = $this->get_ship($ship_id);
		$fleet->add_ship($ship_to_move);
		$this->remove_ship($ship_to_move);
	}
	
	public function move($x, $z, $core){				
		$old_x = $this->x;
		$old_z = $this->z;
		if ($core->map->get_sector($x, $z)->add_fleet($this)){			
			$core->map->get_sector($old_x, $old_z)->remove_fleet($this);
			$this->x = $x;
			$this->z = $z;
			$core->map->get_sector($old_x, $old_z)->resolve_ruin($core, $core->players[$this->owner] );
			return true;
		}else {
			return false;
		}
	}
	
	public function has_orders(){
		if (count($this->orders) > 0){
			return true;
		}
		return false;
	}
	
	public function get_order($tick=0){
		if ($this->orders && count($this->orders) > 0){
			return reset($this->orders);
		}else {
			return false;
		}
	}
	public function complete_order(){
		if ($this->orders){
			array_shift($this->orders);
		}
	}
	
	public function put_order($order){
		array_unshift($this->orders, $order);		
	}
	
	public function reset_movement_points(){
		$this->movement_points = $this->movement_capacity;
		$this->done = false;
	}
	
	public function clean_up(){		
		// remove any dead ships
		foreach($this->ships as $key => $ship){
			if (!$ship){
				unset($this->ships[$key]);
			}
			if ($ship->hull <= 0){
				unset($this->ships[$key]);
			}
		}
		if ($this->docked){
			return true;
		}
		if (count($this->ships) <= 0){
			return true;
		}
		return false;
	}
	
	public function get_special(){
		if (count($this->ships)){
			$special = [];
			foreach($this->ships as $ship){
				if ($list = $ship->get_special()){
					foreach($list as $item){
						$special[$item] = $item;
					}
				}
			}
			return $special;
		}else {
			return false;
		}
	}
	
	public function remove_special($special){
		if (count($this->ships) && $special){
			foreach($this->ships as $key => $ship){
				if ($ship->get_special($special)){
					unset($this->ships[$key]);
					if (count($this->ships) <= 0){
						return true;
					}
				}
			}
		}
		return false;
	} 
	
	public function get_vision_range(){
		$max_vision_range = 1;
		if (count($this->ships)){
			foreach($this->ships as $key => $ship){
				if ($ship->class->vision_range && $ship->class->vision_range > $max_vision_range){
					$max_vision_range = $ship->class->vision_range;
				}
			}
		}
		return $max_vision_range;
	}
	
	
	public function upkeep($core){
		foreach($this->ships as $ship){
			//$core->players[$this->owner]->update_resource("credits", -2, "fleet");
		}
	}
	
	public function update($core){
		if ($this->docked != true && $this->owner){		
			$core->players[$this->owner]->update_resource("credits", -3, "fleet");
		}
		$this->update_damage();
		foreach($this->ships as $ship){
			$ship->update($core);			
		}
	}
	
	
	public function fire($enemy_fleet){		
		// calculate damage output of fleet			
		$enemy_fleet->hit($this->damage);
		VOID_LOG::write($this->owner, "[combat] Your fleet has damaged another fleet");
	}
	
	public function hit($damage){
		// do damage to the ships in the fleet
		$damage = ceil($damage / count($this->ships));
		foreach($this->ships as $ship){
			$ship->hit($damage);
		}
	}
	
	public function update_damage(){
		$this->damage = 0;
		foreach($this->ships as $ship){
			$this->damage = $this->damage + $ship->damage;			
		}
	}
	
}



class VOID_FLEET_VIEW extends VOID_VIEW {
	public $name;
	public $owner;
	public $ships;
	
	public $orders;
	public $x;
	public $z;
	public $id;
	
	public $capacity;
	public $used_capacity;
	
	public $movement_points;
	public $movement_capacity;
	
	public $damage;
	
	function __construct($fleet, $player_id){
		
		$this->ships = [];
		
		$this->owner = $fleet->owner;
		$this->movement_points = $fleet->movement_capacity;
		$this->movement_capacity = $fleet->movement_capacity;
		// need to turn through all ships and output the "view"
		foreach($fleet->ships as $ship){
			$this->ships[] = $ship->dump($player_id);
		}
		$this->x = $fleet->x;
		$this->z = $fleet->z;
		$this->id = $fleet->id;
		$this->capacity = $fleet->capacity;
		$this->used_capacity = count($fleet->ships);
		$this->damage = $fleet->damage;
		//$this->show = true;
		if ($this->owner == $player_id){
			$this->orders = $fleet->orders;
		}
		//$this->special = $fleet->get_special();
		
	}
	
}








class VOID_SHIP {
	public $class;
	public $owner;
	public $id;
	
	public $hull;
	public $shields;
	
	public $damage;
	
	function __construct($class, $player_id){
		$this->class = $class;
		$this->owner = $player_id;
		$this->id = void_unique_id();
		$this->hull = $class->hull;
		$this->shields = $class->shields;
		$this->damage = $class->damage;
	}
	
	function dump($player_id){
		$view = new VOID_SHIP_VIEW($this, $player_id);
		return $view;
	}
	
	public function fire($ship){
		
		if ($this->class->weapon_count > 0){
			for ($i = 0; $i < $this->class->weapon_count; $i++){
				$ship->damage($this->class->weapon_damage * mt_rand(1,2));
			}
		}
	}
	
	public function hit($damage){
		$this->damage($damage);
	}
	
	public function damage($amount){
		if ($this->shields > 0){
			$amount = $amount - $this->shields;
			if ($amount <= 0){
				return;
			}
		}				
		$this->hull = $this->hull - $amount;
	}
	
	public function get_special($special=""){
		return $this->class->get_special($special);
	}
	
	public function update($core){
		$core->players[$this->owner]->update_resource("credits", -2, "ship");
		if ($this->hull){
			$this->damage = ceil($this->class->damage * ($this->hull / $this->class->hull));
		}else {
			$this->damage = 0;
		}
	}
	
}

class VOID_SHIP_VIEW {
	public $class_id;
	public $owner;
	
	public $hull;
	public $shields;
	public $damage;
	
	public $id;
	
	function __construct($ship, $player_id){
		$this->class_id = $ship->class->id;
		$this->owner = $ship->owner;
		$this->id = $ship->id;
		//if ($player_id == $this->owner){
			$this->hull = $ship->hull;
			$this->shields = $ship->shields;
		//}
		$this->damage = $ship->damage;
	}
	
	
}

class VOID_SHIP_CLASS {
	public $name = "";
	public $id;
	
	public $weapon_damage;
	public $weapon_type;
	public $weapon_count;
	
	public $damage;
	
	public $movement_capacity;
	public $vision_range;
	
	public $special;
	
	public $work_required;
	public $rush_cost;
	
	public $hull;
	public $shields;
	
	function __construct(){
		$this->special = [];
		$this->weapon_type = "laser";
		$this->weapon_count = 1;
		$this->weapon_damage = 10;
		$this->work_required = 10;
		$this->vision_range = 1;
		$this->rush_cost = 20;
		$this->movement_capacity = 4;
		$this->hull = 100;
		$this->shields = 10;
		$this->damage = 10;
	}
	
	function add_special($special){
		$this->special[$special] = $special;
	}
	
	function get_special($special=""){
		if ($special){
			// if parameter set then check if a special type is test
			return isset($this->special[$special]);
		}else {
			// otherwise return the data as an array
			return $this->special;
		}		
	}
	
	
}



?>