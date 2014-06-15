
	

var player;
var players;

var hex_path;

var planet_class_cache;
var sector_class_cache;
var ship_class_cache;
var structure_class_cache;
var upgrade_class_cache;
var power_class_cache;
var tech_tree;

var race_classes;

var game_state;

var event_logs;

var fleet_cache = {};

var current_tech;

var mouse_down_event;
var scroll_in_progress;
var map_scroll_offset;
var map_buffer = 800;
var map_chunk_size = {};
var map_canvas_buffer_offset = null;

var panel_view = "";

var fleet_orders = new Object();
var fleet_order_mode = false;
var fleet_order_start_hex = new Object();

var player_orders = [];

var fleet_selected;

var system_orders = new Object();

var first_load = false;
var first_loading_complete = false;

var waiting_for_players = false;

var void_debug;

var void_view;

var changes = {
	fleet_orders: {},
	system_orders: {},
	fleet_transfers: {}	
};

$(document).ready(function (){
	
	var custom_scroll_decorator = function ( node, content ) {
	  // setup work goes here...
		
		/*
		$(node).customScrollbar({
			hScroll: false,
			vScroll: true, 
			skin: "default-skin"	
		});
		*/
		
	  return {
	    teardown: function () {
	      //$(node).customScrollbar("remove")
	    }
	  };
	};
	
	var lock_decorator = function (node, content){
		var bottom = $("#"+content).height() + 6;
		$(node).css({"bottom": bottom});
		console.log($("#"+content));
		return {
	    teardown: function () {
	    	
	    }
	  };
	}
	
	var tooltip_decorator = function ( node, content ) {
	  var tooltip, handlers, eventName, tooltip_content;

	  handlers = {
	    mouseover: function () {
	      tooltip_content = $("#"+$(node).attr("data-decorator")).html();
	      tooltip = $("#tooltip_display");
	      tooltip.html(tooltip_content);
	      tooltip.show();
	    },
	
	    mousemove: function ( event ) {
	      // Keep the tooltip near where the mouse is	      
	      var offset_left = 0;
	      var offset_top = 20;
	      if (event.clientX + tooltip.width() > $(window).width() - 40){
	      	offset_left = event.clientX + tooltip.width() - $(window).width() + 40;	      	
	      }
	      if (event.clientY + tooltip.height() > $(window).height() - 40){
	      	offset_top = event.clientY - tooltip.height() - $(window).height() - 20;	      	
	      }

	      tooltip.css({"left": (event.clientX - offset_left) + 'px', "top": ( event.clientY  + offset_top ) + 'px'});
	    },
	
	    mouseleave: function () {
	      // Destroy the tooltip when the mouse leaves the node
	      tooltip.hide()
	    }
	  };
	
	  // Add event handlers to the node
	  for ( eventName in handlers ) {
	    if ( handlers.hasOwnProperty( eventName ) ) {
	      node.addEventListener( eventName, handlers[ eventName ], false );
	    }
	  }
	
	  // Return an object with a `teardown()` method that removes the
	  // event handlers when we no longer need them
	  return {
	    teardown: function () {
	      for ( eventName in handlers ) {
	        if ( handlers.hasOwnProperty( eventName ) ) {
	          node.removeEventListener( eventName, handlers[ eventName ], false );
	        }
	      }
	    }
	  };
	};
	
	void_view = new Ractive({
	  el: "void_app",
	  template: "#primary_template",	
	  data: {	
	  	void: {
	  		per_turn_value : function(number){
		  		if (number >= 0){
		  			return "+" + number;
		  		}else {
		  			return number;
		  		}
		  	},
		  	turns: function (target, per_turn){
		  		var turns = Math.ceil(target / per_turn);
		  		if (turns == 1){
		  			return turns+" turn";
		  		}else {
		  			return turns+" turns";
		  		}
		  	}
	  	}
	  },
	  decorators: {
	    custom_scroll: custom_scroll_decorator,
	    tooltip: tooltip_decorator,
	    lock: lock_decorator
	  },
	  modifyArrays: false
	});
	
	// on first page load get game status
	$.get("main.php"+location.search,{'action':'status'},function(data){
		console.log(data);
		if (data && data.state == "lobby"){
			// game is in lobby mode
			// start loop checking for lobby mode
			check_lobby_status();
		}else {
			fetch_game_data(true);		
		}
	});	
		
});

