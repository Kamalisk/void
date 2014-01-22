
var hex_map = new Object();
var hex_size = 60; 
var hex_selected = {};
var hex_highlighted = {};
var hex_height = hex_size * 2;
var hex_width = Math.sqrt(3)/2 * hex_height;
var hex_vert = 3 / 4 * hex_height;
var hex_hover_history = new Array();

var map_context_menu_type;
var map_right_click;

var hex_neighbors = [
	[0,  -1],  [+1, -1],  [ +1, 0],
	[0,  +1],  [-1, +1],  [ -1, 0] 
];

var map_scale = 0.5;
var map_height = 1400;
var map_width = 1400;
var map_viewport = new Object();	

var player;
var players;

var hex_path;

var planet_class_cache;
var sector_class_cache;
var ship_class_cache;
var tech_tree;

var fleet_cache = {};

var current_tech;

var mouse_down_event;
var scroll_in_progress;
var map_scroll_offset;

var panel_view = "";

var fleet_orders = new Object();
var fleet_order_mode = false;
var fleet_order_start_hex = new Object();

var fleet_selected;

var system_orders = new Object();

var first_load = false;

var waiting_for_players = false;

$(document).ready(function (){
	Handlebars.registerHelper('compare', function (lvalue, operator, rvalue, options) {

    var operators, result;
    
    if (arguments.length < 3) {
        throw new Error("Handlerbars Helper 'compare' needs 2 parameters");
    }
    
    if (options === undefined) {
        options = rvalue;
        rvalue = operator;
        operator = "===";
    }
    
    operators = {
        '==': function (l, r) { return l == r; },
        '===': function (l, r) { return l === r; },
        '!=': function (l, r) { return l != r; },
        '!==': function (l, r) { return l !== r; },
        '<': function (l, r) { return l < r; },
        '>': function (l, r) { return l > r; },
        '<=': function (l, r) { return l <= r; },
        '>=': function (l, r) { return l >= r; },
        'typeof': function (l, r) { return typeof l == r; }
    };
    
    if (!operators[operator]) {
        throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);
    }
    
    result = operators[operator](lvalue, rvalue);
    
    if (result) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }

	});
	fetch_game_data();
	// fetch map data from the server
	
	// fetch all other game data they need and store it in a javascript object
	
	// this data is used so it hardly ever has to request data from the server other than between turns
	
	//$('#main_loading').hide();
});


