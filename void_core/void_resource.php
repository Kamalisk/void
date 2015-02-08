<?

class VOID_RESOURCE {
	public $type;
	public $per_turn;
	public $percent;
	public $pool;
	public $sources;
	
	function __construct($type){
		$this->type = $type;
		$this->per_turn = 0;
		$this->percent = 0;
		$this->pool = 0;
	}
	public function reset(){
		$this->percent = 0;
		$this->per_turn = 0;
		$this->sources = [];
	}
	public function add_pool($amount){
		$this->pool += $amount;
	}
	public function apply(){
		$this->sources['percent'] = $this->per_turn * ($this->percent) / 100;		
		$this->per_turn = $this->per_turn * ($this->percent + 100) / 100;		
	}
	public function upkeep(){
		$this->pool = $this->pool + $this->per_turn;
	}
	public function add_per_turn($amount, $source=""){
		$this->per_turn += $amount;
		if ($source){
			if (!isset($this->sources[$source])){
				$this->sources[$source] = $amount;
			}else {
				$this->sources[$source] += $amount;
			}
		}
	}
	public function add_percent($amount, $source=""){
		$this->percent += $amount;
	}
	
}

?>