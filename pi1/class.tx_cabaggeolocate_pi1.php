<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Dimitri König <dk@cabag.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('cabag_geolocate').'pi1/class.tx_cabaggeolocate_getgeodata.php');
require_once(t3lib_extMgm::extPath('cabag_geolocate').'lib/class.territory.php');
require_once(t3lib_extMgm::extPath('cabag_geolocate').'lib/geolite/geoipcity.inc');


class tx_cabaggeolocate_pi1  {
	var $prefixId		= 'tx_cabaggeolocate_pi1';		// Same as class name
	var $tablePredifxId	= 'tx_cabaggeolocate';		// DB Table Prefix
	var $scriptRelPath	= 'pi1/class.tx_cabaggeolocate_pi1.php'; // Path to this 
	var $extKey			= 'cabag_geolocate';	// The extension key

	var $rawrecord;
	var $territories;
	var $territoriesDB;
	

	public function postConnectToDB($reference)	{
		if(TYPO3_MODE != "FE") {
			return;
		}
		
		if(!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['config'])) {
			return;
		}

		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData'] = new tx_cabaggeolocate_getgeodata();
		
		$this->territoriesDB = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			"*",
			$this->tablePredifxId.'_territory',
			"pid = ".$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['config']['storageFolderID']
		);

		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritoriesDB($this->territoriesDB);
		
		foreach($this->territoriesDB as $territoryDB) {
			if(!empty($territoryDB['country_initial'])) {
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['country2territory'][$territoryDB['country_initial']] = $territoryDB['title'];
			}

			if(!empty($territoryDB['domains'])) {
				$domains = explode(",", $territoryDB['domains']);
				foreach($domains as $domain) {
					$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['domain2territory'][$domain] = $territoryDB['title'];
				}
			}

			if(!empty($territoryDB['coordinates'])) {
				$points = explode("\r\n",$territoryDB['coordinates']);
				$newter = new tx_cabaggeolocate_territory($territoryDB['uid'], $territoryDB['title']);
				foreach($points as $pointInfo) {
					$point = explode(";", $pointInfo);
					if(count($point) == 3) {
						$newter->insertPointLatLong($point[0], $point[1], $point[2]);
					}
				}
				$this->territories[$territoryDB['title']] = $newter;
			}
			
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2ID'][$territoryDB['title']] = $territoryDB['uid'];
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2Country'][$territoryDB['title']] = $territoryDB['country'];
		}

		$cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['config']['cookieDomain'];
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'])	{
			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain']{0} == '/')	{
				$matchCnt = @preg_match($GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'], t3lib_div::getIndpEnv('TYPO3_HOST_ONLY'), $match);
				if ($matchCnt === FALSE)	{
					t3lib_div::sysLog('The regular expression of $TYPO3_CONF_VARS[SYS][cookieDomain] contains errors. The session is not shared across sub-domains.', 'Core', 3);
				} elseif ($matchCnt)	{
					$cookieDomain = $match[0];
				}
			} else {
				$cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'];
			}
		}
		if(substr($cookieDomain, 0, 1) != '.' && !empty($cookieDomain)) {
			$cookieDomain = '.' . $cookieDomain;
		}

		// if http_host is xxx.mydomain.com set cookie value to a found territory and then return to let typo3 forward 
		if($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['domain2territory'][getenv('HTTP_HOST')]) {
			$ter = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['domain2territory'][getenv('HTTP_HOST')];
			if($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2ID'][$ter]) {
				$terCookie = $ter . "|".$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2ID'][$ter];
				$terCookie .= "|".$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2Country'][$ter];
			}
			$cookieset = setcookie("ter", $terCookie, 0, "/", $cookieDomain, false, true);
			return;
		}

		// if cookie is found set current territory to the cookievalue
		if(!empty($_COOKIE['ter'])) {
			$terCookieInfo = explode("|", $_COOKIE['ter']);
			if(count($terCookieInfo) == 3 && in_array($terCookieInfo[0], $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['domain2territory'])) {
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritory($terCookieInfo[0]);
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritoryID($terCookieInfo[1]);
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setCountry($terCookieInfo[2]);
				return;
			}
		}

		$ip = getenv('REMOTE_ADDR');
		//$ip = '83.77.246.114'; //adsl
		//$ip = '212.203.83.69'; //colt
		//$ip = '82.207.129.82'; //seb

		// check for database entry
		$cachedEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			"*",
			$this->tablePredifxId."_cache",
			"ip = '". $ip ."'"
		);

		// if it's an old entry delete it
		if(count($cachedEntries) > 0) {
			if($cachedEntries[0]['timeout'] < time()) {
				$GLOBALS['TYPO3_DB']->exec_DELETEquery(
					$this->tablePredifxId."_cache",
					"ip = '". $ip ."'"
				);
			} else {
				if(!empty($cachedEntries[0]['territory'])) {
					$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritory($cachedEntries[0]['territory']);
					$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritoryID($cachedEntries[0]['territoryID']);
					$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setCountry($cachedEntries[0]['country']);
				}

				return;
			}
		}

		// if no cookie found nor any subdomain request nor a positive database entry found do a geoip search
		$gi = geoip_open($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['config']['databaseFile'], GEOIP_STANDARD);

		$record = geoip_record_by_addr($gi, $ip);

		if(is_object($record)) {
			$this->rawrecord = $record;
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['rawrecord'] = $record;
		}
		geoip_close($gi);

		$ters = $this->visitorIsInTerritories();
		$curTer = "";
		if(count($ters) == 0) {
			if($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['country2territory'][$record->country_code]) {
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritory($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['country2territory'][$record->country_code]);
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setCountry($record->country_code);
				$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritoryID($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2ID'][$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritory()]);
			}
		} else {
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritory($ters[0]);
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setTerritoryID($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2ID'][$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritory()]);
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->setCountry($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['territory2Country'][$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritory()]);
		}

		// cache current entry
		$fields = array(
			"ip" => $ip,
			"territoryID" => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritoryID(),
			"territory" => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritory(),
			"country" => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getCountry(),
			"timeout" => time()+intval($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['config']['cacheTimeout'])
		);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
			$this->tablePredifxId."_cache",
			$fields
		);
	}


	public function getTerritories() {
		if(is_array($this->territories) && count($this->territories) > 0) {
			return false;
		} else {
			return $this->territories;
		}
	}

	public function getTerritory($name) {
		if(is_object($this->territories->$name)) {
			return $this->territories->$name;
		} else {
			return false;
		}
	}
	
	public function getTerritoryArea($name) {
		if($this->territories[$name]) {
			return $this->territories[$name];
		} else {
			return false;
		}
	}

	public function visitorIsInTerritory($name) {
		return $this->isInTerritory($name, $this->rawrecord->latitude, $this->rawrecord->longitude);
	}

	public function isInTerritory($name, $lat, $long) {
		if($this->territories[$name]) {
			return $this->territories[$name]->isPointInTerritory($lat, $long);
		}
	}

	public function visitorIsInTerritories($list = true) {
		return $this->isInTerritories($this->rawrecord->latitude, $this->rawrecord->longitude, $list);
	}
	
	public function isInTerritories($lat, $long, $list = true) {
		$ters = array();

		foreach($this->territories as $ter) {
			if($ter->isPointInTerritory($lat, $long)) {
				$ters[] = $ter->getName();
			}
		}

		if($list) {
			return $ters;
		} else {
			return count($ters);
		}
	}

	public function getCountry() {
		return $this->rawrecord->country_code;
	}
	
	public function getCity() {
		return $this->rawrecord->city;
	}

	public function getLatitude() {
		return $this->rawrecord->latitude;
	}

	public function getLongitude() {
		return $this->rawrecord->longitude;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/pi1/class.tx_cabaggeolocate_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/pi1/class.tx_cabaggeolocate_pi1.php']);
}

?>