function initialize_map(width, height){
	
	// figure out height and width of map based on the number of hexes
	map_height = (hex_height * 1.5 * Math.floor(height / 2)) + hex_height ;
	map_width = hex_width * width + (hex_width*0.7);
	
	var map_ratio = map_width / map_height;
	
	//map_height = map_height * map_scale;
	//map_width = map_width * map_scale;
	
	/*
	$('#galactic_map').attr("width",map_width);
	$('#galactic_map').attr("height",map_height);
	$('#galactic_map').css("width",map_width*map_scale);
	$('#galactic_map').css("height",map_height*map_scale);
	
	$('#galactic_map_overlay').attr("width",map_width);
	$('#galactic_map_overlay').attr("height",map_height);
	$('#galactic_map_overlay').css("width",map_width*map_scale);
	$('#galactic_map_overlay').css("height",map_height*map_scale);
	
	$('#galactic_map_objects').attr("width",map_width);
	$('#galactic_map_objects').attr("height",map_height);
	$('#galactic_map_objects').css("width",map_width*map_scale);
	$('#galactic_map_objects').css("height",map_height*map_scale);
	*/
	$('#galactic_map_mini').attr("width",map_width);
	$('#galactic_map_mini').attr("height",map_height);
	$('#galactic_map_mini').css("width",200*map_ratio);
	$('#galactic_map_mini').css("height","200");
	
	$('#galactic_map_mini_overlay').attr("width",map_width);
	$('#galactic_map_mini_overlay').attr("height",map_height);
	$('#galactic_map_mini_overlay').css("width",200*map_ratio);
	$('#galactic_map_mini_overlay').css("height","200");
	
	
	// calculate current view port
	//map_viewport.startX = $('#galactic_map_container').scrollLeft();
	//map_viewport.startY = $('#galactic_map_container').scrollTop();
	map_viewport.endX = map_viewport.startX + $('#galactic_map_container').width;
	map_viewport.endY = map_viewport.startY + $('#galactic_map_container').height;
	map_viewport.width = $('#galactic_map_container').width();
	map_viewport.height = $('#galactic_map_container').height();
	map_viewport.percentX = map_viewport.startX / map_viewport.width;
	map_viewport.percentY = map_viewport.startY / map_viewport.height;
	map_viewport.percentWidth = map_width / map_viewport.width;
	map_viewport.percentHeight = map_height / map_viewport.height;
	map_viewport.factorWidth = map_width / 200;
	map_viewport.factorHeight = map_height / 200;
	
	map_context_menu_type = "default";
	map_right_click = "";
	
	draw_minimap_overlay();
	
	//$('#galactic_map_container').bind('scroll',function(event){
		//draw_minimap_overlay();
	//});
	
	//alert(map_viewport.startX);
	
	var map_buffer = 400;
	
	map_scroll_offset = {"x":map_buffer, "y":map_buffer};
	
	$("#galactic_map_hexes").css({"top": -map_buffer , "left": -map_buffer });
	$("#galactic_map_overlay").css({"top": -map_buffer , "left": -map_buffer });
	$("#galactic_map_objects").css({"top": -map_buffer , "left": -map_buffer });
	
	$('#galactic_map_overlay').attr("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_overlay').attr("height",$('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_overlay').css("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_overlay').css("height",$('#galactic_map_container').height() + map_buffer*2);
	
	$('#galactic_map_objects').attr("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_objects').attr("height",$('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_objects').css("width",$('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_objects').css("height",$('#galactic_map_container').height() + map_buffer*2);
	
	$('#galactic_map_hexes').attr("width", $('#galactic_map_container').width() + map_buffer*2);
	$('#galactic_map_hexes').attr("height", $('#galactic_map_container').height() + map_buffer*2);
	$('#galactic_map_hexes').css("width", $('#galactic_map_container').width() + map_buffer*2 );
	$('#galactic_map_hexes').css("height", $('#galactic_map_container').height() + map_buffer*2 );
	
	draw_map("galactic_map", true);
	
	if (first_load){
		return;
	}	
	
	first_load = true;
	scroll_in_progress = false;	
	
	var view_port_width = $("#galactic_map_container").width();
	var map_surface_width = $("#galactic_map_hexes").width();
	var view_port_height = $("#galactic_map_container").height();
	var map_surface_height = $("#galactic_map_hexes").height();
	
	
	
	
	$('#galactic_map_dragger').bind('mousedown',function(event){
		mouse_down_event = event;
		scroll_in_progress = {
			"x": event.pageX, "y": event.pageY,
			"target_x": $("#galactic_map_hexes").position().left, "target_y": $("#galactic_map_hexes").position().top
		};		
		event.preventDefault();
	});
	$('#galactic_map_dragger').bind('mouseup',function(event){
		scroll_in_progress = false;
		if (Math.abs(mouse_down_event.pageX - event.pageX) < 10 && Math.abs(mouse_down_event.pageY - event.pageY) < 10 && event.button == 0){
			var offset_x = $("#galactic_map_hexes").position().left + map_scroll_offset.x;
			var offset_y = $("#galactic_map_hexes").position().top  + map_scroll_offset.y;
			var click_x = event.pageX - $(this).offset().left;// + offset_x/hex_size/2;
			var click_y = event.pageY - $(this).offset().top;// + offset_y/hex_size/2;
			//console.log(offset_x);
			click_to_hex(click_x - offset_x , click_y - offset_y , 'click', event);
			
		}
		mouse_down_event = null;
	});
	
	$('#galactic_map_dragger').bind('wheel',function(event){
		console.log(event);
		map_scale = map_scale * 0.8;
		draw_map("galactic_map");
		redraw_overlay();
	});
	
	$('#galactic_map_dragger').bind('mousemove',function(event){
		
		//console.log(event);
		if (scroll_in_progress){
			// move the map!			
			var x = (event.pageX  - scroll_in_progress.x);
			var y = (event.pageY  - scroll_in_progress.y);
			
			var new_x = scroll_in_progress.target_x + x;
			var new_y = scroll_in_progress.target_y + y;

			var oldX = $("#galactic_map_hexes").css("left");
			var oldY = $("#galactic_map_hexes").css("top");

			$("#galactic_map_hexes").css({"top": new_y , "left": new_x });
			$("#galactic_map_overlay").css({"top": new_y , "left": new_x });
			$("#galactic_map_objects").css({"top": new_y , "left": new_x });
			
			if (new_x < (view_port_width - map_surface_width ) || new_x > 0 || new_y < (view_port_height - map_surface_height ) || new_y > 0){
				map_scroll_offset.x = map_scroll_offset.x + (new_x + map_buffer)/map_scale;
				map_scroll_offset.y = map_scroll_offset.y + (new_y + map_buffer)/map_scale;
				$("#galactic_map_overlay").css({"top": -map_buffer , "left": -map_buffer });
				$("#galactic_map_hexes").css({"top": -map_buffer , "left": -map_buffer });
				$("#galactic_map_objects").css({"top": -map_buffer , "left": -map_buffer });
				scroll_in_progress = {
					"x": event.pageX, "y": event.pageY,
					"target_x": $("#galactic_map_hexes").position().left, "target_y": $("#galactic_map_hexes").position().top
				};
				
				draw_map("galactic_map");
				redraw_overlay();
			}
			
			event.preventDefault();
			//scroll_in_progress = {"x": event.pageX, "y": event.pageY };
		//}
		}
	});
	$('#galactic_map_dragger').bind('mouseout',function(event){
		scroll_in_progress = false;
	});
	 
 	$('#galactic_map_overlay').bind('mousedown',function(event){
		mouse_down_event = event;
		//click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'click', event);
	});
	$('#galactic_map_overlay').bind('mouseup',function(event){
			if (Math.abs(mouse_down_event.pageX - event.pageX) < 10 && Math.abs(mouse_down_event.pageY - event.pageY) < 10 && event.button == 0){
				click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'click', event);
			}
			mouse_down_event = null;
	});
 
	$('#galactic_map_overlay').bind('click',function(event){
			//click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'click', event);
	});
	$('#galactic_map_overlay').bind('mousemove',function(event){
			
	});
	
	$('#galactic_map_overlay').bind('dblclick',function(event){
			click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'double_click', event);
	});
	
	// set up click and drag for mini map
	$('#galactic_map_mini_overlay').bind('mousedown',function(event){
			start_minimap_drag(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top);
	});
	$('#galactic_map_mini_overlay').bind('mouseup',function(event){
			end_minimap_drag(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top);
	});
	$('#galactic_map_mini_overlay').bind('mousemove',function(event){
			minimap_move((event.pageX - $(this).offset().left)  , (event.pageY - $(this).offset().top));
	});
	
	$('#galactic_map_overlay').bind('contextmenu',function(event){
			if (map_right_click && map_right_click != ""){
				click_to_hex(event.pageX - $(this).offset().left, event.pageY - $(this).offset().top, 'click', event);
			}else if (show_context_menu('galactic_map_context_menu', event.pageX, event.pageY)){
				event.preventDefault();
			}
			return false;
	});
	$('body').bind('click',function(event){
			$('#galactic_map_context_menu').hide();
	});
	
	$('body').bind('keypress',function(event){
			// handle shortcut keys
	});
	
	
	// Set drag scroll on first descendant of class dragger on both selected elements
	//$('#galactic_map_container').dragscrollable({dragSelector: '.dragger:first', acceptPropagatedEvent: false});
	
	// Set drag scroll on first descendant of class dragger on both selected elements
	//$('#galactic_map_mini_container').dragscrollable({dragSelector: '.dragger:first', acceptPropagatedEvent: false});
	
	// center the map on the users home system
	if (player && player.home){
		var coords = hex_to_pixel(player.home);
		
		//$('#galactic_map_container').scrollLeft((coords.x * map_scale - ($('#galactic_map_container').width()/2)));
		//$('#galactic_map_container').scrollTop((coords.y  * map_scale - ($('#galactic_map_container').height()/2)));
	}
	
}