function check_lobby_status(){
	$.get("main.php"+location.search,{'action':'status'},function(data){		
		if (data && data.state == "lobby"){
			$('#main_loading').hide();
			if (data.player){
				void_view.set("player", data.player);
			}
			if (data.players){
				void_view.set("players", data.players);
			}
			if (data.state){
				void_view.set("game_state", data.state);
			}
			if (data.races && !race_classes){
				race_classes = data.races;
				void_view.set("races", race_classes);
			}
			setTimeout(check_lobby_status, 3000);
		}
		if (data && data.state == "game"){
			// reload the page
		}
	});
}
var selected_race;
var selected_empire;
var selected_leader;

function reset_lobby(){
	var url = "main.php";
	if (location.search){
		url += location.search;
	}	
	$.post(url,{
		"action":"reset_lobby"
	},check_lobby_status);
	
}

function send_race(){
	var url = "main.php";
	if (location.search){
		url += location.search;
	}
	if (selected_race && selected_empire && selected_leader){
		$.post(url,{
			"action":"select", 
			"race_id":selected_race.id, 
			"empire_id":selected_empire.id, 
			"leader_id":selected_leader.id
		},check_lobby_status);
	}
}

function select_race(id){
	if (selected_race){
		selected_race.selected = false;
	}
	selected_race = race_classes[id];
	selected_race.selected = true;
	void_view.set("races", race_classes);
	void_view.set("selected_race", selected_race );
	
}
function select_empire(id){
	if (selected_empire){
		selected_empire.selected = false;
	}
	selected_empire = selected_race.empires[id];
	selected_empire.selected = true;
	//void_view.set("selected_race.empires["+id+"].selected", true );
	void_view.set("selected_race", selected_race );
}
function select_leader(id){
	if (selected_leader){
		selected_leader.selected = false;
	}
	selected_leader = selected_race.leaders[id];
	selected_leader.selected = true;
	void_view.set("selected_race", selected_race );
}

function allow_actions(){
	if (player.done){
		return false;
	}else {
		return true;
	}
}



function update_interface(){
	if (player){
		if (player.current_tech && player.research_per_turn){
			player.research_time = Math.ceil(player.current_tech.progress / player.research_per_turn);
		}
		//append_template("player_resources_template", player, "player_resources");
		
		//append_template("event_log_template", player, "galactic_map_event_list");
		//$("#galactic_map_event_list").show();
		//$("#galactic_map_event_list").fadeOut(7000);
	}
	if (panel_view == "system" && hex_selected && hex_selected.x){
		show_map_panel_system(hex_selected);	
	}
	render_research();
	//render_diplomacy();
}


function parse_logs(lines){
	var logs = [];
	$.each(lines, function(index, value){
		var log_obj = {};
		log_obj.text = value;
		
		var matches = log_obj.text.match(/^\[(.+)\]/i, "");
		if (matches && matches[1]){
			log_obj.category = matches[1];
			//log_obj.category = "generic";
		}else {
			log_obj.category = "generic";
		}
		log_obj.text = log_obj.text.replace(/^\[.+\]/i, "");
		
		logs.push(log_obj);
	});	
	
	return logs;
}


var minimap_drag = false;
function start_minimap_drag(x, y){
	minimap_drag = true;
}
function end_minimap_drag(x, y){
	// in case it was a single click and no drag, move the view point to the cursor
	minimap_move(x, y);
	minimap_drag = false;
}
function minimap_move(x, y){
	if (minimap_drag){
		//mouse_move_event({"pageX": , "});
		//jump_map(x * map_viewport.factorWidth  - $('#galactic_map_container').width()/2, y * map_viewport.factorHeight  - $('#galactic_map_container').height()/2);
		//$('#galactic_map_container').scrollLeft(x*map_viewport.factorWidth*map_scale - ($('#galactic_map_container').width() / 2));
		//$('#galactic_map_container').scrollTop(y*map_viewport.factorHeight*map_scale - ($('#galactic_map_container').height() / 2));
		draw_minimap_overlay();
	}
}


