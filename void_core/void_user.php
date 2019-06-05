<?php

require_once(".constants.php");

class VOID_USER {
	public $name;
	public $uuid;	
	public $authed = false;
	public $id;
	
	public $db;
	
	function __construct(){
		global $DBUSER, $DBPASSWORD;
		
		$this->db = new mysqli("localhost", $DBUSER, $DBPASSWORD, "void");
		if ($this->db->connect_errno) {
		    
		}
		$this->load_from_session();
	}
	
	public function auth($username, $password){
		//echo create_hash($password);
		$query = "
			SELECT uuid, hash, id
			FROM user
			WHERE user.name = '".$this->db->escape_string($username)."'
		";
		$result = $this->db->query($query);
		if ($row = $result->fetch_assoc()){
			if (validate_password($password, $row['hash'])){
				$_SESSION['user'] = [
					"uuid" => $row['uuid'],
					"id" => $row['id']
				];
				$this->load_from_session();
				return true;
			}else {
				return false;
			}
		}
		return false;
		
	}
	
	public function get_own_games(){
		$query = "
			SELECT game.name, game.uuid, game.id, game.state, game_user.game_id as joined
			FROM game
			INNER JOIN game_user ON game_user.game_id = game.id AND game_user.user_id = '".$this->db->escape_string($this->id)."'
			ORDER BY game.id DESC
		";
		$result = $this->db->query($query);
		$return = [];
		while ($row = $result->fetch_assoc()){
			if ($row['state'] == "lobby"){
				$row['joinable'] = 1;
			}
			$return[] = $row;
		}
		return $return;
	}
	
	public function get_games(){
		$query = "
			SELECT game.name, game.uuid, game.id, game.state, game_user.game_id as joined
			FROM game
			LEFT JOIN game_user ON game_user.game_id = game.id AND game_user.user_id = '".$this->db->escape_string($this->id)."'
			WHERE game_user.user_id IS NULL 
			ORDER BY game.id DESC
		";
		$result = $this->db->query($query);
		$return = [];
		while ($row = $result->fetch_assoc()){
			if ($row['state'] == "lobby"){
				$row['joinable'] = 1;
			}
			$return[] = $row;
		}
		return $return;
	}
	
	public function is_valid_game($game_id){
		$query = "
			SELECT game.name, game.uuid, game.id, game_user.game_id AS valid
			FROM game
			LEFT JOIN game_user ON game_user.game_id = game.id AND game_user.user_id = '".$this->db->escape_string($this->id)."'
			WHERE game.id = '".$this->db->escape_string($game_id)."'
			LIMIT 1
		";
		$result = $this->db->query($query);
		$return = [];
		if ($row = $result->fetch_assoc()){
			if ($row['valid']){
				return true;
			}
		}
		return false;
	}
	
	public function create_game($name){
		$query = "
			INSERT INTO game
			SET name = '".$this->db->escape_string($name)."', uuid = '".void_unique_id()."', state = 'lobby'
		";
		$result = $this->db->query($query);		
		return $this->db->insert_id;
	}
	
	public function reset_game($game_id){
		$query = "
			DELETE FROM game_user
			WHERE game_user.game_id = '".$this->db->escape_string($game_id)."'
		";
		$result = $this->db->query($query);		
		return true;		
	}
	
	public function join_game($game_id){
		$query = "
			INSERT INTO game_user
			SET game_id = '".$this->db->escape_string($game_id)."', user_id = '".$this->db->escape_string($this->id)."'			
		";
		$result = $this->db->query($query);		
		return true;		
	}
	
	public function start_game($game_id){
		$query = "
			UPDATE game 
			SET game.state = 'started' 
			WHERE game_id = '".$this->db->escape_string($game_id)."'
		";
		$result = $this->db->query($query);		
		return true;		
	}
	
	private function load_from_session(){
		if (isset($_SESSION['user'])){
			$this->uuid = $_SESSION['user']['uuid'];
			$this->id = $_SESSION['user']['id'];
			$this->authed = true;
			
			//echo create_hash($password);
			$query = "
				SELECT uuid, hash, id, name
				FROM user
				WHERE user.id = '".$this->db->escape_string($this->id)."'
			";
			$result = $this->db->query($query);
			if ($row = $result->fetch_assoc()){
				$this->name = $row['name'];
			}
			
			
		}
	}
	
	public function is_authed(){
		if ($this->authed){
			return true;
		}
	}
	
	public function unauth(){
		unset($_SESSION['user']);
		$this->authed = false;
	}
	

}


?>
