<?php
/*
	Redaxo-Addon Backend-Tools
	Deinstallation
	v1.9.0
	by Falko Müller @ 2018-2024
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = ""; $notice = "";


//Datenbank-Einträge löschen
rex_sql_table::get(rex::getTable('article_slice'))
	->removeColumn('bet_slicetimer')
    ->alter();


//Cache leeren
rex_delete_cache();
echo rex_view::info($this->i18n('a1510_cache_cleared'));

//Module löschen
//$notice .= $I18N->msg('a1544_deletemodule');	//'Bitte löschen Sie die installierten Addon-Module von Hand.<br />';


//Aktionen löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Aktionen von Hand.<br />';


//Templates löschen
//$notice .= 'Bitte löschen Sie die installierten Addon-Templates von Hand.<br />';
?>