function update_map(){
	
}




function fetch_template(id, data){
	var source = $('#'+id).html();
	//var template = Handlebars.compile(source);
	
	//var html = template(data);
	return source;
}

function change_view(view_id){
	$('.view').hide();
	if (view_id == "research"){
		render_research();
	}
	if (view_id == "diplomacy"){
		render_diplomacy();
	}	
	$('#'+view_id+'_view').show();
}


function render_research(){
	var temp_obj = new Object();
	temp_obj['tech_tree'] = tech_tree;
	temp_obj['player'] = player;
	temp_obj['tiers'] = {};
	$.each(tech_tree.items, function(){
		this.player_has = null;
		if (player.tech[this.id]){
			this.player_has = player.tech[this.id];
		}
		this.player_available = null;
		if (player.available_tech[this.id]){
			this.player_available = player.available_tech[this.id];
		}
		if (player.current_tech && player.current_tech.class.id == this.id){
			this.player_current = true;
		}else {
			this.player_current = false;
		}
		if (!temp_obj['tiers'][this.tier]){
			temp_obj['tiers'][this.tier] = [];
		}
		temp_obj['tiers'][this.tier].push(this);
	});
	void_view.set("tech_tree", temp_obj);
	//append_template("tech_tree_template",temp_obj,"tech_tree_items");
}

function render_diplomacy(){
	var temp_obj = new Object();	
	temp_obj['players'] = players;	
	//append_template("diplomacy_template",temp_obj,"diplomacy_view");
	/*
	$("#diplomacy_player_list_container").customScrollbar({
		hScroll: false,
		vScroll: true, 
		skin: "default-skin"	
	});
	*/
}

function show_diplomacy_detail(player_id) {		
	void_view.set("diplomacy.selected_player", players[player_id]);
	//append_template("diplomacy_detail_template",players[player_id],"diplomacy_detail_view");
}

function show_map_panel_system(hex){
	panel_view = "system";
	fleet_selected = false;
	$("#galactic_map_panel").show();
	$("#galactic_map_panel_fleet").hide();
	$("#galactic_map_panel_content").empty();			
	var hex = hex_map['x'+hex.x+'z'+hex.z];
	if (hex.owner){
		hex.owner_object = players[hex.owner];
	}
	if (hex.system && hex.system.planets){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];
		});
	}
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		
		$.each(hex.system.build_queue.items, function (){
			
			if (this.type == "ship"){
				this.target = ship_class_cache[this.target_id];
			}else if (this.type == "structure"){
				this.target = structure_class_cache[this.target_id];
			}
			
		});
	}
	if (hex.class_id && hex.type){
		hex.class = sector_class_cache[hex.class_id];		
	}
	
	if (hex.your_fleets){
		$.each(hex.your_fleets, function(){
			if (this.ships && this.ships){
				$.each(this.ships, function (){
					this.class = ship_class_cache[this.class_id];					
				});
			}
			show_map_panel_fleet(this.id);
		});		
	}
	if (hex.space_dock && hex.space_dock.fleet && hex.space_dock.fleet.ships){
		$.each(hex.space_dock.fleet.ships, function (){
			this.class = ship_class_cache[this.class_id];					
		});
	}
	
	redraw_overlay();
	
	append_template('galactic_map_panel_system_template',hex, "galactic_map_panel_content");
	var html = fetch_template('galactic_map_panel_system_template',hex);
	if (hex.your_fleets && hex.your_fleets.length > 0){
		map_context_menu_type = "default";
	}else {
		map_context_menu_type = "default";
	}
}


function show_map_panel_fleet(id){
	$("#galactic_map_panel_fleet").show();
	//panel_view = "fleet";
	//$("#galactic_map_panel_content").empty();
	//start_fleet_order(id);
	var fleet = fleet_cache[id];
	var hex = get_hex(fleet.x, fleet.z);
	hex_selected = hex;
	fleet_selected = id;

	if (!fleet){
		return;
	}
	if (fleet.owner){
		fleet.owner_object = players[fleet.owner];
	}
	if (fleet.ships && fleet.ships){
		$.each(fleet.ships, function (){
			this.class = ship_class_cache[this.class_id];
		});
	}
	redraw_overlay();
	//append_template('galactic_map_panel_fleet_template',fleet, "galactic_map_panel_fleet");
	map_context_menu_type = "default";
}

