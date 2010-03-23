This extension gives you informations about the users location like country & territory.
Use this functions to access the data:

territory id:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritoryID()

territory name:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getTerritory()

country code:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']->getCountry()

country id for territory:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cabag_geolocate']['currentData']-> getCountryID()

