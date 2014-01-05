<?

error_reporting(E_ALL);

include_once("void_core/void_core.php");

// load the game object from storage
$void = load();

if (isset($_GET['action'])){
	$_POST = $_GET;
}
if (isset($_GET['player_id'])){
	$player_id = $_GET['player_id'];
}else {
	$player_id = 1;
}

if (isset($_POST['action'])){
	$void->handle_input($_POST, $player_id);
}else {
	//$void->setup(20, 20);
	$void->dump_map($player_id);
}

save($void);

function load(){
	if (file_exists("test.core.void")){
		$void = unserialize(file_get_contents("test.core.void"));
		if ($void){
			return $void;
		}
	}
	$void = new VOID();
	$void->setup(20, 20);
	return $void;
}

function save($core){
	// store the game 
	file_put_contents("test.core.void", serialize($core));
}


?>
