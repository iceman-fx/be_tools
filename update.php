<?php
/*
	Redaxo-Addon Backend-Tools
	Updateprozeduren
	v1.9.0
	by Falko Müller @ 2018-2024
*/

/** RexStan: Vars vom Check ausschließen */
/** @var rex_addon $this */


//Variablen deklarieren
$mypage = $this->getProperty('package');
$error = "";


//Datenbank-Spalten anlegen, sofern noch nicht verfügbar
rex_sql_table::get(rex::getTable('article_slice'))
	->ensureColumn(new rex_sql_column('bet_slicetimer', 'text'))
    ->alter();
?>