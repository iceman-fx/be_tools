<?php
/*
	Redaxo-Addon Backend-Tools
	Boot (weitere Konfigurationen)
	v1.6
	by Falko Müller @ 2018-2021 (based on 1.0@rex4)
	package: redaxo5
*/

//Variablen deklarieren
$mypage = $this->getProperty('package');
//$this->setProperty('name', 'Wert');

	//Berechtigungen deklarieren
	if (rex::isBackend() && is_object(rex::getUser())):
		rex_perm::register($mypage.'[]');
		//rex_perm::register($mypage.'[admin]');
	endif;


//Userrechte prüfen
$isAdmin = ( is_object(rex::getUser()) AND (rex::getUser()->hasPerm($mypage.'[admin]') OR rex::getUser()->isAdmin()) ) ? true : false;


//Addon Einstellungen
$config = rex_addon::get($mypage)->getConfig('config');			//Addon-Konfig einladen


//Funktionen einladen/definieren
//Global für Backend+Frontend
global $a1510_mypage;
$a1510_mypage = $mypage;

require_once(rex_path::addon($mypage)."/functions/functions.inc.php");
	//Prüffunktionen einbinden
	//rex_extension::register('PACKAGES_INCLUDED', 'a1XX_functionname');	

//Backendfunktionen
if (rex::isBackend() && rex::getUser()):
	//Globale Einstellungen
	require_once(rex_path::addon($mypage)."/functions/functions_be.inc.php");
	
		
	//Backend-Anpassungen
	rex_view::addJsFile($this->getAssetsUrl('rextree/js.cookie.min.js'));
	rex_extension::register('OUTPUT_FILTER', 'a1510_changeBE');
	
	
	//Navigation minimieren
	if (@$config['be_minnav'] == 'checked'):
		rex_view::addCssFile($this->getAssetsUrl('style.css'));
		rex_view::addJsFile($this->getAssetsUrl('jquery.browser.min.js'));
		rex_view::addJsFile($this->getAssetsUrl('script.js'));
	endif;
	
	
	//Sidebar minimieren
	if (@$config['be_minsidebar'] == 'checked'):
		rex_view::addCssFile($this->getAssetsUrl('style-sidebar.css'));
		rex_view::addJsFile($this->getAssetsUrl('script-sidebar.js'));
	endif;


	//Tree einbinden
	$tmp = @$config['be_tree'];
	if (preg_match("/(top|left|right)/i", $tmp)):
		rex_extension::register('PACKAGES_INCLUDED', function($ep){
			global $a1510_mypage;
			$config = rex_addon::get($a1510_mypage)->getConfig('config');
	
			if (rex::getUser()->hasPerm('structure/hasStructurePerm')):
				require_once(rex_path::addon($a1510_mypage)."/functions/functions_be_tree.inc.php");
				
				$stcArr = array("structure", "content");							//Definition der gültigen Seiten, wo rexTree grundsätzlich eingebunden werden soll
				$stcAll = ($config['be_tree_onlystructure'] == 'checked') ? 0 :	1;	//rexTree in allen Bereichen anzeigen oder nur in Struktur -> siehe Config
				$stcAllNot = array("mediapool", "linkmap", "imagecropper");			//Definition der Seiten, wo rexTree nicht eingebunden werden soll
				
				$page = rex_be_controller::getCurrentPagePart(1);
				$subpage = rex_be_controller::getCurrentPagePart(2);
				$in = count(preg_grep('/^'.$page.'/i', $stcArr));
				$out = count(preg_grep('/^'.$page.'/i', $stcAllNot));
				
				if (($in > 0 || ($stcAll == 1 && $out <= 0))):
					rex_view::addCssFile($this->getAssetsUrl('rextree/jstree/themes/default/style.min.css'));
					rex_view::addCssFile($this->getAssetsUrl('rextree/rextree.css'));
					//rex_view::addJsFile($this->getAssetsUrl('rextree/js.cookie.min.js'));
					rex_view::addJsFile($this->getAssetsUrl('rextree/jstree/jstree.min.js'));
					rex_extension::register('OUTPUT_FILTER', 'a1510_showTree');
				endif;
			endif;	
		});
	endif;

endif;

//Frontendfunktionen
if (!rex::isBackend()):
	//require_once(rex_path::addon($mypage)."/functions/functions_fe.inc.php");
	
	//CSS/Skripte einbinden
	//rex_extension::register('OUTPUT_FILTER', 'a1510_addAssets');
endif;
?>