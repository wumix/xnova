<?php

/**
 * moonlist.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

	if ($user['authlevel'] >= "2") {
		includeLang('admin/planet_moon_list');


// to order the contents
if ($_GET['cmd'] == 'sort') {
	// to prevent errors
	switch ($_GET['type']) {
		case 'user':     	$short = 'id_owner';        			break;
		case 'planet':    $short = 'name';      				break;
		case 'corde':   	$short = 'galaxy'; 'system'; 'planet';  	break;
		case 'activiti': 	$short = 'last_update'; 			break;
		case 'diametro': 	$short = 'diameter'; 				break;
		default:          $short = 'id';          			break;


	}
} else {
	$short = 'id';
}


		$parse = $lang;
		$query = doquery("SELECT `id`, `id_owner`, `name`, `last_update`,  `field_current`, `field_max`, `diameter`, `galaxy`, `system`, `planet` FROM {{table}} WHERE planet_type=3 ORDER BY `". $short ."` ASC", "planets");


		$i = 0;
		while ($u = mysql_fetch_array($query)) {



			$parse['planetes'] .= "<tr>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . $u['id'] . "</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . $u['id_owner'] . "</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . $u['name'] . "</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . $u['field_current'] . "/" . $u['field_max'] . "</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . pretty_number($u['diameter']) . " Km</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . $u['galaxy'] . ":" . $u['system'] . ":" . $u['planet'] . "</a></center></td>"
			. "<td class=b><center><a href='moonlist.php?action=edit&id=".$u['id'] ."'>" . pretty_time(time() - $u['last_update']) . "</a></center></td>"
			. "</tr>";
			$i++;
		}

		if ($i == "1")
			$parse['planetes'] .= "<tr><th class=b colspan=7>Hay 1 luna creada</th></tr>";
		else
			$parse['planetes'] .= "<tr><th class=b colspan=7>Hay {$i} lunas creadas</th></tr>";




		
		if(isset($_GET['action']) && isset($_GET['id'])) {
			$id = intval($_GET['id']);
			$query  = doquery("SELECT * FROM {{table}} WHERE planet_type=3 AND id='".$id."' LIMIT 1", "planets");
			$planet = mysql_fetch_array($query);
			$parse['show_edit_form'] = parsetemplate(gettemplate('admin/moon_edit_form'),$planet);
		}




		if(isset($_POST['submit'])) {
			
			$edit_id 	= intval($_POST['currid']);
			$planetname = mysql_real_escape_string($_POST['planetname']);
			$fields_max = intval($_POST['felder']);
			$query = doquery("UPDATE {{table}} SET 
							`name` 				= '".$planetname."', 
							`field_max` 			= '".$fields_max."',
							`metal`				= '".$_POST['metal']."',
							`crystal`				= '".$_POST['crystal']."',
							`deuterium`				= '".$_POST['deuterium']."', 
							`small_ship_cargo` 		= '".intval($_POST['small_ship_cargo'])."', 
							`big_ship_cargo` 			= '".intval($_POST['big_ship_cargo'])."', 
							`light_hunter`			= '".intval($_POST['light_hunter'])."', 
							`heavy_hunter`			= '".intval($_POST['heavy_hunter'])."', 
							`crusher`				= '".intval($_POST['crusher'])."', 
							`battle_ship`			= '".intval($_POST['battle_ship'])."', 
							`colonizer`				= '".intval($_POST['colonizer'])."', 
							`recycler`				= '".intval($_POST['recycler'])."', 
							`spy_sonde`				= '".intval($_POST['spy_sonde'])."', 
							`bomber_ship`			= '".intval($_POST['bomber_ship'])."', 
							`solar_satelit`			= '".intval($_POST['solar_satelit'])."', 
							`destructor`			= '".intval($_POST['destructor'])."', 
							`dearth_star`			= '".intval($_POST['dearth_star'])."', 
							`battleship`			= '".intval($_POST['battleship'])."',
							`misil_launcher`			= '".intval($_POST['misil_launcher'])."',
							`small_laser`			= '".intval($_POST['small_laser'])."',
							`big_laser`				= '".intval($_POST['big_laser'])."',
							`gauss_canyon`			= '".intval($_POST['gauss_canyon'])."',
							`ionic_canyon`			= '".intval($_POST['ionic_canyon'])."',
							`buster_canyon`			= '".intval($_POST['buster_canyon'])."',
							`small_protection_shield` 	= '".intval($_POST['small_protection_shield'])."',
							`big_protection_shield` 	= '".intval($_POST['big_protection_shield'])."',
							`robot_factory`			= '".intval($_POST['robot_factory'])."',
							`hangar`				= '".intval($_POST['hangar'])."',
							`metal_store`			= '".intval($_POST['metal_store'])."',
							`crystal_store`			= '".intval($_POST['crystal_store'])."',
							`deuterium_store`			= '".intval($_POST['deuterium_store'])."',
							`mondbasis`				= '".intval($_POST['mondbasis'])."',
							`phalanx`				= '".intval($_POST['phalanx'])."',
							`sprungtor`				= '".intval($_POST['sprungtor'])."'
							  WHERE `id` = '".$edit_id."' LIMIT 1",'planets'); 
			
			AdminMessage ('<meta http-equiv="refresh" content="3; url=moonlist.php">Los datos han sido modificados', 'Edicion de luna');
		}




		display(parsetemplate(gettemplate('admin/moonlist_body'), $parse), 'Planetlist', false, '', true);
	} else {
		message($lang['sys_noalloaw'], $lang['sys_noaccess']);
	}

// Created by e-Zobar. All rights reversed (C) XNova Team 2008
?>
