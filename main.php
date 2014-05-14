<?

error_reporting(E_ALL);
ini_set("memory_limit","274217728");

include_once("void_core/void_core.php");

$game_id = 1;

//$_POST = $_GET;	
if (isset($_GET['action']) && $_GET['action'] == "reset"){
	$_POST = $_GET;	
}

if (isset($_POST['action'])){
	// need to get a lock before continuing
	$fp = fopen("games/".$game_id."/lock.void", "w");
	while (!flock($fp, LOCK_EX)) {  
	   sleep(1);
	}
}

try {
	// load the game object from storage
	$void = load($game_id);
	
	if (isset($_GET['player_id'])){
		$player_id = $_GET['player_id'];
	}else {
		if ($void->state != "lobby"){
			//return false;
		}
		$player_id = 0;
	}
	
	
	
	if (isset($_POST['action'])){
		
		$return = $void->handle_input($_POST, $player_id);
		if ($return){
			$player_id = $return;
		}
		$void->dump_status($player_id);
		save($void);
	}else if (isset($_GET['action']) && $_GET['action'] == "status"){	
		$void->dump_status($player_id);
	}else {
		//$void->setup(20, 20);
		if (isset($_GET['first'])){
			$void->dump_map($player_id, true);
		}else {
			$void->dump_map($player_id, false);
		}
		save($void);
	}
	
	if (isset($_POST['action'])){
		flock($fp, LOCK_UN);    // release the lock
	}
}catch (Exception $e){
	if (isset($_POST['action'])){
		flock($fp, LOCK_UN);    // release the lock
	}
	throw $e;
}

function load($game_id){
	if (file_exists("games/".$game_id."/core.void")){
		$void = unserialize(file_get_contents("games/".$game_id."/core.void"));
		if ($void){
			$void->post_load();
			return $void;
		}
	}
	$void = new VOID($game_id);
	$void->init();
	//$void->setup(30, 10);
	return $void;
}

function save($core){
	// store the game 
	if (!file_exists($core->game_path)){
		mkdir($this->game_path);
	}
	file_put_contents("games/".$core->game_id."/core.void", serialize($core));
}


?>