function show_system_view(x, z){		
	var hex = hex_map['x'+x+'z'+z];
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		$.each(hex.system.build_queue.items, function (){
			if (this.type == "ship"){
				//this.target = ship_class_cache[this.target_id];
			}else if (this.type == "structure"){
				//this.target = structure_class_cache[this.target_id];
			}
		});
		
		hex.system.available_structure_classes = [];
		$.each(hex.system.available_structures, function (){
			if (structure_class_cache[this]){
				var structure_id = this;
				var in_queue = false;
				// check the orders they have made this turn also
				if (system_orders[hex.system.id]){
					$.each(system_orders[hex.system.id], function (){
						if (this.target_id == structure_id && this.type == "structure"){
				 			in_queue = true;
						}
					});
				}
				if (!in_queue){
					hex.system.available_structure_classes.push(structure_class_cache[this]);
				}
								
			}
		});
	}	
	void_view.set("system_view.system", hex.system);
	void_view.set("system_view.mode", "full");	
}


function show_system_view_colonise(x, z){
	var hex = hex_map['x'+x+'z'+z];
	
	if (hex.system){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];
			this.selectable = true;
		});
	}
	
	if (!hex.system){		
		show_error("No colonizable system here!");
	}
	
	void_view.set("system_view.system", hex.system);
	void_view.set("system_view.mode", "colonise");	
}

function show_construct(x, z){
	void_view.set("fleet_selected.sub", true);
}


function show_context_menu(id, x, y, type){
	
	var map_x = x - $("#galactic_map_overlay").offset().left;
	var map_y = y - $("#galactic_map_overlay").offset().top
	
	$("#"+id).css({"left": x-4, "top" : y-4});
	$("#"+id).empty();
	if (type){
		if (type == "system_planet_menu"){
			var html = fetch_template('system_planet_context_menu');
		}
	}else {
		if (map_context_menu_type == "fleet_move"){
			var html = fetch_template('map_context_menu_fleet_move');		
		}else if (map_context_menu_type == "default"){
			var coords = pixel_to_hex(map_x,map_y);
			var html = fetch_template('map_context_menu_default', hex_map["x"+coords[0]+"z"+coords[2]]);	
		}else {
			return false;
		}
	}	
	$("#"+id).append(html);
	$("#"+id).show();
}


function show_error(msg){
	void_view.set("global_error", msg);
	$("#error_display").show();
	$("#error_display").fadeOut(3000);
	//void_view.animate("global_error", null, {"duration": 20000, "easing": "easeIn"});
}

function clean_up_tooltips(){
	$('.tooltipsy').remove();
}

function context_menu(event, params){
	show_context_menu('galactic_map_context_menu', event.pageX, event.pageY, 'system_planet_menu');
	event.preventDefault();
}

function start_fleet_management(){
	var adj_hexes = [];
	adj_hexes.push(get_adjacent_hex(hex_selected, 0));
	adj_hexes.push(get_adjacent_hex(hex_selected, 1));
	adj_hexes.push(get_adjacent_hex(hex_selected, 2));
	adj_hexes.push(get_adjacent_hex(hex_selected, 3));
	adj_hexes.push(get_adjacent_hex(hex_selected, 4));
	adj_hexes.push(get_adjacent_hex(hex_selected, 5));	
	void_view.set("fleet_management", fleet_selected);
	void_view.set("fleet_management_hexes", adj_hexes);
}

function fleet_management_drag(event){
	$("#tooltip_display").hide();	
	event.dataTransfer.setData("from_fleet",$(event.target).attr("data-fleet") );
	event.dataTransfer.setData("from_ship",$(event.target).attr("data-index") );
}