function map_zoom(out){
	if (out){
		map_scale = map_scale * 2;
	}else {
		map_scale = map_scale / 2;
	}
	$('#galactic_map').css("width",map_width*map_scale);
	$('#galactic_map').css("height",map_height*map_scale);
	$('#galactic_map_overlay').css("width",map_width*map_scale);
	$('#galactic_map_overlay').css("height",map_height*map_scale);
	$('#galactic_map_objects').css("width",map_width*map_scale);
	$('#galactic_map_objects').css("height",map_height*map_scale);
	
	draw_map("galactic_map");
}


function update_interface(){
	if (player){
		if (player.current_tech && player.research_per_turn){
			player.research_time = Math.ceil(player.current_tech.progress / player.research_per_turn);
		}
		append_template("player_resources_template", player, "player_resources");
	}
	if (panel_view == "system" && hex_selected && hex_selected.x){
		show_map_panel_system(hex_selected);	
	}
	render_research();
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
		//$('#galactic_map_container').scrollLeft(x*map_viewport.factorWidth*map_scale - ($('#galactic_map_container').width() / 2));
		//$('#galactic_map_container').scrollTop(y*map_viewport.factorHeight*map_scale - ($('#galactic_map_container').height() / 2));
		draw_minimap_overlay();
	}
}


