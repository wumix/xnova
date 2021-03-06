<?php

 /**
 * rak.php
 *
 * @version 1.0
 * @copyright 2009 by Dr.Isaacs f�r XNova-Germany
 * http://www.xnova-germany.org
 */

//Revisioned svn 12 - Urza    
//Revisioned svn 13 - GioGio
    if (!defined('INSIDE')) {
       die("Hacking attempt");
    }
    define('INSIDE' , true);
    define('INSTALL' , false);

    includeLang('tech');

    if (file_exists($ugamela_root_path . "includes/raketenangriff.php")) {
       include($ugamela_root_path . "includes/raketenangriff.php");
    } elseif (file_exists("includes/raketenangriff.php")) {
       include("./includes/raketenangriff.php");
    } elseif (file_exists("../includes/raketenangriff.php")) {
       include("../includes/raketenangriff.php");
    } else {
       die('Fatal error!');
    }

    if (isset($resource) && !empty($resource[401])) {
       $iraks = doquery("SELECT * FROM {{table}} WHERE zeit <= '" . time() . "'", 'iraks');

       while ($selected_row = mysql_fetch_array($iraks)) {
          if ($selected_row['zeit'] != '' && $selected_row['galaxy'] != '' && $selected_row['system'] != '' && $selected_row['planet'] != '' && is_numeric($selected_row['owner']) && is_numeric($selected_row['zielid']) && is_numeric($selected_row['anzahl']) && !empty($selected_row['anzahl'])) {
             $planetrow = doquery("SELECT * FROM {{table}} WHERE
                            galaxy = '" . $selected_row['galaxy'] . "' AND
                            system = '" . $selected_row['system'] . "' AND
                            planet = '" . $selected_row['planet'] . "' AND
                            planet_type = '1'", 'planets');

             $TargetArmour = doquery("SELECT `defence_tech` FROM {{table}} WHERE
                            id = '" . $selected_row['zielid'] . "'", 'users');

             $OwnWeapons = doquery("SELECT `military_tech` FROM {{table}} WHERE
                            id = '" . $selected_row['owner'] . "'", 'users');

             if (mysql_num_rows($planetrow) != 1 OR mysql_num_rows($TargetArmour) != 1) {
                doquery("DELETE FROM {{table}} WHERE id = '" . $selected_row['id'] . "'", 'iraks');
             } else {
                $Defender = mysql_fetch_array($TargetArmour);
                $Attacker = mysql_fetch_array($OwnWeapons);
                $planet = mysql_fetch_array($planetrow);

                $ids = array(
                      0 => 401,
                      1 => 402,
                      2 => 403,
                      3 => 404,
                      4 => 405,
                      5 => 406,
                      6 => 407,
                      7 => 408,
                      8 => 502,
                      9 => 503
                      );

                $def = array(
                      0 => $planet['misil_launcher'], // Raketenwerfer
                      1 => $planet['small_laser'], // Leichtes Lasergesch�tz
                      2 => $planet['big_laser'], // Schweres Lasergesch�tz
                      3 => $planet['gauss_canyon'], // Gau�kanone
                      4 => $planet['ionic_canyon'], // Ionengesch�tz
                      5 => $planet['buster_canyon'], // Plasmawerfer
                      6 => $planet['small_protection_shield'], // Kleine Schildkuppel
                      7 => $planet['big_protection_shield'], // Gro�e Schildkuppel
                      8 => $planet['interceptor_misil'], // Abfangrakete
                      9 => $planet['interplanetary_misil'], // Interplanetarrakete
                      );

                $lang =   array(
                      0 => $lang['tech'][401],
                      1 => $lang['tech'][402],
                      2 => $lang['tech'][403],
                      3 => $lang['tech'][404],
                      4 => $lang['tech'][405],
                      5 => $lang['tech'][406],
                      6 => $lang['tech'][407],
                      7 => $lang['tech'][408],
                      8 => $lang['tech'][502],
                      9 => $lang['tech'][503],
                      10 => "Missiles Intercepteur"
                      );

				if ($anzahl == 1){
				$n = "";
				}else{
				$n = "n";
				}
                $message = '';
				$abgeschossenenAR=$selected_row['anzahl'];
                if ($planet['interceptor_misil'] >= $selected_row['anzahl']) {
                   $message = "Es wurde".$anzahl." (" . $abgeschossenenAR . ") Interplanetarrakete".$anzahl." durch Abfangraketen vernichtet, <br>aber keine Verteidigung.";
				   
                   $x = $planet['interceptor_misil'] - $selected_row['anzahl'];

                   doquery("UPDATE {{table}} SET `interceptor_misil` = '".$x."' WHERE id = " . $planet['id'], 'planets');
                } else {
                   if ($planet['interceptor_misil'] > 0) {

                      doquery("UPDATE {{table}} SET `interceptor_misil` = '0' WHERE id = " . $planet['id'], 'planets');

                      $message = $planet['interceptor_misil'] . " Interplanetarrakete".$anzahl." durch Abfangraketen vernichtet <br>";
					  
                      $selected_row['anzahl'] = $selected_row['anzahl'] - $planet['interceptor_misil'];
                   }

                   $irak = raketenangriff($Defender['defence_tech'], $Attacker['military_tech'], $selected_row['anzahl'], $def, $selected_row['primaer']);

                   foreach ($irak['zerstoert'] as $id => $anzahl) {
                      if (!empty($anzahl) && $id < 10) {
                         if ($id != 9)
                            $message .= $lang[$id] . " (- " . $anzahl . ")<br>";

                         $x = $resource[$ids[$id]];

                         doquery("UPDATE {{table}} SET " . $x . " = " . $x . "-" . $anzahl . " WHERE id = " . $planet['id'], 'planets');
						 
                      }
                   }
                }

                $planet_1 = doquery("SELECT * FROM {{table}} WHERE
                            galaxy = '" . $selected_row['galaxy_angreifer'] . "' AND
                            system = '" . $selected_row['system_angreifer'] . "' AND
                            planet = '" . $selected_row['planet_angreifer'] . "' AND
                            planet_type ='1'", 'planets');

                if (mysql_num_rows($planet_1) == 1) {
                   $array = mysql_fetch_array($planet_1);

                   $name = $array['name'];
                }

                $planet_2 = doquery("SELECT * FROM {{table}} WHERE
                            galaxy = '" . $selected_row['galaxy'] . "' AND
                            system = '" . $selected_row['system'] . "' AND
                            planet = '" . $selected_row['planet'] . "' AND
                            planet_type ='1'", 'planets');

                if (mysql_num_rows($planet_2) == 1) {
                   $array = mysql_fetch_array($planet_2);

                   $name_deffer = $array['name'];
                }

                $message_vorlage  = 'Sie wurden mit (' . $abgeschossenenAR . ') Interplanetarrakete vom Planeten  ' . $name .  ' <a href="galaxy.php?mode=3&galaxy=' . $selected_row['galaxy_angreifer'] . '&system=' . $selected_row['system_angreifer'] . '&planet=' . $selected_row['planet_angreifer'] . '">[' . $selected_row['galaxy_angreifer'] . ':' . $selected_row['system_angreifer'] . ':' . $selected_row['planet_angreifer'] . ']</a>';
                $message_vorlage .= ' auf den Planeten ' . $name_deffer . ' <a href="galaxy.php?mode=3&galaxy=' . $selected_row['galaxy'] . '&system=' . $selected_row['system'] . '&planet=' . $selected_row['planet'] . '">[' . $selected_row['galaxy'] . ':' . $selected_row['system'] . ':' . $selected_row['planet'] . ']</a>'.' angegriffen.<br><br>';

                if (empty($message)){
                   $message = "Da sich auf dem Planeten keine Verteidigungsanlagen und Abfangraketen befanden, wurde nichts zerst&ouml;rt!!";
                }

                
				doquery("INSERT INTO {{table}} SET
                      `message_owner`='" . $selected_row['zielid'] . "',
                      `message_sender`='0',
                      `message_time`=UNIX_TIMESTAMP(),
                      `message_type`='0',
                      `message_from`='Hauptquatier',
                      `message_subject`='Raketenangriff',
                      `message_text`='" . $message_vorlage . $message . "'" , 'messages');
                doquery("UPDATE {{table}} SET `new_message` = `new_message` + 1 WHERE `id` = '" . $selected_row['zielid'] . "'", 'users');

                doquery("DELETE FROM {{table}} WHERE `id` = '" . $selected_row['id'] . "'", 'iraks');

			doquery("INSERT INTO {{table}} SET
                      `message_owner`='" . $selected_row['owner'] . "',
                      `message_sender`='0',
                      `message_time`=UNIX_TIMESTAMP(),
                      `message_type`='0',
                      `message_from`='Hauptquatier',
                      `message_subject`='Raketenangriff',
                      `message_text`='" . $message_vorlage . $message . "'" , 'messages');
                doquery("UPDATE {{table}} SET `new_message` = `new_message` + 1 WHERE `id` = '" . $selected_row['owner'] . "'", 'users');
             }
          } else {
             doquery("DELETE FROM {{table}} WHERE `id` = '" . $selected_row['id'] . "'", 'iraks');
          }
       }
    }

?>
