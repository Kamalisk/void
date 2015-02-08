<?

class VOID_MODIFIER {
	public $max;
	public $value;
	public $type;
	public $scope;
	public $category;
	
	function __construct($type, $category, $value, $max=0){
		$this->type = $type;
		$this->value = $value;
		$this->max = $max;
		$this->scope = "player";
		$this->category = $category;
	}
	function get_modifier_id(){
		if ($this->category){
			return $this->type."_".$this->category;
		}else {
			return $this->type;
		}
	}
	function set_category($category){
		$this->category = $category;
	}
	function set_scope($scope){
		$this->scope = $scope;
	}
	function apply($target, $target_scope){
		$type = $this->type;
		$value = $this->value;
		$max = $this->max;
		$subtype = $this->category;
		
		if ($target_scope == "system"){
			if ($this->scope == "player"){
				$scope_target = $target->owner;
			}else {
				$scope_target = $target;
			}
		}else {
			$scope_target = $target;
		}
		$resource = "";
		switch($type){
			case "index":{
				$scope_target->add_property_index($subtype, $value);
				break;
			}
			case "command":{
				$scope_target->command++;
				break;
			}
			case "research":{
				$resource = $scope_target->research;
				break;
			}
			case "credits":{
				$resource = $scope_target->credits;
				break;
			}
			case "production":{
				$resource = $scope_target->production;
				break;
			}
			case "food":{
				$resource = $scope_target->food;
				break;
			}
			case "influence":{
				$resource = $scope_target->influence;
				break;
			}
			case "morale":{
				$resource = $scope_target->morale;
				break;
			}
		}
		
		if ($resource){
			switch($subtype){
				case "per_turn":{
					$resource->add_per_turn($value, "structures");
					break;
				}
				case "percent":{
					$resource->add_percent($value, "structures");
					break;
				}
				case "per_population":{
					$value = $value * $target->population;
					if ($max && $value > $max){
						$value = $max;
					}
					$resource->add_per_turn($value, "structures");
					break;
				}
			}
		}
	}
}


?>