
function get_hex(x, z){
	if (hex_map['x'+x+'z'+z]){
		return hex_map['x'+x+'z'+z];
	}
	return false;
}



// get the hex object of an adjacent hex to the one given and in a given direction
// 0 is NW. 1 is NE. 2 is E. 3 is SE. 4 is SW. 5 is W.
function get_adjacent_hex(hex, direction){
	var d = hex_neighbors[direction];
	if (hex_map['x'+(hex.x+d[0])+'z'+(hex.z+d[1])]){
		return hex_map['x'+(hex.x+d[0])+'z'+(hex.z+d[1])];
	}
	return false;
}

// converts hex.x and hex.z to pixel coords.x and coords.y 
// will give the center point of the hex
function pixel_to_hex(x,y){	
	x = x;// + map_buffer;
	y = y;// + map_buffer;	
	var q = (1/3*Math.sqrt(3) * (x-hex_size) - 1/3 * (y-hex_size)) / hex_size
	var r = 2/3 * (y-hex_size) / hex_size
	var z = 0 - q - r;
	
	var coords = hex_round(q, z, r);
	return coords;
}

function hex_to_pixel(hex, offset){
	var i = hex.x;
	var j = hex.z;
	// calculate the pixel position of this hex
	var x = hex_size * Math.sqrt(3) * (i + j/2);
	var y = hex_size * 3/2 * j;
	// offset the hex position (currently by the size of a hex)	
	x = x + hex_size;
	y = y + hex_size;
	
	if (offset){
		x = x + offset.x;
		y = y + offset.y;
	}
	//x = x * map_scale;
	//y = y * map_scale;
	return {'x': x, 'y': y};
}

// round a hex coord to the nearest hex
function hex_round(x, y, z){
  var rx = Math.round(x)
  var ry = Math.round(y)
  var rz = Math.round(z)

  var x_err = Math.abs(rx - x)
  var y_err = Math.abs(ry - y)
  var z_err = Math.abs(rz - z)

  if (x_err > y_err && x_err > z_err){
      rx = -ry-rz;
  }else if (y_err > z_err){
      ry = -rx-rz;
  }else{
      rz = -rx-ry;
  }
	return [rx, ry, rz];	
}



function hex_distance(hex1, hex2) {
  return Math.abs((Math.abs(hex1.z - hex2.z) + Math.abs(hex1.x - hex2.x)
        + Math.abs(hex1.z + hex1.x - hex2.z - hex2.x)) / 2);
}