function fleet_management_drop(event){
	if (!allow_actions()){
		return;
	}
	//console.log( event.dataTransfer.getData("from_fleet"));
	// now move the ship from one fleet to another somehow? :P
	var from_fleet = fleet_cache[event.dataTransfer.getData("from_fleet")];
	var from_ship_index = event.dataTransfer.getData("from_ship")
	
	var to_fleet = fleet_cache[$(event.target).attr("data-fleet")];
	//var to_ship_index = $(event.target).attr("data-index");
	
	//console.log($(event.target).attr("data-fleet"));
	if (!to_fleet.ships){
		to_fleet.ships = [];
	}
	// player.fleet_size
	if (to_fleet.ships.length >= 3){
		show_error("A fleet cannot exceed 3 ships");
		return false;
	}
	if (!from_fleet.ships[from_ship_index].from_fleet){
		from_fleet.ships[from_ship_index].from_fleet = from_fleet.id;
	}
	from_fleet.transfer = true;
	to_fleet.transfer = true;
	
	if (!changes.fleet_transfers[from_fleet.id]){
		changes.fleet_transfers[from_fleet.id] = {};
	}
	changes.fleet_transfers[from_fleet.id].ship_id = from_fleet.ships[from_ship_index].id;
	changes.fleet_transfers[from_fleet.id].fleet_id = to_fleet.id;
	
	to_fleet.ships.push(from_fleet.ships[from_ship_index]);
	from_fleet.ships.splice(from_ship_index, 1);
	
	void_view.update();
	
}

function start_fleet_order_mode(id){
	// get the current fleet details 
	var fleet = fleet_cache[id];
	
	if (fleet.transfer == true){
		show_error("Cannot move a fleet undergoing ship transfers.");
		return false;
	}
	
	map_context_menu_type = "default";
	map_right_click = "fleet_move";

	if (fleet_orders[id] && fleet_orders[id].length > 0){
		fleet_order_start_hex = fleet_orders[id][fleet_orders[id].length-1];
	}else {
		fleet_order_start_hex = get_hex(fleet.x,fleet.z);
	}
	
	// set the hex highlight path start points
	fleet_order_mode = true;

	redraw_overlay();
}
function end_fleet_order_mode(id){
	fleet_order_mode = false;
	map_context_menu_type = "default";
	map_right_click = "";
	redraw_overlay();
}

function add_fleet_order(start, end){
	// add path of hexes to order list
	if (!allow_actions()){
		return;
	}
	var path = get_path_between_hexes(start, end, fleet_cache[fleet_selected.id].movement_capacity);
	var attack = false;
	if (path){
		for (var i = 0; i < path.length ; i++){
			if (path[i]){
				if (path[i].enemy_fleets){					
					if (path[i].enemy_fleets && path[i].enemy_fleets[0]){
						attack = path[i].enemy_fleets[0].owner;
					}
				}
			}
		}
		if (attack && players[attack]){
			if (!players[attack].war){
				show_error("You must declare war before you can attack other empires.");
				return false;
			}
		}

		for (var i = 0; i < path.length ; i++){
			if (path[i]){
				if (!fleet_orders[fleet_selected.id]){
					fleet_orders[fleet_selected.id] = new Array();
				}
				var order = {'type':'move', 'x':path[i].x, 'z':path[i].z};
				fleet_orders[fleet_selected.id].push(order);
			}
		}
	}
	fleet_order_start_hex = end;
}

function add_fleet_colonise_order_dialog(){
	if (fleet_orders[fleet_selected.id] && fleet_orders[fleet_selected.id].length > 0){
		var previous_order = fleet_orders[fleet_selected.id][fleet_orders[fleet_selected.id].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected.id].x , 'z':fleet_cache[fleet_selected.id].z};		
	}
	show_system_view_colonise(previous_order.x, previous_order.z);
}

function add_fleet_colonise_order(pid){
	if (!allow_actions()){
		return;
	}
	if (fleet_orders[fleet_selected.id] && fleet_orders[fleet_selected.id].length > 0){
		var previous_order = fleet_orders[fleet_selected.id][fleet_orders[fleet_selected.id].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected.id].x , 'z':fleet_cache[fleet_selected.id].z};
		fleet_orders[fleet_selected.id] = [];
	}
	var order = {'type':'colonise', "x":previous_order.x, "z":previous_order.z, "planet_id": pid};
	fleet_orders[fleet_selected.id].push(order);
	
	redraw_overlay();
}

