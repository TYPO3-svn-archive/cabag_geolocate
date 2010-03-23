<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB'][] = 'EXT:cabag_geolocate/pi1/class.tx_cabaggeolocate_pi1.php:tx_cabaggeolocate_pi1->postConnectToDB';

// Include configuration file
$_cabgag_geolocate_conf = @unserialize($_EXTCONF);
if (is_array($_cabgag_geolocate_conf)) {
	$TYPO3_CONF_VARS['EXTCONF']['cabag_geolocate']['config']['cookieDomain'] = trim($_cabgag_geolocate_conf['cookieDomain']); 
	$TYPO3_CONF_VARS['EXTCONF']['cabag_geolocate']['config']['databaseFile'] = trim($_cabgag_geolocate_conf['databaseFile']);
	$TYPO3_CONF_VARS['EXTCONF']['cabag_geolocate']['config']['cacheTimeout'] = trim($_cabgag_geolocate_conf['cacheTimeout']);
	$TYPO3_CONF_VARS['EXTCONF']['cabag_geolocate']['config']['storageFolderID'] = trim($_cabgag_geolocate_conf['storageFolderID']);
}
unset($_cabgag_geolocate_conf);

t3lib_extMgm::addUserTSConfig('
    options.saveDocNew.tx_cabaggeolocate_territory=1
');

?>