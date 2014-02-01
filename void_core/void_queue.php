<?php 

class VOID_QUEUE {
	public $items;
	private $index;
	
	function __construct(){
		$this->items = [];
	}
	public function add($item){
		$this->index[$item->type.$item->data->id] = 1;
		$this->items[] =& $item;
	}
	public function remove(){
		
	}
	public function pop(){
		return array_shift($this->items);
	}
	public function swap(){
		
	}
	public function get_front(){
		return reset($this->items);
	}
	public function progress($work){
		$item = $this->get_front();
		if ($item){
			$item->progress = $item->progress - $work;
			if ($item->progress <= 0){
				return $this->pop();
			}
		}
		return false;
	}
	public function dump($work=0){
		return new VOID_QUEUE_VIEW($this, $work);
	}
	public function exists($id, $type){
		if (isset($this->index[$type.$id])){
			return true;
		}
		return false;
	}
}

class VOID_QUEUE_VIEW {
	public $items;
	function __construct($queue, $work=0){
		$this->items = array();
		foreach($queue->items as &$item){
			$this->items[] = $item->dump($work);
		}
	}
	
}

class VOID_QUEUE_ITEM {
	public $id;
	public $data;
	public $progress;
	public $target;
	public $type;
	
	function __construct(){
		// generate a "random" unique id for the queue
		$this->id = uniqid(rand(100,999));
		
	}
}

class VOID_SYSTEM_QUEUE_ITEM extends VOID_QUEUE_ITEM {
	public $work;
	
	public function dump($work){
		return new VOID_SYSTEM_QUEUE_ITEM_VIEW($this, $work);
	}
}
class VOID_SYSTEM_QUEUE_ITEM_VIEW {
	public $type;
	public $id;
	public $target_id;
	public $turns;
	
	function __construct($item, $work=0){
		$this->id = $item->id;
		$this->type = $item->type;
		$this->target_id = $item->data->id;
		if ($work){
			$this->turns = ceil($item->progress / $work);
		}
	}
}

?>