function add_fleet_construct_order_dialog(){
	if (fleet_orders[fleet_selected.id] && fleet_orders[fleet_selected.id].length > 0){
		var previous_order = fleet_orders[fleet_selected.id][fleet_orders[fleet_selected.id].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected.id].x , 'z':fleet_cache[fleet_selected.id].z};		
	}
	show_construct(previous_order.x, previous_order.z);
}

function add_fleet_construct_order(uid){
	if (!allow_actions()){
		return;
	}
	if (fleet_orders[fleet_selected.id] && fleet_orders[fleet_selected.id].length > 0){
		var previous_order = fleet_orders[fleet_selected.id][fleet_orders[fleet_selected.id].length-1];
	}else {
		var previous_order = {'x':fleet_cache[fleet_selected.id].x , 'z':fleet_cache[fleet_selected.id].z};
		fleet_orders[fleet_selected.id] = [];
	}
	var order = {'type':'construct', "x":previous_order.x, "z":previous_order.z, "upgrade_id": uid};
	fleet_orders[fleet_selected.id].push(order);
	
	redraw_overlay();
}


function cancel_fleet_orders(){
	if (!allow_actions()){
		return;
	}
	fleet_orders[fleet_selected.id] = [];
	fleet_selected.orders = [];
	redraw_overlay();
}

function show_combat_comparison(fleet1, fleet2){
	// calculate comparison?
	var combat_comparison = {};
	combat_comparison.outgoing = fleet1.damage;
	combat_comparison.incoming = fleet2.damage;
	void_view.set("combat_comparison", combat_comparison)
}
function hide_combat_comparison(){
	void_view.set("combat_comparison", "")
}


function declare_war(player_id){
	player_orders.push({"type":"war", "target":player_id});
}

function select_tech(id){
	if (!allow_actions()){
		return;
	}
	if (!player.available_tech[id]){
		return false;
	}
	current_tech = id;
	player.current_tech = player.available_tech[id];
	void_view.update("player");
	update_interface();
	render_research();
}

function buy_queue_item(id){
	if (!allow_actions()){
		return;
	}
	player.credits_pool = player.credits_pool - 10;
	// update player resources
	// flag this queue item for speed up
	if (!system_orders[hex_selected.id]){
		system_orders[hex_selected.id] = {};
	}
	if (!system_orders[hex_selected.id][id]){
		system_orders[hex_selected.id][id] = {};
	}
	system_orders[hex_selected.id][id].purchased = true;	
	void_view.update("player");
}

function build_ship(ship_class_id){
	if (!allow_actions()){
		return;
	}
	var system = hex_map['x'+hex_selected.x+'z'+hex_selected.z].system;
	
	var ship_class = ship_class_cache[ship_class_id];
	
	var item = new Object();
	item.type = "ship";
	item.target_id = ship_class_id;
	item.progress = ship_class.work_required;
	if (system.production_per_turn){
		item.turns = Math.ceil(item.progress / system.production_per_turn);		
	}else {
		item.turns = "1000";
	}
	var d = new Date();
	item.id = Math.random().toString(36).substr(4)+hex_selected.x+hex_selected.z+system.build_queue.items.length;
	item.order = system.build_queue.items.length;
	//alert(system.build_queue.items.length);
	system.build_queue.items[system.build_queue.items.length] = item;
	
	if ($("#system_view").is(":visible")){
		//show_system_view(hex_selected.x, hex_selected.z);
	}
	//show_map_panel_system(hex_selected)
	if (!system_orders[system.id]){
		system_orders[system.id] = new Object();
	}
	system_orders[system.id][item.id] = item;
	
	void_view.update("system_view");
	void_view.update("hex_selected");
	//void_view.update("system_view.system");
	
	$(".custom_scroll").customScrollbar("resize", true);
	
}

