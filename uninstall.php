<?php
/*
	Redaxo-Addon Backend-Tools
	Deinstallation
	v1.4
	by Falko Müller @ 2018-2019 (based on 1.0@rex4)
	package: redaxo5
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = ""; $notice = "";


//Datenbank-Einträge löschen


//Module löschen
//$notice .= $I18N->msg('a1510_deletemodule');	//'Bitte löschen Sie die installierten Addon-Module von Hand.<br />';


//Aktionen löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Aktionen von Hand.<br />';


//Templates löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Templates von Hand.<br />';


//Deinstallation abschließen und Fehler aufbereiten
/*
if (!empty($error)):
	$REX['ADDON']['installmsg'][$mypage] = $error;
	//rex_warning($error);
endif;
	//Komponente als deinstalliert markieren
	$REX['ADDON']['install'][$mypage] = 0;
		echo rex_info($notice);
*/
?>