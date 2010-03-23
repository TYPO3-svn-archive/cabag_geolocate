<?php
if (!defined ('TYPO3_MODE'))     die ('Access denied.');

$TCA['tx_cabaggeolocate_territory'] = array (
    'ctrl' => $TCA['tx_cabaggeolocate_territory']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,title,country_initial,country,domains,coordinates'
    ),
    'feInterface' => $TCA['tx_cabaggeolocate_territory']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'title' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:cabag_geolocate/locallang_db.xml:tx_cabaggeolocate_territory.title',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '30',    
                'eval' => 'required,trim',
            )
        ),
        'country_initial' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:cabag_geolocate/locallang_db.xml:tx_cabaggeolocate_territory.country_initial',        
            'config' => array (
                'type' => 'input',    
                'size' => '5',    
                'max' => '4',    
                'eval' => 'trim',
            )
        ),
        'country' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:cabag_geolocate/locallang_db.xml:tx_cabaggeolocate_territory.country',        
            'config' => array (
                'type' => 'input',    
                'size' => '5',    
                'max' => '4',    
                'eval' => 'trim',
            )
        ),
        'domains' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:cabag_geolocate/locallang_db.xml:tx_cabaggeolocate_territory.domains',        
            'config' => array (
                'type' => 'input',    
                'size' => '48',
            )
        ),
        'coordinates' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:cabag_geolocate/locallang_db.xml:tx_cabaggeolocate_territory.coordinates',
            'config' => array (
                'type' => 'text',
                'wrap' => 'OFF',
                'cols' => '48',    
                'rows' => '20',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, country_initial;;;;3-3-3, country, domains, coordinates')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>
