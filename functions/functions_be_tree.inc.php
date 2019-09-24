<?php
/*
	Redaxo-Addon Backend-Tools
	Backend-Funktionen (Tree)
	v1.3
	by Falko Müller @ 2018-2019 (based on 1.0@rex4)
	package: redaxo5
*/

//aktive Session prüfen


//globale Variablen


//Funktionen
function a1510_showTree($ep)
{	global $a1510_mypage;

	//Vorgaben einlesen
	$op = $ep->getSubject();										//Content des ExtPoint (z.B. Seiteninhalt)
	//$artid = $ep->getParams('article_id');							//Umgebungsparameter des Ex.Points (z.B. article_id | clang)

	$config = rex_addon::get($a1510_mypage)->getConfig('config');			//Addon-Konfig einladen
		$rtPO = ($config['be_tree'] == 'top') ? 'top' : 'left';
		$rtAM = ($config['be_tree_activemode'] == 'checked') ? 1 : 0;
		$rtPS = ($config['be_tree_persist'] == 'checked') ? ',"state"' : '';
			$rtPSs = ($config['be_tree_persist'] == 'checked') ? '"state": {"key": "rextreePersist", "events": "activate_node.jstree"},' : '';
	$cnt = "";
	

	//Tree erzeugen
	$cnt .= '<div class="rextree" id="rextree">';
	$cnt .= ($rtPO == 'top') ? '<span class="rtpanel"><i class="rex-icon fa-angle-double-up"></i></span>' : '<span class="rtpanel"><i class="rex-icon fa-angle-double-left"></i></span>';
	$cnt .= '<h4><a href="index.php?page=structure&amp;clang='.rex_clang::getCurrentId().'">'.rex_i18n::msg('a1510_tree_name').'</a></h4>';
	$cnt .= '<div id="jstree">';
		$cnt .= a1510_getRexTree();
	$cnt .= '</div>';	
	$cnt .= '</div>';
		
	$cnt .= '<script type="text/javascript">
	$(function(){
		var rtPosition = "'.$rtPO.'";
		var rtActiveMode = '.$rtAM.';
		var rtActive = rtUpdated = false;
		var rtQuery = new URLSearchParams(window.location.search);
		var rtLoaded = true;
		var rextree = $("#rextree");
		var rextreejs = rextree.find("#jstree");
		
		//check if files loaded		
		try { rextreejs.jstree({'.$rtPSs.' "plugins": ["wholerow"'.$rtPS.']}); }
		catch(e) { rtLoaded = false; }
		
		//set & init rexTree
		if (!rtLoaded) { rextree.hide(); }
		else {
			$(document).on("rex:ready", function(){	getRexTree(); });
			getRexTree();
			
			//rtPanel
			$("span.rtpanel").click(function(){
				if (rtPosition == "top") {
					//embed @top
					if ($(this).find("i").hasClass("fa-angle-double-down")) {
						//show it
						rextree.removeClass("rthideTop").find("#jstree").animate({height: 250}, 300);
						$(this).find("i").removeClass("fa-angle-double-down");
						Cookies.set("rextree", "show");
					} else {
						//hide it
						rextree.addClass("rthideTop").find("#jstree").animate({height: 0}, 300);
						$(this).find("i").addClass("fa-angle-double-down");
						Cookies.set("rextree", "hide");
					}
				} else {
					//embed @left
					if ($(this).find("i").hasClass("fa-angle-double-right")) {
						//show it
						rextree.find("h4, #jstree").fadeOut(0);
						rextree.removeClass("rthide").animate({width: 250}, 300);
							rextree.find("h4, #jstree").fadeIn();
						$(this).find("i").removeClass("fa-angle-double-right");
						Cookies.set("rextree", "show");
					} else {
						//hide it
						rextree.addClass("rthide").animate({width: 52}, 300);
							rextree.find("h4, #jstree").fadeOut();
						$(this).find("i").addClass("fa-angle-double-right");
						Cookies.set("rextree", "hide");
					}
				}
			});
			//state @reload
			if (Cookies.get("rextree") == "hide") {
				if (rtPosition == "top") { rextree.addClass("rthideTop").find(".rtpanel > i").addClass("fa-angle-double-down"); }
				else { rextree.addClass("rthide").find(".rtpanel > i").addClass("fa-angle-double-right"); }
			}
		}
		
		function getRexTree()
		{	//get & set params
			rtQuery = new URLSearchParams(window.location.search);
				rtPage = rtQuery.get("page");
				rtCat = parseInt(rtQuery.get("category_id"));
					rtCat = (isNaN(rtCat) || rtCat <= 0 ? false : "rtCat"+rtCat);
				rtArt = parseInt(rtQuery.get("article_id"));
					rtCat = (!rtCat && !isNaN(rtArt) && rtArt > 0 ? "rtCat"+rtArt : rtCat);
				rtClang = parseInt(rtQuery.get("clang"));
					rtClang = (isNaN(rtClang) || rtClang <= 1 ? 1 : rtClang);
				rtStart = (rtQuery.get("catstart") == "0" || rtQuery.get("artstart") == "0" ? true : false);
				rtFunc = rtQuery.get("function");
					rtFunc = (rtFunc == "add_cat" || rtFunc == "add_art" ? true : false);
				rtStatus = rtQuery.get("rex-api-call");
					rtStatus = (rtStatus == "category_status" || rtFunc == "article_status" ? true : false);
			rtUpdated = (rtFunc ? false : rtUpdated);
			
			//reload site @change-language (rex_clang::getCurrentId() has the old ID if not reloaded)
			if ('.rex_clang::getCurrentId().' != rtClang) { window.location.reload(); }
				
			//init tree
			if (!rtActive) {
				rextreejs.jstree({'.$rtPSs.' "plugins": ["wholerow"'.$rtPS.']});
				rextreejs.on("activate_node.jstree", function(e,data){ window.location.href = data.node.a_attr.href; });
				var rtObj = rextree.find("#jstree").jstree(true);
				rtActive = true;
			}
			//open tree path
			if (rtActiveMode && rtActive) {
				rtObj = rextreejs.jstree(true);
				//bei Klick in Struktur
				if (rtObj && rtPage == "structure" || rtPage.search("content/") >= 0) {
					if (!rtCat) { rtObj.close_all(); } 
					else { rtObj.open_node(rtCat); rtObj._open_to(rtCat); }
				}
			}		
			//update tree
			if ( (rtActiveMode && rtActive && !rtUpdated && rtStart && (!rtFunc || rtStatus)) ) {
				rextreejs.load("", "rex-api-call=a1510_getStructure", function(){
					rtUpdated = true;
					rextreejs.jstree("destroy", true);
					rtActive = false;
					getRexTree();
				});	
			}
		}
	});
	</script>';
	
	
	$search = '<div class="rex-page-main">';
		if ($config['be_tree'] == 'top'):
			$cnt = $search."\n".$cnt;
			$op = str_replace($search, $cnt, $op);
			$op = str_replace('class="rex-page"', 'class="rex-page rextree-top"', $op);
		elseif ($config['be_tree'] == 'left'):
			$cnt .= $search."\n";
			$op = str_replace($search, $cnt, $op);
			$op = str_replace('class="rex-page"', 'class="rex-page rextree-left"', $op);
		endif;

	return $op;
}