function build_structure(structure_class_id){
	if (!allow_actions()){
		return;
	}
	var system = hex_map['x'+hex_selected.x+'z'+hex_selected.z].system;	
	var structure_class = structure_class_cache[structure_class_id];
	
	var item = new Object();
	item.type = "structure";
	item.target_id = structure_class_id;
	item.progress = structure_class.work_required;
	if (system.production_per_turn){
		item.turns = Math.ceil(item.progress / system.production_per_turn);
		
	}else {
		item.turns = "1000";
	}
	var d = new Date();
	item.id = Math.random().toString(36).substr(4)+hex_selected.x+hex_selected.z+system.build_queue.items.length;
	item.order = system.build_queue.items.length;
	//alert(system.build_queue.items.length);
	system.build_queue.items[system.build_queue.items.length] = item;
			
	if (!system_orders[system.id]){
		system_orders[system.id] = new Object();
	}
	system_orders[system.id][item.id] = item;
	if ($("#system_view").is(":visible")){
		//show_system_view(hex_selected.x, hex_selected.z);
	}
	
	var key_to_remove = null;
	$.each(system.available_structure_classes, function (key, value){
		if (value.id == structure_class_id){
			key_to_remove = key;			
		}
	});
	if (key_to_remove || key_to_remove === 0){
		system.available_structure_classes.splice(key_to_remove, 1);		
	}
	
	//show_map_panel_system(hex_selected);
	void_view.update("system_view");
	void_view.update("hex_selected");
}





// convert a mouseover or mouseclick event to hex coords and redraw the needed hexes
function click_to_hex(x, y, param, event){
	
	var coords = pixel_to_hex(x,y);
	//console.log(coords);
	if (param == "click"){		
		if (fleet_order_mode == true){
			
			add_fleet_order(fleet_order_start_hex, {'x':coords[0], 'z':coords[2]});
			if (event && event.ctrlKey){
				// if ctrl key pressed then waypoint mode
			}else {
				end_fleet_order_mode(fleet_selected.id);
			}
			
		}else {
			fleet_order_mode = false;
			
			hex_selected = get_hex(coords[0], coords[2]);

			hex_hover_history.push(hex_selected);
			if (hex_hover_history.length > 5){
				hex_hover_history.pop();
			}
			
			fleet_selected = null;
			if (hex_selected.your_fleets){
				$.each(hex_selected.your_fleets, function(){
					if (this.ships && this.ships){
						$.each(this.ships, function (){
							this.class = ship_class_cache[this.class_id];					
						});
					}
					fleet_selected = this;
				});		
			}
			enemy_fleet_selected = null;
			if (hex_selected.enemy_fleets){
				$.each(hex_selected.enemy_fleets, function(){
					if (this.ships && this.ships){
						$.each(this.ships, function (){
							this.class = ship_class_cache[this.class_id];					
						});
					}
					enemy_fleet_selected = this;
				});		
			}
			redraw_overlay();
			void_view.set("hex_selected", hex_selected);
			void_view.set("fleet_selected", fleet_selected);
			void_view.set("enemy_fleet_selected", enemy_fleet_selected);
			if(fleet_selected){
				void_view.set("fleet_selected.sub", false);
			}
			//show_map_panel_system(hex_selected);
			//console.log(hex_map['x'+coords[0]+'z'+coords[2]]);			
		}
		
	}else if (param == "ctrl_click"){
		
	}else if (param == "double_click"){		
		hex_selected = get_hex(coords[0], coords[2]);
		redraw_overlay();
		show_system_view(hex_selected.x, hex_selected.z);
	}else {
		if (hex_highlighted.x != coords[0] || hex_highlighted.z != coords[2]){
			hex_highlighted = get_hex(coords[0], coords[2]);
			//draw_map("galactic_map");
			if (hex_map['x'+coords[0]+'z'+coords[2]]){
				redraw_overlay();
			}
		}
	}
}

function start_game(){
	$.post("main.php",{"action":"start"},handle_start_game);
}
function handle_start_game(data){
	window.location = "main.html?player_id=1";
}

function join_game(){
	$.post("main.php",{"action":"join"},handle_join_game);
}
function handle_join_game(data){
	window.location = "main.html?player_id="+data.player.id;
}

function reset_game(){
	$.post("main.php"+location.search,{"action":"reset"},handle_end_turn);
}

function fetch_game_data(first){
	// call a function to the remote server to get all the game data.
	var url = "main.php";
	var params = {};
	if (location.search){
		url += location.search;
	}else {
		params.player_id = 0;
	}
	if (first){
		params.first = true;
	}
	$.get(url,params,handle_fetch_game_data);
}

