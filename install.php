<?php
/*
	Redaxo-Addon Backend-Tools
	Installation
	v1.4.5
	by Falko Müller @ 2018-2019 (based on 1.0@rex4)
	package: redaxo5
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Vorgaben vornehmen
if (!$this->hasConfig()):
	$this->setConfig('config', [
		'be_hplink'				=> 'checked',
		'be_minnav'				=> '',
		
		'be_tree'				=> 'left',
		'be_tree_menu'			=> 'checked',
		'be_tree_shortnames'	=> '',
		'be_tree_showid'		=> '',
		'be_tree_onlystructure'	=> '',
		'be_tree_activemode'	=> 'checked',
		'be_tree_persist'		=> '',
		
		'be_crop'				=> 'checked',
		'be_crop_pre1'			=> '50x33',
		'be_crop_pre2'			=> '100x67',
		'be_crop_pre3'			=> '150x100',
		'be_crop_pre4'			=> '250x167',
		'be_crop_pre5'			=> '500x333',
	]);
endif;


//Datenbank-Einträge vornehmen


//Module anlegen


//Aktionen anlegen


//Templates anlegen


//Installation abschließen und Fehler aufbereiten
/*
if (!empty($error)):
	$REX['ADDON']['installmsg'][$mypage] = $error;
	$REX['ADDON']['install'][$mypage] = 0;
else:
	//Komponente als installiert markieren
	$REX['ADDON']['install'][$mypage] = 1;
endif;
*/
?>