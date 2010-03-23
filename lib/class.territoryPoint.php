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

class tx_cabaggeolocate_territoryPoint {
	var $name;
	var $latitude;
	var $longitude;

	public function tx_cabaggeolocate_territoryPoint($name, $lat, $long) {
		$this->name = $name;

		if(is_numeric($lat)) {
			$this->latitude = $lat;
		} else {
			$this->latitude = 0;
		}

		if(is_numeric($long)) {
			$this->longitude = $long;
		} else {
			$this->longitude = 0;
		}
	}

	public function getName() {
		return $this->name;
	}
	
	public function getLatitude() {
		return $this->latitude;
	}
	
	public function getLongitude() {
		return $this->longitude;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/lib/class.territoryPoint.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_geolocate/lib/class.territoryPoint.php']);
}

?>