function handle_fetch_game_data(data){
	if (data){
		
		// load the player data and game state to see if we are in lobby mode
		game_state = data.state;
		player = data.player;
		players = data.players;
		
		void_view.set("player", player);
		void_view.set("players", players);
		void_view.set("game_state", game_state);
						
		fleet_orders = new Object();
		hex_map = data.map.sectors;				
		void_view.set("hex_map", true);
		if (data.debug){
			//void_debug = data.debug;
		}
		
		if (data.logs){
			event_logs = data.logs;
			player.logs = data.logs;
			player.logs = parse_logs(player.logs);
		}
		if (data.planet_classes){
			planet_class_cache = data.planet_classes;
			void_view.set("planet_class_cache", planet_class_cache);
		}
		if (data.ship_classes){
			ship_class_cache = data.ship_classes;
			void_view.set("ship_class_cache", ship_class_cache);
			//var html = fetch_template('ship_class_tooltip_templates', ship_class_cache);		
			//$("#tooltip_block").append(html);						
		}
		if (data.structure_classes){
			structure_class_cache = data.structure_classes;
			void_view.set("structure_class_cache", structure_class_cache);
		}
		if (data.sector_classes){
			sector_class_cache = data.sector_classes;
			void_view.set("sector_class_cache",sector_class_cache);
		}
		
		if (data.upgrade_classes){
			upgrade_class_cache = data.upgrade_classes;
			void_view.set("upgrade_class_cache",upgrade_class_cache);
		}
		if (data.power_classes){
			power_class_cache = data.power_classes;
			void_view.set("power_class_cache",power_class_cache);
		}
		if (data.tech_tree){
			tech_tree = data.tech_tree;
		}
				
		
		void_view.update();				
		
		if (!first_loading_complete){
			preload_images(data, game_update);
			first_loading_complete = true;
		}else {
			game_update(data);
		}
	}
}

function game_update(data){
	initialize_map(data.map.map_width, data.map.map_height);
	update_interface();
	redraw_overlay();
	
	if (hex_selected){
		hex_selected = get_hex(hex_selected.x, hex_selected.z);
		void_view.set("hex_selected", hex_selected);
	}
	if (fleet_selected){
		fleet_selected = fleet_cache[fleet_selected.id];
		fleet_selected.sub = false;
		void_view.set("fleet_selected", fleet_selected);
	}
	
	if (!player.done){
		$("#end_turn_button").html('<img src="images/ajax-loader.png" style="visibility: hidden;"> End Turn ');
		$("#end_turn_button").attr("disabled", false);
	}else {
		status_check();
		$("#end_turn_button").html('+ End Turn ');
		$("#end_turn_button").attr("disabled", true);
	}
	$('#main_loading').hide();
}


function status_check(){
	$.get("main.php"+location.search,{'action':'status'},handle_status_check);	
}
function handle_status_check(data){
	if (data.debug){
		void_debug = data.debug;
	}
	var waiting_for_players = [];
	if (data && data.player){
		if (data.players){
			
			$.each(data.players, function(key, value){
				if (value.done == false && value.id != player.id){
					waiting_for_players.push(value);
				}
			});			
		}
		
		if (data.player.done == false){
			fetch_game_data();
			return;
		}
		$("#end_turn_button").html('+ End Turn ');
		$("#end_turn_button").attr("disabled", true);
	}
	void_view.set("waiting_for_players", waiting_for_players);
	
	setTimeout(status_check, 4000);
}

function end_turn(){
	// send all committed orders to the server
	$("#end_turn_button").attr("disabled", true);
	$("#end_turn_button").html('<img src="images/ajax-loader.png"> End Turn ');
	$.post("main.php"+location.search,{'action':'end_turn', 'fleet_transfers':changes.fleet_transfers, 'player_orders': player_orders, 'fleet_orders':fleet_orders, 'system_orders':system_orders, 'current_tech':current_tech},handle_end_turn);
	system_orders = new Object();
	changes.fleet_transfers = {};
	current_tech = null;
}

function handle_end_turn(data){
	//console.log(data);
	handle_status_check(data);
}