//Hilfsfunktionen
function a1510_getRexTree($lev = 0)
{	global $a1510_mypage;

	//Variablen deklarieren
	$cnt = "";
	$actArt = rex_request('article_id', 'int');
	$actCat = rex_request('category_id', 'int');
		$actCat = ($actArt > 0) ? 0 : $actCat;
	$actClang = rex_request('clang', 'int');

	//Vorgaben einlesen
	$config = rex_addon::get($a1510_mypage)->getConfig('config');			//Addon-Konfig einladen
	$maxchars = 25;
	
	//Kategorien + Artikel durchlaufen
	$cnt .= "<ul>";
	
	//Kategorien
	$cats = (empty($lev)) ? rex_category::getRootCategories(false, $actClang) : $lev->getChildren();
	foreach ($cats as $cat):
		$oid = $cat->getId();
		$oname = $cat->getName();
		
		$css = "";
			$css .= ($cat->isOnline()) ? "online" : "offline";
			
			$name = a1510_getName($oname, $oid);
			$state = ($cat->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=structure&amp;category_id='.$oid.'&amp;clang='.$cat->getClang().'">'.$name.'</a>';
			
			$cnt .= '<li id="rtCat'.$oid.'" class="folder '.$css.'" title="'.$title.'">'.$link;
				$cnt .= a1510_getRexTree($cat);
			$cnt .= '</li>';
	endforeach;
	
	//Artikel
	$arts = (empty($lev)) ? rex_article::getRootArticles(false, rex_clang::getCurrentId()) : $lev->getArticles(false);
	foreach ($arts as $art):
		$oid = $art->getId();
		$oname = $art->getName();

		$css = "";
			$css .= ($art->isOnline()) ? "online" : "offline";
			$css .= ($art->isStartArticle()) ? " startarticle" : "";
			$css .= ($art->isSiteStartArticle()) ? " sitestartarticle" : "";
	
			$name = a1510_getName($oname, $oid);
			$state = ($art->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=content/edit&amp;article_id='.$oid.'&amp;mode=edit&amp;clang='.$art->getClang().'&amp;category_id='.$art->getCategoryId().'">'.$name.'</a>';
			
			$cnt .= '<li id="rtArt'.$oid.'" class="file '.$css.'" title="'.$title.'">'.$link.'</li>';
	endforeach;                 			    

	$cnt .= '</ul>';
	
	return $cnt;
}

function a1510_getName($str, $id, $isTitle = false)
{	global $a1510_mypage;

	//Variablen deklarieren
	$maxchars = 25;
	
	//Vorgaben einlesen
	$config = rex_addon::get($a1510_mypage)->getConfig('config');			//Addon-Konfig einladen

	//Namen des Eintrages holen
	$str = trim($str);
	$name = $str;
		$name = (empty($name) && !$isTitle) ? '<span class="emptyname_art">'.rex_i18n::msg('a1510_tree_emptyname').'</span>' : $name;
			$name = (empty($name) && $isTitle) ? rex_i18n::msg('a1510_tree_emptyname') : $name;
		$name = ($config['be_tree_shortnames'] == 'checked' && strlen($name) > $maxchars && !$isTitle) ? substr($name, 0,$maxchars)."..." : $name;
		
		if ($config['be_tree_showid'] == 'checked' && !$isTitle):
			$maxchars = $maxchars-5;
			$name = ($config['be_tree_shortnames'] == 'checked' && strlen($str) > $maxchars) ? substr($str, 0,$maxchars)."..." : $name;
			$name .= ' <span class="rtid">['.intval($id).']</span>';
		endif;
	$str = $name;

	return $str;
}


//rexAPI Klassen-Erweiterung (Ajax-Abfrage)
class rex_api_a1510_getStructure extends rex_api_function
{	protected $published = false;		//true = auch im Frontend

	function execute()
	{	//Ajax-URL-Parameter einlesen
		//$var = rex_request('var', 'int');
		
		//Kategorien + Artikel auslesen
		$op = a1510_getRexTree();
		
		//Ajax-Rückgabe
		header('Content-type: text/html; charset=UTF-8');
		exit($op);		//Rückgabe ausgeben + Anfrage beenden
	}
}
?>