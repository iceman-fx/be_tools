<?php
/*
	Redaxo-Addon Backend-Tools
	Boot (weitere Konfigurationen)
	v1.8.0
	by Falko Müller @ 2018-2023
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

global $a1510_darkmode;
$a1510_darkmode = (rex_string::versionCompare(rex::getVersion(), '5.13.0-dev', '>=')) ? true : false;


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
		if ($a1510_darkmode) { rex_view::addCssFile($this->getAssetsUrl('style-darkmode.css')); }
		
		rex_view::addJsFile($this->getAssetsUrl('jquery.browser.min.js'));
		rex_view::addJsFile($this->getAssetsUrl('script.js'));
	endif;
	
	
	//Sidebar minimieren
	if (@$config['be_minsidebar'] == 'checked'):
		rex_view::addCssFile($this->getAssetsUrl('style-sidebar.css'));
		if ($a1510_darkmode) { rex_view::addCssFile($this->getAssetsUrl('style-sidebar-darkmode.css')); }
		
		rex_view::addJsFile($this->getAssetsUrl('script-sidebar.js'));
	endif;
	
	
	//Nach oben-Button
	if (@$config['be_gototop'] == 'checked'):
		rex_view::addCssFile($this->getAssetsUrl('style-gototop.css'));
		if ($a1510_darkmode) { rex_view::addCssFile($this->getAssetsUrl('style-gototop-darkmode.css')); }
		
		rex_view::addJsFile($this->getAssetsUrl('script-gototop.js'));
		rex_extension::register('OUTPUT_FILTER', 'a1510_gotoTop');
	endif;
    
    
    //Medienpool-Sortierung
    if (@$config['be_mediapool_usesort'] == 'checked' && !empty(@$config['be_mediapool_sort'])):
        rex_extension::register('MEDIA_LIST_QUERY', function (rex_extension_point $ep) {
            global $a1510_mypage;
            $config = rex_addon::get($a1510_mypage)->getConfig('config');
            
            $sort = explode('|', @$config['be_mediapool_sort']);
                $sort_f = strtolower(@$sort[0]);	//Sortierfeld
                $sort_c = strtoupper(@$sort[1]);	//Sortierrichtung asc|desc
            
            $subject = $ep->getSubject();
            
                if ($sort_f == 'name'):
                    $subject = str_replace("m.updatedate", "m.filename ".strtoupper($sort_c).", m.updatedate", $subject);
                elseif ($sort_f == 'createdate'):
                    $subject = str_replace("m.updatedate", "m.createdate ".strtoupper($sort_c).", m.updatedate", $subject);
                elseif ($sort_f == 'updatedate'):
                    $subject = str_ireplace("m.updatedate desc", "m.updatedate ".strtoupper($sort_c), $subject);
                endif;
                
            return $subject;
        });        
    endif;


	//Tree einbinden
	$tmp = @$config['be_tree'];
	if (preg_match("/(top|left|right)/i", $tmp)):
		rex_extension::register('PACKAGES_INCLUDED', function($ep){
			global $a1510_mypage;
			global $a1510_darkmode;
			
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
					if ($a1510_darkmode) { rex_view::addCssFile($this->getAssetsUrl('rextree/rextree-darkmode.css')); }

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