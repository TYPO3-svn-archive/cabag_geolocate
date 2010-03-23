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

class tx_cabaggeolocate_getgeodata {

	var $_territory = "";
	var $_country = "";
	var $_id = 0;
	var $_territoriesDB;

	public function setTerritoryID($id) {
		$this->_id = $id;
	}

	public function setTerritory($title) {
		$this->_territory = $title;
	}

	
	public function setCountry($title) {
		$this->_country = $title;
	}

	public function setTerritoriesDB($arr) {
		$this->_territoriesDB = $arr;
	}
	
	public function getTerritoryID() {
		return $this->_id;
	}

	public function getTerritory() {
		return $this->_territory;
	}

	public function getCountry() {
		return $this->_country;
	}

	public function getCountryID() {
		foreach($this->_territoriesDB as $ters) {
			if($ters['country_initial'] == $this->_country) {
				return $ters['uid']; 
			}
		}
	}
	
	public function isTerACountry($ter) {
		if($this->_territoriesDB[$ter]['country'] == $this->_territoriesDB[$ter]['country_initial']) {
			return true;
		}
		return false;
	}
	
	public function territoryID2Country($id) {
		foreach($this->_territoriesDB as $ters) {
			if($ters['uid'] == $id) {
				return $ters['country']; 
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/pi1/class.tx_cabaggeolocate_getgeodata.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/pi1/class.tx_cabaggeolocate_getgeodata.php']);
}

?>
