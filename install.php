<?php
/*
	Redaxo-Addon Backend-Tools
	Installation
	v1.9.0
	by Falko Müller @ 2018-2024
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
if (!$this->hasConfig()):
	$this->setConfig('config', [
		'be_hplink'				=> 'checked',
		'be_minnav'				=> '',
		'be_minsidebar'			=> '',
		'be_gototop'			=> 'checked',
		
		'be_tree'				=> 'left',
		'be_tree_menu'			=> 'checked',
		'be_tree_shortnames'	=> '',
		'be_tree_showid'		=> '',
		'be_tree_onlystructure'	=> 'checked',
		'be_tree_activemode'	=> 'checked',
		'be_tree_persist'		=> 'checked',
        
        'be_mediapool_usesort'  => '',
        'be_mediapool_sort'     => '',
		
        'be_slicetimer'  		=> '',
        'be_slicetimer_workversion' => '',
		'be_slicetimer_infoblock' 	=> '',
		
		'be_crop'				=> 'checked',
		'be_crop_pre1'			=> '50x33',
		'be_crop_pre2'			=> '100x67',
		'be_crop_pre3'			=> '150x100',
		'be_crop_pre4'			=> '250x167',
		'be_crop_pre5'			=> '500x333',
	]);
endif;


//Datenbank-Spalten anlegen, sofern noch nicht verfügbar
rex_sql_table::get(rex::getTable('article_slice'))
	->ensureColumn(new rex_sql_column('bet_slicetimer', 'text'))
    ->alter();


//Module anlegen


//Aktionen anlegen


//Templates anlegen
?>