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
	
	function __construct(){
		$this->id = void_unique_id();
		$this->in_transit = false;
		$this->capacity = 3;
		$this->docked = false;
		$this->movement_points = 2;
		$this->movement_capacity = 2;
	}
	
	public function add_ship($ship){
		if (count($this->ships) < $this->capacity){
			$this->ships[] = $ship;
			$this->owner = $ship->owner;
			return true;
		}else {
			return false;
		}
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
		if (count($this->ships)){
			$view = new VOID_FLEET_VIEW($this, $player_id);
			return $view;
		}else {
			return false;
		}
	}
	
	public function move($x, $z, $core){
		$core->map->get_sector($this->x, $this->z)->remove_fleet($this);
		$this->x = $x;
		$this->z = $z;
		$core->map->get_sector($x, $z)->add_fleet($this);
	}
	
	public function get_order($tick=0){
		
		if ($this->orders){
			return array_shift($this->orders);
		}
	}
	
	public function reset_movement_points(){
		$this->movement_points = 2;
	}
	
	public function clean_up(){
		// remove any dead ships
		foreach($this->ships as $key => $ship){
			if ($ship->shields <= 0){
				unset($this->ships[$key]);
			}
		}
	}
	
	public function get_special(){
		if (count($this->ships)){
			$special = [];
			foreach($this->ships as &$ship){
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
	
	function __construct($class, $player_id){
		$this->class = $class;
		$this->owner = $player_id;
		$this->id = void_unique_id();
		$this->hull = 100;
		$this->shields = 100;
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
	
	public function damage($amount){
		if ($this->shields > 0){
			$this->shields = $this->shields - $amount;
		}
		if ($this->shields <= 0){
			$this->shields = 0;
			$this->hull = $this->hull - $amount;
		}
	}
	
	public function get_special($special=""){
		return $this->class->get_special();
	}
	
}

class VOID_SHIP_VIEW {
	public $class_id;
	public $owner;
	
	public $hull;
	public $shields;
	
	public $id;
	
	function __construct($ship, $player_id){
		$this->class_id = $ship->class->id;
		$this->owner = $ship->owner;
		$this->id = $ship->id;
		if ($player_id == $this->owner){
			$this->hull = $ship->hull;
			$this->shields = $ship->shields;
		}
	}
	
	
}

class VOID_SHIP_CLASS {
	public $name = "";
	public $id;
	
	public $weapon_damage;
	public $weapon_type;
	public $weapon_count;
	
	public $movement_points;
	
	public $special;
	
	public $work_required;
	public $rush_cost;
	
	function __construct(){
		$this->special = [];
		$this->weapon_type = "laser";
		$this->weapon_count = 2;
		$this->weapon_damage = 10;
		$this->work_required = 10;
		$this->rush_cost = 100;
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