function update_map(){
	
}




function fetch_template(id, data){
	var source = $('#'+id).html();
	var template = Handlebars.compile(source);
	Handlebars.registerPartial("planet", $("#planet_template").html());
	var html = template(data);
	return html;
}

function append_template(template_id, data, element_id){
	$("#"+element_id).hide();
	$("#"+element_id).empty();
	var html = fetch_template(template_id, data);	
	$("#"+element_id).append(html);
	
	$('.tooltip-template', $("#"+element_id)).each(function() {
		var selector = '#' + $(this).data('tooltip-id');		
		$(this).tooltipsy({
			content: $(selector).html(),
			delay : 1000,
			alignTo: 'element',
    	offset: [0, 1]
		});
	});
	$('.tooltip-title', $("#"+element_id)).each(function() {
		$(this).tooltipsy({
			content: $(this).attr('title'),
			delay : 1000,
			alignTo: 'element',
    	offset: [0, 1]
		});
	});
	$("#"+element_id).show();
}

function change_view(view_id){
	$('.view').hide();
	if (view_id == "research"){
		render_research();
	}
	
	$('#'+view_id+'_view').show();
}


function render_research(){
	var temp_obj = new Object();
	temp_obj['tech_tree'] = tech_tree;
	temp_obj['player'] = player;
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
	});
	append_template("tech_tree_template",temp_obj,"tech_tree_items");
}

