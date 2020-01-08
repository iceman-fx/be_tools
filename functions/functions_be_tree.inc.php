<?php
/*
	Redaxo-Addon Backend-Tools
	Backend-Funktionen (Tree)
	v1.4.6
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
	
	$versAddon = (rex_plugin::get('structure', 'version')->isAvailable()) ? "true" : "false";
	
	
	//Tokens holen
	$tok_catdel = rex_api_category_delete::getUrlParams();
	$tok_catstat = rex_api_category_status::getUrlParams();
	$tok_artdel = rex_api_article_delete::getUrlParams();
	$tok_artstat = rex_api_article_status::getUrlParams();
	

	//Tree erzeugen
	$cnt .= '<div class="rextree" id="rextree">';
	$cnt .= ($rtPO == 'top') ? '<span class="rtpanel"><i class="rex-icon fa-angle-double-up"></i></span>' : '<span class="rtpanel"><i class="rex-icon fa-angle-double-left"></i></span>';
	$cnt .= '<h4><a href="index.php?page=structure&amp;clang='.rex_clang::getCurrentId().'">'.rex_i18n::msg('a1510_tree_name').'</a></h4>';
	$cnt .= '<div id="jstree">';
		//$cnt .= a1510_getRexTree();
	$cnt .= '</div>';	
	$cnt .= '</div>';
			
	$cnt .= '<script type="text/javascript">
	$(function(){
		var rtVersAddon = '.$versAddon.';
		var rtPosition = "'.$rtPO.'";
		var rtActiveMode = '.$rtAM.';
		var rtActive = rtUpdated = false;
		var rtQuery = new URLSearchParams(window.location.search);
		var rtLoaded = true;
		var rextree = $("#rextree");
		var rextreejs = rextree.find("#jstree");
		
		//check if files loaded		
		try { rextreejs.jstree({'.$rtPSs.' "plugins": ["wholerow"'.$rtPS.']}); rextreejs.jstree("destroy", true); }
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
				rextreejs.jstree({
					"core": { "check_callback": true, "data": { "url": window.location.href+"&rex-api-call=a1510_getStructure", "data": function(nodes){} }},
					'.$rtPSs.' "plugins": ["contextmenu", "wholerow"'.$rtPS.'],
					"contextmenu": {
						items: function(node){
							//var tree = rextreejs.jstree(true);
							$node = $("#"+node.id);
							$path = $node.attr("data-path");
								$path = $path.split("|");
							$rights = $node.attr("data-rights");
								if (typeof $rights === typeof undefined || $rights === false) { $rights = ""; }
							
							$aid = 	parseInt($path[0]);
							$cid = 	parseInt($path[1]);
							$pcid = parseInt($path[2]);
							
							//urls
							var url_liveview = url_preview = "";
								$.get("", "rex-api-call=a1510_getUrl&aid="+$aid+"&cid="+$cid+"&preview=1", function(data){
									url_preview = data;
									url_liveview = url_preview.replace(new RegExp("[\?&]rex_version=1","gmi"), "");
								});
							
							var url_open 		= "./?page=content/edit&article_id="+$aid+"&clang="+$cid+"&mode=edit";
							var url_onoff		= "./?page=structure&category_id="+$pcid+"&article_id="+$aid+"&clang="+$cid+"&rex-api-call='.$tok_artstat['rex-api-call'].'&_csrf_token='.$tok_artstat['_csrf_token'].'";
							var url_prop 		= "./?page=structure&category_id="+$pcid+"&article_id="+$aid+"&clang="+$cid+"&function=edit_art";
							var url_func 		= "./?page=content/functions&article_id="+$aid+"&clang="+$cid;
							var url_meta 		= "./?page=content/metainfo&article_id="+$aid+"&clang="+$cid;
							var url_del 		= "./?page=structure&category_id="+$pcid+"&article_id="+$aid+"&clang="+$cid+"&rex-api-call='.$tok_artdel['rex-api-call'].'&_csrf_token='.$tok_artdel['_csrf_token'].'";
							
							var urlC_open 		= "./?page=structure&category_id="+$aid+"&clang="+$cid;
							var urlC_addC 		= "./?page=structure&category_id="+$aid+"&clang="+$cid+"&function=add_cat";
							var urlC_addA 		= "./?page=structure&category_id="+$aid+"&clang="+$cid+"&function=add_art";
							var urlC_onoff 		= "./?page=structure&category_id="+$pcid+"&category-id="+$aid+"&clang="+$cid+"&rex-api-call='.$tok_catstat['rex-api-call'].'&_csrf_token='.$tok_catstat['_csrf_token'].'";
							var urlC_prop 		= "./?page=structure&category_id="+$pcid+"&clang="+$cid+"&edit_id="+$aid+"&function=edit_cat";
							var urlC_del 		= "./?page=structure&category_id="+$pcid+"&category-id="+$aid+"&clang="+$cid+"&rex-api-call='.$tok_catdel['rex-api-call'].'&_csrf_token='.$tok_catdel['_csrf_token'].'";
							
							//items
							var items;
								if ($aid > 0 && $cid > 0 && $pcid >= 0) {
									if ($node.hasClass("folder")) {
										//Categorys
										items = {
											openItem: { label: "'.rex_i18n::msg('a1510_tree_cm_open').'", 				action: function(){ window.open(urlC_open, "_blank"); }, 	separator_after: true, icon: "rex-icon fa-external-link" },
												addcatItem: { label: "'.rex_i18n::msg('a1510_tree_cm_cataddcat').'", 	action: function(){ window.location.href = urlC_addC; } },
												addartItem: { label: "'.rex_i18n::msg('a1510_tree_cm_cataddart').'", 	action: function(){ window.location.href = urlC_addA; } },
												onlineItem: { label: "'.rex_i18n::msg('a1510_tree_cm_online').'", 		action: function(){ window.location.href = urlC_onoff; }, 	separator_before: true, icon: "rex-icon fa-eye" },
												offlineItem: { label: "'.rex_i18n::msg('a1510_tree_cm_offline').'", 	action: function(){ window.location.href = urlC_onoff; }, 	separator_before: true, icon: "rex-icon fa-eye-slash" },
												propertyItem: { label: "'.rex_i18n::msg('a1510_tree_cm_property').'", 	action: function(){ window.location.href = urlC_prop; }, 	icon: "rex-icon fa-cog" },
											deleteItem: { label: "'.rex_i18n::msg('a1510_tree_cm_catdelete').'", 		action: function(){ var rtConfirm = confirm("löschen ?"); if (rtConfirm == true) { window.location.href = urlC_del; } }, separator_before: true, icon: "rex-icon fa-times rextree-icon-red" }
										};
									} else {
										//Articles
										items = {
											previewItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artpreview').'", 		action: function(){ window.open(url_preview, "_blank"); }, 	icon: "rex-icon fa-external-link" },
											liveviewItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artliveview').'", 	action: function(){ window.open(url_liveview, "_blank"); },	separator_after: true, icon: "rex-icon fa-external-link" },
												editItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artedit').'", 		action: function(){ window.location.href = url_open; },		icon: "rex-icon rex-icon-editmode" },
												openItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artopen').'", 		action: function(){ window.open(url_open, "_blank"); },		separator_after: true },
												onlineItem: { label: "'.rex_i18n::msg('a1510_tree_cm_online').'", 		action: function(){ window.location.href = url_onoff; }, 	icon: "rex-icon fa-eye" },
												offlineItem: { label: "'.rex_i18n::msg('a1510_tree_cm_offline').'", 	action: function(){ window.location.href = url_onoff; }, 	icon: "rex-icon fa-eye-slash" },
												propertyItem: { label: "'.rex_i18n::msg('a1510_tree_cm_property').'", 	action: function(){ window.location.href = url_prop; }, 	icon: "rex-icon fa-cog" },
												funcItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artfunc').'", 		action: function(){ window.location.href = url_func; },		icon: "rex-icon rex-icon-metafuncs" },
												metaItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artmeta').'", 		action: function(){ window.location.href = url_meta; },		icon: "rex-icon rex-icon-metainfo" },
											deleteItem: { label: "'.rex_i18n::msg('a1510_tree_cm_artdelete').'", 		action: function(){ var rtConfirm = confirm("'.rex_i18n::msg('a1510_tree_cm_confirm').'"); if (rtConfirm == true) { window.location.href = url_del; } }, separator_before: true, icon: "rex-icon fa-times rextree-icon-red" }
										};
									}
									
									//delete unused items
									if ( ($node.hasClass("file") && $rights.indexOf("onoffArt") == -1) || ($node.hasClass("folder") && $rights.indexOf("onoffCat") == -1) || $node.hasClass("online") || $node.hasClass("startarticle") ) { delete items.onlineItem; }
									if ( ($node.hasClass("file") && $rights.indexOf("onoffArt") == -1) || ($node.hasClass("folder") && $rights.indexOf("onoffCat") == -1) || $node.hasClass("offline") || $node.hasClass("startarticle") ) { delete items.offlineItem; }
									if ( $node.hasClass("startarticle") ) { delete items.deleteItem; }
									if (!rtVersAddon) { delete items.previewItem; }
								}
								
							return items;
						}
					}
				});				
				rextreejs.on("activate_node.jstree", function(e,data){ /*console.log(data);*/ if (data.event.type == "click") { window.location.href = data.node.a_attr.href; } });
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
	$isAdmin = ( rex::getUser()->isAdmin() ) ? true : false;

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
		$cid = $cat->getClang();
		$pcid = (!empty($lev)) ? $cat->getParent()->getId() : 0;
		
		if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($oid)) { continue; }				//Kategorie ohne User-Berechtigung ausblenden
		
		$css = "";
			$css .= ($cat->isOnline()) ? "online" : "offline";
			
			$name = a1510_getName($oname, $oid);
			$state = ($cat->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=structure&amp;category_id='.$oid.'&amp;clang='.$cid.'">'.$name.'</a>';
			
			$rights = array();
				if ($isAdmin || rex::getUser()->hasPerm('publishArticle[]')) { array_push($rights, "onoffArt"); }
				if ($isAdmin || rex::getUser()->hasPerm('publishCategory[]')) { array_push($rights, "onoffCat"); }
			$rights = implode("|", $rights);
			
			$cnt .= '<li id="rtCat'.$oid.'" class="folder '.$css.'" title="'.$title.'" data-aid="'.$oid.'" data-cid="'.$cid.'" data-path="'.$oid.'|'.$cid.'|'.$pcid.'" data-rights="'.$rights.'">'.$link;
				$cnt .= a1510_getRexTree($cat);
			$cnt .= '</li>';
	endforeach;
	
	//Artikel
	$arts = (empty($lev)) ? rex_article::getRootArticles(false, rex_clang::getCurrentId()) : $lev->getArticles(false);
	foreach ($arts as $art):
		$oid = $art->getId();
		$oname = $art->getName();
		$cid = $art->getClang();
		$pcid = $art->getCategoryId();
		//$pcid = $art->getParent()->getId();

		$css = "";
			$css .= ($art->isOnline()) ? "online" : "offline";
			$css .= ($art->isStartArticle()) ? " startarticle" : "";
			$css .= ($art->isSiteStartArticle()) ? " sitestartarticle" : "";
	
			$name = a1510_getName($oname, $oid);
			$state = ($art->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=content/edit&amp;article_id='.$oid.'&amp;mode=edit&amp;clang='.$cid.'&amp;category_id='.$art->getCategoryId().'">'.$name.'</a>';
			
			$rights = array();
				if ($isAdmin || rex::getUser()->hasPerm('publishArticle[]')) { array_push($rights, "onoffArt"); }
				if ($isAdmin || rex::getUser()->hasPerm('publishCategory[]')) { array_push($rights, "onoffCat"); }
			$rights = implode("|", $rights);
			
			$cnt .= '<li id="rtArt'.$oid.'" class="file '.$css.'" title="'.$title.'" data-aid="'.$oid.'" data-cid="'.$cid.'" data-path="'.$oid.'|'.$cid.'|'.$pcid.'" data-rights="'.$rights.'">'.$link.'</li>';
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
	{	//Kategorien + Artikel auslesen
		$op = a1510_getRexTree();
		
		//Ajax-Rückgabe
		header('Content-type: text/html; charset=UTF-8');
		exit($op);		//Rückgabe ausgeben + Anfrage beenden
	}
}

class rex_api_a1510_getUrl extends rex_api_function
{	protected $published = false;		//true = auch im Frontend

	function execute()
	{	//Ajax-URL-Parameter einlesen
		$aid = rex_request('aid', 'int');
		$cid = rex_request('cid', 'int');
		$preview = rex_request('preview', 'int');
			$preview = ($preview == 1) ? array("rex_version"=>1) : array();
		
		//Kategorien + Artikel auslesen
		$op = (rex_addon::get('yrewrite')->isAvailable()) ? rex_yrewrite::getFullUrlByArticleId($aid, $cid, $preview, '&') : rex_getUrl($aid, $cid, $preview, '&');
		
		//Ajax-Rückgabe
		header('Content-type: text/html; charset=UTF-8');
		exit($op);		//Rückgabe ausgeben + Anfrage beenden
	}
}
?>