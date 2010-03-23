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

require_once('class.territoryPoint.php');

class tx_cabaggeolocate_territory {
	var $name;
	var $uid;

	var $points = array();

	public function tx_cabaggeolocate_territory($uid, $name) {
		$this->uid = $uid;
		$this->name = $name;
	}

	public function insertPoint($point) {
		$this->points[] = $point;
	}

	public function insertPointLatLong($name, $lat, $long) {
		$point = new tx_cabaggeolocate_territoryPoint($name, $lat, $long);
		if($point) {
			$this->insertPoint($point);
		} else {
			return false;
		}
	}

	public function isPointInTerritory($lat, $long) {
		if(count($this->points) < 3) {
			return false;
		}

		if($this->points[0]->name != $this->points[count($this->points)-1]->name) {
			$this->insertPoint($this->points[0]);
		}

		$j = count($this->points) - 1;
		$inPoly = false;
		
		for($i = 0; $i < count($this->points); $i++) {
			if(($this->points[$i]->getLongitude() < $long && $this->points[$j]->getLongitude() >= $long) || ($this->points[$j]->getLongitude() < $long && $this->points[$i]->getLongitude() >= $long)) {
				if($this->points[$i]->getLatitude() + ($long - $this->points[$i]->getLongitude()) / ($this->points[$j]->getLongitude() - $this->points[$i]->getLongitude()) * ($this->points[$j]->getLatitude() - $this->points[$i]->getLatitude()) < $lat) {
					$inPoly = !$inPoly;
				}
			}
			$j = $i;
		}
		return $inPoly; 
	}

	public function getName() {
		return $this->name;
	}
	
	public function getPoints() {
		return $this->points;
	}

	public function getPoint($name) {
		foreach($this->points as $point) {
			if($point->getName() == $name) {
				return $point;
			}
		}

		return false;
	}
	
	public function getLatitudeOfPoint($name) {
		if($point = $this->getPoint($name)) {
			return $point->getLatitude;
		}

		return false;
	}
	
	public function getLongitudeOfPoint($name) {
		if($point = $this->getPoint($name)) {
			return $point->getLongitude;
		}

		return false;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/lib/class.territory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/lib/class.territory.php']);
}

?>