function show_map_panel_system(hex){
	panel_view = "system";
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
			this.target = ship_class_cache[this.target_id];
		});
	}
	
	if (hex.your_fleets){
		$.each(hex.your_fleets, function(){
			if (this.ships && this.ships){
				$.each(this.ships, function (){
					this.class = ship_class_cache[this.class_id];
				});
			}
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
	panel_view = "fleet";
	$("#galactic_map_panel_content").empty();
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
	append_template('galactic_map_panel_fleet_template',fleet, "galactic_map_panel_content");
	map_context_menu_type = "default";
}

function show_system_view(x, z){	
	$("#system_view_window").empty();		
	var hex = hex_map['x'+x+'z'+z];
	if (hex.system){
		$.each(hex.system.planets, function (){
			this.class = planet_class_cache[this.class_id];			
		});
	}
	if (hex.system && hex.system.build_queue && hex.system.build_queue.items){
		$.each(hex.system.build_queue.items, function (){
			this.target = ship_class_cache[this.target_id];
		});
	}
	
	var html = fetch_template('system_view_template',hex);		
	$("#system_view_window").append(html);
	$("#system_view_window").show();			
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


function context_menu(event, params){
	show_context_menu('galactic_map_context_menu', event.pageX, event.pageY, 'system_planet_menu');
	event.preventDefault();
}

function start_fleet_order_mode(id){
	// get the current fleet details 
	var fleet = fleet_cache[id];
	
	// set the hex highlight path start points
	fleet_order_mode = true;
	
	map_context_menu_type = "default";
	map_right_click = "fleet_move";

	if (fleet_orders[id]){
		fleet_order_start_hex = fleet_orders[id][fleet_orders[id].length-1];
	}else {
		fleet_order_start_hex = get_hex(fleet.x,fleet.z);
	}
	
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
	
	var path = get_path_between_hexes(start, end);
	if (path){
		for (var i = 0; i < path.length ; i++){
			if (path[i]){
				if (!fleet_orders[fleet_selected]){
					fleet_orders[fleet_selected] = new Array();
				}
				var order = {'type':'move', 'x':path[i].x, 'z':path[i].z}
				if (fleet_orders[fleet_selected].length < 3){
					order.this_turn = true;
				}
				fleet_orders[fleet_selected][fleet_orders[fleet_selected].length] = order;
			}
		}
	}
	fleet_order_start_hex = end;
}

function add_fleet_colonise_order(){
	var previous_order = fleet_orders[fleet_selected][fleet_orders[fleet_selected].length-1];
	var order = {'type':'colonise', "x":previous_order.x, "z":previous_order.z};
	fleet_orders[fleet_selected][fleet_orders[fleet_selected].length] = order;
	console.log(fleet_orders);
	redraw_overlay();
}

function cancel_fleet_orders(){
	fleet_orders[fleet_selected] = new Array();
}

function select_tech(id){
	if (!player.available_tech[id]){
		return false;
	}
	current_tech = id;
	player.current_tech = player.available_tech[id];
	update_interface();
	render_research();
}


function build_ship(ship_class_id){
	var system = hex_map['x'+hex_selected.x+'z'+hex_selected.z].system;
	
	var ship_class = ship_class_cache[ship_class_id];
	
	var item = new Object();
	item.type = "build";
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
		show_system_view(hex_selected.x, hex_selected.z);
	}
	show_map_panel_system(hex_selected)
	if (!system_orders[system.id]){
		system_orders[system.id] = new Object();
	}
	system_orders[system.id][item.id] = item;
}


function get_hex(x, z){
	return hex_map["x"+x+"z"+z];
}



/* galactic map drawing funtions */




// convert a mouseover or mouseclick event to hex coords and redraw the needed hexes
function click_to_hex(x, y, param, event){
	
	var coords = pixel_to_hex(x,y);
	console.log(coords);
	if (param == "click"){
		if (panel_view == "fleet" && fleet_order_mode == true){
			
			add_fleet_order(fleet_order_start_hex, {'x':coords[0], 'z':coords[2]});
			if (event && event.ctrlKey){
				// if ctrl key pressed then waypoint mode
			}else {
				end_fleet_order_mode(fleet_selected);
			}
			
		}else {
			fleet_order_mode = false;
			if (hex_selected.x != coords[0] || hex_selected.z != coords[2]){
				hex_selected.x = coords[0];
				hex_selected.z = coords[2];
				hex_hover_history.push(hex_selected);
				if (hex_hover_history.length > 5){
					hex_hover_history.pop();
				}
				redraw_overlay();
				show_map_panel_system(hex_selected);
			}
		}
	}else if (param == "ctrl_click"){
		
	}else if (param == "double_click"){
		if (fleet_order_mode){
			end_fleet_order_mode(fleet_selected);
		}
	}else {
		if (hex_highlighted.x != coords[0] || hex_highlighted.z != coords[2]){
			hex_highlighted.x = coords[0];
			hex_highlighted.z = coords[2];
			//draw_map("galactic_map");
			if (hex_map['x'+coords[0]+'z'+coords[2]]){
				redraw_overlay();
			}
		}
	}
}





function galactic_map_panel_system_data($scope){
	$scope.name = function (){
		return "hey";
	}
}

function reset_game(){
	$.get("main.php"+location.search,{"action":"reset"},handle_end_turn);
}

function fetch_game_data(){
	// call a function to the remote server to get all the game data.
	if (location.search){		
		$.get("main.php"+location.search,{},handle_fetch_game_data);
	}else {
		$.get("main.php",{'player_id':1},handle_fetch_game_data);
	}
	// for now fake the data
	//var data = create_fake_game_data();
	//handle_fetch_game_data(data);
}
function create_fake_game_data(){
	var data = new Object();
	var width = 18;
	var height = 18;
	var hex_map2 = new Object();
	for (var z = 0; z < height; z++){
		for (var x = -Math.floor(z/2); x < width - Math.floor(z/2); x++){
			
			hex_map2['x'+x+'z'+z] = {'x':x,'z':z};
			if (x < 3 || x > 10 || z < 5 || z > 16){
				hex_map2['x'+x+'z'+z].unknown = 1;
			}else {
				if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].star = 1;
					hex_map2['x'+x+'z'+z].planets = new Array();
					var system_size = 0;
					var max_system_size = 0;
					for (var i = 0; i < Math.random()*6+1; i++){
						hex_map2['x'+x+'z'+z].planets[i] = {
							'name' : 'Planet '+i,
							'class' : 'M',
							'population' : 3
						}
						system_size += 3;
						max_system_size += 5;
					}
					hex_map2['x'+x+'z'+z].size = system_size;
					hex_map2['x'+x+'z'+z].max_size = max_system_size;
				}
				if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].fleet = 1;
					hex_map2['x'+x+'z'+z].fleets = new Array();
					for (var i = 1; i < Math.random()*2+1; i++){
						hex_map2['x'+x+'z'+z].fleets[i] = new Object();
					}
				}
				if (x > 5 && x < 10 && z > 5 && z < 10){
					hex_map2['x'+x+'z'+z].friendly = 1;					
				}else if (Math.random() < 0.2){
					hex_map2['x'+x+'z'+z].enemy = 1;
				}
			} 
		}
	}
	data.map = hex_map2;
	data.map_width = width;
	data.map_height = height;
	
	// the player object for the logged in players
	data.player = new Object();
	data.player.name = 'Kam';
	data.player.home = {'x':7, 'z': 8};
	data.player.resources = 'No Resources!';
	data.players = new Object();
	
	data.tech = new Object();
	data.ships = new Object();
	data.planets = new Object();
	
	
	return data;
}

