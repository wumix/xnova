<?php

##############################################################################
# *                                                                          #
# * 2MOONS                                                                   #
# *                                                                          #
# * @copyright Copyright (C) 2010 By ShadoX from titanspace.de               #
# *                                                                          #
# *	                                                                         #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.                                     #
# *	                                                                         #
# *  This program is distributed in the hope that it will be useful,         #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of          #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           #
# *  GNU General Public License for more details.                            #
# *                                                                          #
##############################################################################

if(!defined('INSIDE')) die('Hacking attempt!');

function ShowTraderPage()
{
	global $USER, $PLANET, $LNG, $db;
	$ress 		= request_var('ress', '');
	$action 	= request_var('action', '');
	$metal		= round(request_var('metal', 0.0), 0);
	$crystal 	= round(request_var('crystal', 0.0), 0);
	$deut		= round(request_var('deuterium', 0.0), 0);

	$PlanetRess = new ResourceUpdate();
	
	$template	= new template();
	$template->loadscript("trader.js");
	$template->page_topnav();
	$template->page_header();
	$template->page_leftmenu();
	$template->page_planetmenu();
	$template->page_footer();

	if ($ress != '')
	{
		switch ($ress) {
			case 'metal':
				if($action == "trade")
				{
					if ($USER['darkmatter'] < DARKMATTER_FOR_TRADER)
						$template->message(sprintf($LNG['tr_empty_darkmatter'], $LNG['Darkmatter']), "game." . PHP_EXT . "?page=trader", 1);
					elseif ($crystal < 0 || $deut < 0)
						$template->message($LNG['tr_only_positive_numbers'], "game." . PHP_EXT . "?page=trader",1);
					else
					{
						$trade	= ($crystal * 2 + $deut * 4);
						$PlanetRess->CalcResource();
						if ($PLANET['metal'] > $trade)
						{
							$PLANET['metal']     -= $trade;
							$PLANET['crystal']   += $crystal;
							$PLANET['deuterium'] += $deut;
							$USER['darkmatter']	-= DARKMATTER_FOR_TRADER;
							$template->message($LNG['tr_exchange_done'],"game." . PHP_EXT . "?page=trader",1);
						}
						else
							$template->message($LNG['tr_not_enought_metal'], "game." . PHP_EXT . "?page=trader", 1);
							
						$PlanetRess->SavePlanetToDB();
					}
				} else {
					$template->assign_vars(array(
						'tr_resource'		=> $LNG['tr_resource'],
						'tr_sell_metal'		=> $LNG['tr_sell_metal'],
						'tr_amount'			=> $LNG['tr_amount'],
						'tr_exchange'		=> $LNG['tr_exchange'],	
						'tr_quota_exchange'	=> $LNG['tr_quota_exchange'],
						'Metal'				=> $LNG['Metal'],
						'Crystal'			=> $LNG['Crystal'],
						'Deuterium'			=> $LNG['Deuterium'],
						'mod_ma_res_a' 		=> "2",
						'mod_ma_res_b' 		=> "4",
						'ress' 				=> $ress,
					));

					$template->show("trader_metal.tpl");	
				}
			break;
			case 'crystal':
				if($action == "trade")
				{
					if ($USER['darkmatter'] < DARKMATTER_FOR_TRADER)
						$template->message(sprintf($LNG['tr_empty_darkmatter'], $LNG['Darkmatter']), "game." . PHP_EXT . "?page=trader", 1);
					elseif ($metal < 0 || $deut < 0)
						$template->message($LNG['tr_only_positive_numbers'], "game." . PHP_EXT . "?page=trader",1);
					else
					{
						$trade	= ($metal * 0.5 + $deut * 2);						
						$PlanetRess->CalcResource();
						if ($PLANET['crystal'] > $trade)
						{
							$PLANET['metal']     += $metal;
							$PLANET['crystal']   -= $trade;
							$PLANET['deuterium'] += $deut;
							$USER['darkmatter']	-= DARKMATTER_FOR_TRADER;
							$template->message($LNG['tr_exchange_done'],"game." . PHP_EXT . "?page=trader",1);
						}
						else
							$template->message($LNG['tr_not_enought_crystal'], "game." . PHP_EXT . "?page=trader", 1);
						
						$PlanetRess->SavePlanetToDB();
					}
				} else {
					$template->assign_vars(array(
						'tr_resource'		=> $LNG['tr_resource'],
						'tr_sell_crystal'	=> $LNG['tr_sell_crystal'],
						'tr_amount'			=> $LNG['tr_amount'],
						'tr_exchange'		=> $LNG['tr_exchange'],	
						'tr_quota_exchange'	=> $LNG['tr_quota_exchange'],
						'Metal'				=> $LNG['Metal'],
						'Crystal'			=> $LNG['Crystal'],
						'Deuterium'			=> $LNG['Deuterium'],
						'mod_ma_res_a' 		=> "0.5",
						'mod_ma_res_b' 		=> "2",
						'ress' 				=> $ress,
					));

					$template->show("trader_crystal.tpl");	
				}
			break;
			case 'deuterium':
				if($action == "trade")
				{
					if ($USER['darkmatter'] < DARKMATTER_FOR_TRADER)
						$template->message(sprintf($LNG['tr_empty_darkmatter'], $LNG['Darkmatter']), "game." . PHP_EXT . "?page=trader", 1);
					elseif ($metal < 0 || $crystal < 0)
						message($LNG['tr_only_positive_numbers'], "game." . PHP_EXT . "?page=trader",1);
					else
					{
						$trade	= ($metal * 0.25 + $crystal * 0.5);						
						$PlanetRess->CalcResource();
						if ($PLANET['deuterium'] > $trade)
						{
							$PLANET['metal']     += $metal;
							$PLANET['crystal']   += $crystal;
							$PLANET['deuterium'] -= $trade;
							$USER['darkmatter']	-= DARKMATTER_FOR_TRADER;
							$template->message($LNG['tr_exchange_done'],"game." . PHP_EXT . "?page=trader", 1);
						}
						else
							$template->message($LNG['tr_not_enought_deuterium'], "game." . PHP_EXT . "?page=trader", 1);
							
						$PlanetRess->SavePlanetToDB();
					}
				} else {
					$template->assign_vars(array(
						'tr_resource'		=> $LNG['tr_resource'],
						'tr_sell_deuterium'	=> $LNG['tr_sell_deuterium'],
						'tr_amount'			=> $LNG['tr_amount'],
						'tr_exchange'		=> $LNG['tr_exchange'],	
						'tr_quota_exchange'	=> $LNG['tr_quota_exchange'],
						'Metal'				=> $LNG['Metal'],
						'Crystal'			=> $LNG['Crystal'],
						'Deuterium'			=> $LNG['Deuterium'],
						'mod_ma_res_a' 		=> "0.25",
						'mod_ma_res_b' 		=> "0.5",
						'ress' 				=> $ress,
					));

					$template->show("trader_deuterium.tpl");	
				}
			break;
		}
	}
	else
	{
		$PlanetRess->CalcResource();
		$PlanetRess->SavePlanetToDB();
		$template->assign_vars(array(
			'tr_cost_dm_trader'			=> sprintf($LNG['tr_cost_dm_trader'], pretty_number(DARKMATTER_FOR_TRADER), $LNG['Darkmatter']),
			'tr_call_trader_who_buys'	=> $LNG['tr_call_trader_who_buys'],
			'tr_call_trader'			=> $LNG['tr_call_trader'],
			'tr_exchange_quota'			=> $LNG['tr_exchange_quota'],
			'tr_call_trader_submit'		=> $LNG['tr_call_trader_submit'],
			'Metal'						=> $LNG['Metal'],
			'Crystal'					=> $LNG['Crystal'],
			'Deuterium'					=> $LNG['Deuterium'],
		));

		$template->show("trader_overview.tpl");
	}
}
?>