function get_path_between_hexes(hexA, hexB, cost_threshold){
	// init the open and closed arrays for storing traversed hexes
	var open_list = new Object;
	var closed_list = new Object;
	var has_fleets = false;
	if (hexB.your_fleets && hexB.your_fleets.length > 0){
		//has_fleets = true;
	}
	if (hexB.enemy_fleets && hexB.enemy_fleets.length > 0){
		has_fleets = true;
	}
	var hex1 = {'x': hexA.x, 'z': hexA.z, 'movement_cost': hexA.movement_cost};
	var hex2 = {'x': hexB.x, 'z': hexB.z, 'movement_cost': hexB.movement_cost, 'fleets': false, 'attack': has_fleets};
	
	hex1.f = 0;
	hex1.g = 0;
	hex1.h = 0;
	// add start space to open list
	open_list['x'+hex1.x+'z'+hex1.z] = hex1;
	console.log(cost_threshold);
	if (hex2.movement_cost > cost_threshold || hex2.fleets){			
		return false;
	}
	for(var limit = 0; limit < 1000; limit++){
		// first find the lowest (f) ranking hex in open to look at
		var lowest_f = 10000;
		var lowest_f_hex = null;
		for (key in open_list){
			var hex = open_list[key];
			if (hex && hex.f < lowest_f){
				lowest_f = hex.f;
				lowest_f_hex = hex;
			}
		}
		
		if (!lowest_f_hex){
			alert("failed: "+limit);
			break;
		}
		
		// if the lowest ranking hex is the goal. we have made it!!
		if (hex2.x == lowest_f_hex.x && hex2.z == lowest_f_hex.z){
			// reverse traverse the path using parents
			var path = new Array();
			var current_hex = lowest_f_hex;
			while (current_hex.parent_hex){
				path[path.length] = get_hex(current_hex.x, current_hex.z);
				current_hex = closed_list['x'+current_hex.parent_hex.x+'z'+current_hex.parent_hex.z];
			}
			path.reverse();
			return path;
			break;
		}
		
		open_list['x'+lowest_f_hex.x+'z'+lowest_f_hex.z] = null;
		closed_list['x'+lowest_f_hex.x+'z'+lowest_f_hex.z] = lowest_f_hex;
		
		for (var i = 0; i < 6; i++){
			var adjacent_hex_temp = get_adjacent_hex(lowest_f_hex, i);
			has_fleets = false;
			if (adjacent_hex_temp.your_fleets && adjacent_hex_temp.your_fleets.length > 0){
				//has_fleets = true;
			}
			if (adjacent_hex_temp.enemy_fleets && adjacent_hex_temp.enemy_fleets.length > 0){
				has_fleets = true;
			}
			var adjacent_hex = {'x': adjacent_hex_temp.x, 'z': adjacent_hex_temp.z, 'movement_cost': adjacent_hex_temp.movement_cost, 'fleet': has_fleets};
			if (!adjacent_hex){
				continue;
			}
			var g_cost = lowest_f_hex.g + 10; // add cost of existing hex to the movement cost to get to the adjacent hex, currently set to 10			
			
			if (adjacent_hex){
				g_cost = g_cost + adjacent_hex.movement_cost * 10 ;
			}
			if (adjacent_hex.movement_cost > cost_threshold || (adjacent_hex.fleet && !(adjacent_hex.x == hex2.x && adjacent_hex.z == hex2.z) )){
				continue;
			}
			if (adjacent_hex_temp.unknown == 1){
				g_cost = g_cost + 10;
			}
			/*
			if (hex_hover_history && hex_hover_history.length > 0){
				for (var key = 0; key < hex_hover_history.length; key++){
					var history_hex = hex_hover_history[key];
					if (history_hex.x == adjacent_hex.x && history_hex.z == adjacent_hex.z){
						//g_cost = g_cost - 8;
						break;
					}
				}
			}
			*/
			if (open_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] && g_cost < open_list['x'+adjacent_hex.x+'z'+adjacent_hex.z].g ){
				open_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] = null;
			}
			if (closed_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] && g_cost < closed_list['x'+adjacent_hex.x+'z'+adjacent_hex.z].g ){
				closed_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] = null;
			}
			if (!open_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] && !closed_list['x'+adjacent_hex.x+'z'+adjacent_hex.z]){
				// if it is not in the open or closed list, then add it to the open list
				adjacent_hex.g = g_cost;
				
				var distance = hex_distance(hex2, adjacent_hex);
				/*
				var diff_x = Math.abs(hex2.x - adjacent_hex.x);
				var diff_z = Math.abs(hex2.z - adjacent_hex.z);
				if (diff_x > diff_z){
					var diff_x = Math.abs(lowest_f_hex.x - adjacent_hex.x);
					var diff_z = Math.abs(lowest_f_hex.z - adjacent_hex.z);
					if (diff_x > diff_z){
						g_cost = g_cost - 10;
					}
				}else {
					var diff_x = Math.abs(lowest_f_hex.x - adjacent_hex.x);
					var diff_z = Math.abs(lowest_f_hex.z - adjacent_hex.z);
					if (diff_z > diff_x){
						g_cost = g_cost - 10;
					}
				}
				*/
				adjacent_hex.f = g_cost + (distance * 20);
				adjacent_hex.h = distance;
				
				adjacent_hex.parent_hex = lowest_f_hex;
				open_list['x'+adjacent_hex.x+'z'+adjacent_hex.z] = adjacent_hex;	
			}
		}	
	}
}


function get_hex_line(hex1, hex2){
	var diff_x = hex1.x - hex2.x;
	var diff_z = hex1.z - hex2.z;
	hex1.y = - hex1.x - hex1.z;
	hex2.y = - hex2.x - hex2.z;
	var diff_y = hex1.y - hex2.y;
	var n = Math.max(Math.abs(diff_x - diff_y), Math.abs(diff_y - diff_z), Math.abs(diff_z - diff_x))
}