function handle_fetch_game_data(data){
	if (data){		
		fleet_orders = new Object();
		hex_map = data.map.sectors;
		player = data.player;
		players = data.players;
		planet_class_cache = data.planet_classes;
		ship_class_cache = data.ship_classes;
		sector_class_cache = data.sector_classes;
		tech_tree = data.tech_tree;
		initialize_map(data.map.map_width, data.map.map_height);
		update_interface();
		redraw_overlay();
		if (!player.done){
			$("#end_turn_button").attr("disabled", false);
		}else {
			status_check();
			$("#end_turn_button").attr("disabled", true);
		}
		$('#main_loading').hide();
	}
}	

function status_check(){
	$.get("main.php"+location.search,{'action':'status'},handle_status_check);	
}
function handle_status_check(data){
	if (data && data.player){
		if (data.player.done == false){
			fetch_game_data();
			return;
		}
	}
	setTimeout(status_check, 4000);
}

function end_turn(){
	// send all committed orders to the server
	$("#end_turn_button").attr("disabled", true);
	$.post("main.php"+location.search,{'action':'end_turn', 'fleet_orders':fleet_orders, 'system_orders':system_orders, 'current_tech':current_tech},handle_end_turn);
	system_orders = new Object();
	current_tech = null;
}

function handle_end_turn(data){
	handle_status_check(data);
}


//
// galactic map drawing functions
//

function draw_hex(canvas, x, y, size, color, fill, layer){
	var options = {
		strokeStyle: color,
	  strokeWidth: 2,
	  x: x, y: y,
	  radius: size,
	  sides: 6,
	  rotate : 30
	};
	if (fill != "none"){
		options['fillStyle'] = fill; 
	}
	if (layer){
		options['layer'] = true; 
		options['name'] = layer; 
	}
	$(canvas).drawPolygon(options);
}

function redraw_overlay(){
	
	var canvas = document.getElementById("galactic_map_overlay");
	var overlay_ctx = canvas.getContext('2d');
	overlay_ctx.save();
	overlay_ctx.scale(map_scale,map_scale);
	$(canvas).clearCanvas();
	if (hex_highlighted && hex_map['x'+hex_highlighted.x+'z'+hex_highlighted.z]){
		var coords = hex_to_pixel(hex_highlighted);
		draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "none");
	}
	if (hex_selected && hex_map['x'+hex_selected.x+'z'+hex_selected.z]){
		var coords = hex_to_pixel(hex_selected);
		draw_hex(canvas, coords.x,  coords.y, hex_size-1, "red", "none");
	}
	
	var fleet_order_turn_counter = 0;
	var fleet_order_movement_counter = 0;
	// draw the current fleet orders
	if (panel_view == "fleet" && fleet_selected && fleet_orders && fleet_orders[fleet_selected]){
		
			$.each(fleet_orders[fleet_selected], function (key2, value){
				if (value.type == "move"){
					var coords = hex_to_pixel(value);
					fleet_order_movement_counter++;
					if(fleet_order_movement_counter > fleet_cache[fleet_selected].movement_points){
						fleet_order_movement_counter = 0;
						fleet_order_turn_counter++;
						//console.log(fleet_cache[fleet_selected]);
					}
					if (fleet_order_turn_counter <= 0){
						draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "rgba(240, 150, 40, 0.2)");
					}else {
						draw_hex(canvas, coords.x,  coords.y, hex_size, "yellow", "rgba(240, 240, 40, 0.2)");
					}
				}else if (value.type == "colonise"){
					var coords = hex_to_pixel(value);
					draw_hex(canvas, coords.x,  coords.y, hex_size-5, "pink", "rgba(240, 150, 40, 0.2)");
				}
			});
	
	}
	
	if (panel_view == "fleet" && fleet_selected && fleet_order_mode && fleet_order_start_hex && hex_highlighted && get_hex(hex_highlighted.x,hex_highlighted.z) ){
		var path = get_path_between_hexes(fleet_order_start_hex, hex_highlighted);
		//console.log(hex_highlighted);
		//var path = false;
		if (path){
			for (var i = 0; i < path.length; i++){
				if (path[i]){
					var coords = hex_to_pixel(path[i]);
					
					fleet_order_movement_counter++;
					if(fleet_order_movement_counter > fleet_cache[fleet_selected].movement_points){
						fleet_order_movement_counter = 0;
						fleet_order_turn_counter++;
						console.log(fleet_cache[fleet_selected]);
					}
					
					if (fleet_order_turn_counter <= 0){
						draw_hex(canvas, coords.x,  coords.y, hex_size, "orange", "rgba(255, 150, 40, 0.5)");
					}else {
						draw_hex(canvas, coords.x,  coords.y, hex_size, "yellow", "rgba(255, 255, 40, 0.5)");
					}

				}
			}
		}
	}
	
	overlay_ctx.restore();
	
}


function draw_minimap_overlay(){
	
	$("#galactic_map_mini_overlay").clearCanvas();
	$("#galactic_map_mini_overlay").drawRect({
	  'x': $('#galactic_map_container').scrollLeft() / map_scale, 'y': $('#galactic_map_container').scrollTop() / map_scale,
	  width: $('#galactic_map_container').width() / map_scale,
	  height: $('#galactic_map_container').height() / map_scale,
	  strokeStyle : "white",
	  strokeWidth : 30,
	  fromCenter: false
	});
	
}



function redraw_map_section(x, y){
	
	var canvas_overlay = document.getElementById("galactic_map_overlay");
	//$(canvas_overlay).clearCanvas();
	var canvas = document.getElementById("galactic_map");
	var coords = pixel_to_hex(x, y);
	var hex = hex_map['x'+coords[0]+'z'+coords[2]];
	
	$(canvas).clearCanvas({
	    x: x, y: y,
	    width: hex_size*2,
	    height: hex_size*2
	});
	
	for (var direction = 0; direction < 6; direction++){
		var adj_hex = get_adjacent_hex(hex, direction);
		var i = adj_hex.x;
		var j = adj_hex.z;
		var x = hex_size * Math.sqrt(3) * (i + j/2);
		var y = hex_size * 3/2 * j;
		draw_hex(canvas, x+hex_size,  y+hex_size, hex_size, "white", "none");
	}
	var i = hex.x;
	var j = hex.z;
	var x = hex_size * Math.sqrt(3) * (i + j/2);
	var y = hex_size * 3/2 * j;
	draw_hex(canvas, x+hex_size,  y+hex_size, hex_size, "orange", "none");

}


function draw_map(map_id, first){

	var canvas = document.getElementById(map_id+"_hexes");
	var canvas_overlay = document.getElementById(map_id+"_overlay");
	var canvas_objects = document.getElementById(map_id+"_objects");

	//$(canvas_overlay).clearCanvas();
	$(canvas_objects).clearCanvas();
	$(canvas).clearCanvas();
	var ctx = canvas.getContext('2d');
	var objects_ctx = canvas_objects.getContext('2d');
	var overlay_ctx = canvas_overlay.getContext('2d');
	ctx.save();
	objects_ctx.save();
	
	var mini_canvas = $('#galactic_map_mini');
	var mini_canvas_overlay = $('#galactic_map_mini_overlay');
	
	ctx.scale(map_scale,map_scale);
	objects_ctx.scale(map_scale,map_scale);
	
	
	for(key in hex_map){
		var hex = hex_map[key];
		// first pre-compute the pixel coordinates of the hexes to save computing them lots later
		var coords = hex_to_pixel(hex);
		hex.pixel_x = coords.x;
		hex.pixel_y = coords.y;
		draw_map_tile(canvas, hex);
		if (first){
			draw_map_tile(mini_canvas, hex, true);
		}
	}
	
	for(key in hex_map){
		var hex = hex_map[key];
		draw_tile_overlay(canvas, hex);
		if (first){
			draw_tile_overlay(mini_canvas, hex, true);
		}
	}
	
	// draw objects
	for(key in hex_map){
		var hex = hex_map[key];
		if (hex.star){
			$(canvas_objects).drawImage({
			  source: "images/default.png",
			  x: hex.pixel_x, y: hex.pixel_y
			});
		}
		if (hex.your_fleets && hex.your_fleets.length > 0){
			
			// really needs to be temporary
			$.each(hex.your_fleets, function (){
				fleet_cache[this.id]  = this;
			});
			
			$(canvas_objects).drawImage({
			  source: "images/fleet_f.png",
			  x: hex.pixel_x-20, y: hex.pixel_y-25
			});
			
			if (hex.your_fleets[0].orders.length > 0){				
				fleet_orders[hex.your_fleets[0].id] = hex.your_fleets[0].orders;
			}
		}
		if (hex.enemy_fleets.length > 0){
			$(canvas_objects).drawImage({
			  source: "images/fleet_e.png",
			  x: hex.pixel_x+20, y: hex.pixel_y-25
			});
		}
		if (hex.system && hex.system.population){
			$(canvas_objects).drawText({
			  fillStyle: "black",
			  strokeStyle: "black",
			  strokeWidth: 2,
			  x: hex.pixel_x, y: hex.pixel_y,
			  fontSize: "14pt",
			  fontFamily: "Verdana, sans-serif",
			  text: hex.system.population
			});
			if (hex.system.owner){
				$(canvas_objects).drawEllipse({
				  strokeStyle : players[hex.system.owner].color.border, 
				  strokeWidth : 4,
				  x: hex.pixel_x, y: hex.pixel_y,
				  width: 32, height: 32
				});
			}
		}
	}
	
	ctx.restore();
	objects_ctx.restore();
}

function draw_tile_overlay(canvas, map_tile, small){
	if (map_tile.friendly){
		if (small){
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", "rgba(40,255,40,1)");
		}else {
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(70,255,70,0.3)");
			//draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "friendly");
		}
	}else if (map_tile.enemy){
		if (small){
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", "rgba(165,40,40,1)");
		}else {
			//draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(205,70,70,0.3)");
			//draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "enemy");
		}
	}
	
	if (players[map_tile.owner]){
		if (small){
			draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size-5, "none", players[map_tile.owner].color.border);
		}else {
			draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", players[map_tile.owner].color.background);
			if (map_tile.fog){
				draw_hex(canvas, map_tile.pixel_x,  map_tile.pixel_y, hex_size, "rgba(0,0,0,0)", "rgba(80,80,80,0.4)");
			}
			draw_hex_borders(canvas, map_tile.pixel_x, map_tile.pixel_y, map_tile, "friendly");
		}
	}
	
}


function draw_map_tile(canvas, map_tile, small){

	var x = map_tile.pixel_x;
	var y = map_tile.pixel_y;
	
	if (map_tile.unknown){
		if (small){
			//draw_hex(canvas, x,  y, hex_size+1, "none", "#3f3f3f");
		}else {
			draw_hex(canvas, x,  y, hex_size, "rgba(0,0,0,0.1)", "rgba(156,156,156,0.2)");
		}
	}else {
		if (!small){
			if (map_tile.type == "asteroid"){
				$(canvas).drawImage({
				  source: "images/asteroids.png",
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: sector_class_cache[map_tile.class_id].image,
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: sector_class_cache[map_tile.class_id].image,
				  x: x, y: y
				});
			}else if (map_tile.type == "nebula"){
				$(canvas).drawImage({
				  source: "images/nebula_c.png",
				  x: x, y: y
				});
			}
			if (map_tile.fog){
				draw_hex(canvas, x,  y, hex_size, "rgba(150,150,150,0.0)", "rgba(180,180,180,0.3)");
			}else {
				draw_hex(canvas, x,  y, hex_size, "rgba(34,34,34,0.5)", "none");
			}
		}else {			
			draw_hex(canvas, x,  y, hex_size, "none", "black");
			if (map_tile.fog){
				draw_hex(canvas, x,  y, hex_size, "rgba(150,150,150,0.0)", "rgba(180,180,180,0.5)");
			}
		}
		
	}
}


function draw_hex_borders(canvas, x, y, map_tile, property){

	
	var stroke_color = players[map_tile.owner].color.border;
	//var stroke_color = "#fff";
	var adjacent_tile = get_adjacent_hex(map_tile, 0);
	if (!adjacent_tile || (adjacent_tile.owner != map_tile.owner)){
		// north west
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x, "y": y-hex_size,
		  a1: 240, l1: hex_size
		});
		
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 1);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// north east border 
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x, "y": y-hex_size,
		  a1: 120, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 2);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// east border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x+(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 0, l1: hex_size
		});
	}

	adjacent_tile = get_adjacent_hex(map_tile, 3);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// south east border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x+(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 240, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 4);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// south west border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x-(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 120, l1: hex_size
		});
	}
	
	adjacent_tile = get_adjacent_hex(map_tile, 5);
	if (!adjacent_tile || adjacent_tile.owner != map_tile.owner){
		// west border??
		$(canvas).drawVector({
		  strokeStyle: stroke_color,
		  strokeWidth: 3,
		  "x": x-(hex_width*0.5), "y": y+(hex_height*0.25),
		  a1: 0, l1: hex_size
		});
	}
}