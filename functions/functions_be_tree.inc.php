<?php
/*
	Redaxo-Addon Backend-Tools
	Backend-Funktionen (Tree)
	v1.6.2
	by Falko Müller @ 2018-2021 (based on 1.0@rex4)
	package: redaxo5
*/

//aktive Session prüfen


//globale Variablen


//Funktionen
function a1510_showTree($ep)
{	global $a1510_mypage;

	//Vorgaben einlesen
	$op = $ep->getSubject();																	//Content des ExtPoint (z.B. Seiteninhalt)
	//$artid = $ep->getParams('article_id');													//Umgebungsparameter des Ex.Points (z.B. article_id | clang)

	$config = rex_addon::get($a1510_mypage)->getConfig('config');								//Addon-Konfig einladen
		$rtPO = $config['be_tree'];
		$rtAM = ($config['be_tree_activemode'] == 'checked') ? 1 : 0;
		$rtPM = ($config['be_tree_persist'] == 'checked') ? 1 : 0;
		$rtPS = ($config['be_tree_persist'] == 'checked') ? ',"state"' : '';
			$rtPSs = ($config['be_tree_persist'] == 'checked') ? '"state": {"key": "rextreePersist", "events": "activate_node.jstree"},' : '';
	$cnt = "";
	
	$versAddon = (rex_plugin::get('structure', 'version')->isAvailable()) ? "true" : "false";
	$rexLT510 = (!class_exists('rex_version')) ? "true" : "false";
	
	
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
		//$cnt .= a1510_getRexTree();															//nur für Testzwecke
	$cnt .= '</div>';	
	$cnt .= '</div>';
			
	$cnt .= '<script type="text/javascript">
	$(function(){
		var rtRexLT510 = '.$rexLT510.';
		var rtVersAddon = '.$versAddon.';
		var rtPosition = "'.$rtPO.'";
		var rtActiveMode = '.$rtAM.';
		var rtPersistMode = '.$rtPM.';
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
				dsti = $(this).find("i");
				if (rtPosition == "top") {
					//embed @top
					if (dsti.hasClass("fa-angle-double-down")) {
						//show it
						rextree.removeClass("rthideTop").find("#jstree").animate({height: 250}, 300);
						dsti.removeClass("fa-angle-double-down");
						Cookies.set("rextree", "show");
					} else {
						//hide it
						rextree.addClass("rthideTop").find("#jstree").animate({height: 0}, 300);
						dsti.addClass("fa-angle-double-down");
						Cookies.set("rextree", "hide");
					}
				} else {
					//embed @left or @right
					if (dsti.hasClass("fa-angle-double-right")) {
						//show it
						rextree.find("h4, #jstree").fadeOut(0);
						rextree.removeClass("rthide").animate({width: 250}, 300);
							rextree.find("h4, #jstree").fadeIn();
						dsti.removeClass("fa-angle-double-right");
						Cookies.set("rextree", "show");
					} else {
						//hide it
						rextree.addClass("rthide").animate({width: 52}, 300);
							rextree.find("h4, #jstree").fadeOut();
						dsti.addClass("fa-angle-double-right");
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
						
			if (rtQuery.get("page") != null) {
				rtPage = rtQuery.get("page");																		//aktuelle Seite
				rtCat = parseInt(rtQuery.get("category_id"));														//aktuelle Kategorie
					rtCat = (isNaN(rtCat) || rtCat <= 0 ? false : "rtCat"+rtCat);
				rtArt = parseInt(rtQuery.get("article_id"));														//aktueller Artikel
					rtCat = (!rtCat && !isNaN(rtArt) && rtArt > 0 ? "rtCat"+rtArt : rtCat);
				rtClang = parseInt(rtQuery.get("clang"));															//aktuelle Sprache
					rtClang = (isNaN(rtClang) || rtClang <= 1 ? 1 : rtClang);
				rtStart = (rtQuery.get("catstart") == "0" || rtQuery.get("artstart") == "0" ? true : false);		//Param artstart oder catstart
				rtFunc = rtQuery.get("function");																	//Param function
					rtFunc = (rtFunc == "add_cat" || rtFunc == "add_art" ? true : false);									//add_art || add_cat
				rtStatus = rtQuery.get("rex-api-call");																//Param für Status-Änderung
					rtStatus = (rtStatus == "category_status" || rtFunc == "article_status" ? true : false);				//category_status || article_status
				rtUpdated = (rtFunc ? false : rtUpdated);															//ist Update erfolgt
			} else {
				//defaults
				rtPage = "";
				rtCat = rtArt = 0;
				rtClang = '.rex_clang::getCurrentId().';
				rtStart = rtFunc = rtStatus = rtUpdated = false;
			}
			
			
			//reload site @change-language (rex_clang::getCurrentId() has the old ID if not reloaded)
			if ('.rex_clang::getCurrentId().' != rtClang) { window.location.reload(); }
				
			//init tree
			if (!rtActive) {
				var apiurl = window.location.href;
					apiurl = apiurl.replace(/[\&]+rex-api-call=[^\&]*/i, "");										//remove old api-calls
					apiurl = apiurl.replace(/[\&]+rex-api-result=[^\&]*/i, "");										//remove old api-results
					apiurl = apiurl.replace(/#.*/i, "");															//remove #-anchor
					
					apiurl += (apiurl.indexOf("?") < 0 ? "?rtparams=0" : "");										//add missing "?"
					apiurl += (rtPage == "" ? "&page=structure" : "");												//add missing "page="
					apiurl += "&rex-api-call=a1510_getStructure";													//add my api-call
					
				
					/* 	"conditionalselect": function(node,e){ return false; },	*/
				rextreejs.jstree({
					"core": {
						"check_callback": function (e,node,node_parent,node_pos,more) {
							nid = node.id; npid = node_parent.id;							
							
							if ( e == "move_node" ) {
								//if ( more && more.dnd && more.pos !== "i" ) { return false; }						//disallow re-ordering
								if ( node.parent !== node_parent.id ) { return false; }								//move only at the same level
							}
							if ( e == "copy_node") { return false; }												//disallow copy mode

							var pnode = this.get_prev_dom(node, true);
								if (typeof(pnode) == "object") { pnode = pnode.get(0); }
							var nnode = this.get_next_dom(node, true);
								if (typeof(nnode) == "object") { nnode = nnode.get(0); }
							
							//var t = pnode.get(0);
							//console.log(t.id);
							
							
							//mach was mit dem dragNdrop
							/*
							console.log("--- zu verschiebender node -----");
							console.log(node.id);
							console.log("--- node vor der neuen Position -----"); 
							console.log(pnode.id);
							console.log("--- node nach der neuen Position -----"); 
							console.log(nnode.id);
							*/
							
							//var t = this.get_node(pnode);							
							//console.log(t.id);
							
							//console.log(nid.substr(0,5)+" / "+npid.substr(0,5));
							
							return true;
							
							/* '.$rtPSs.' "plugins": ["dnd", "contextmenu", "conditionalselect", "wholerow"'.$rtPS.'], */
						},					
						"data": { "url": apiurl, "data": function(nodes){} }},
					'.$rtPSs.' "plugins": ["contextmenu", "conditionalselect", "wholerow"'.$rtPS.'],
					"dnd": {
						open_timeout: 99999999999,
						use_html5: true
					},
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
												propertyItem: { label: "'.rex_i18n::msg('a1510_tree_cm_property').'", 	action: function(){ window.location.href = urlC_prop; }, 	separator_before: true, icon: "rex-icon fa-cog" },
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
									
									//check rights & unset items
									//category
									if ( $node.hasClass("folder") && $rights.indexOf("deleteCat") == -1 ) 	{ delete items.deleteItem; }
									if ( $node.hasClass("folder") && $rights.indexOf("changeCat") == -1 ) 	{ delete items.propertyItem; }
									if ( $node.hasClass("folder") && $rights.indexOf("onoffCat") == -1 ) 	{ delete items.onlineItem; delete items.offlineItem; }
									if ( $node.hasClass("folder") && $rights.indexOf("addCat") == -1 ) 		{ delete items.addcatItem; }
									if ( $node.hasClass("folder") && $rights.indexOf("addArt") == -1 ) 		{ delete items.addartItem; }
									
									//article
									if ( ($node.hasClass("file") && $rights.indexOf("deleteArt") == -1) ) 	{ delete items.deleteItem; }
									if ( ($node.hasClass("file") && $rights.indexOf("changeArt") == -1) ) 	{ delete items.propertyItem; }
									if ( ($node.hasClass("file") && $rights.indexOf("onoffArt") == -1) ) 	{ delete items.onlineItem; delete items.offlineItem; }
									if ( ($node.hasClass("file") && $rights.indexOf("addArt") == -1) ) 		{ delete items.addartItem; }
									if ( ($node.hasClass("file") && $rights.indexOf("funcArt") == -1) ) 	{ delete items.funcItem; }

									//special
									if ( $node.hasClass("online") ) 										{ delete items.onlineItem; }
									if ( $node.hasClass("offline") ) 										{ delete items.offlineItem; }
									if ( $node.hasClass("startarticle") ) 									{ delete items.deleteItem; delete items.onlineItem; delete items.offlineItem; }
									if (!rtVersAddon) { delete items.previewItem; }
									if (!rtRexLT510) { delete items.metaItem; }
								}
								
							return items;
						}
					}
				});				
				var rtObj = rextree.find("#jstree").jstree(true);
				rextreejs.on("activate_node.jstree", function(e,data){ if (data.event.type == "click") { rtObj.deselect_node(data.node.id); window.location.href = data.node.a_attr.href; } });
				rtActive = true;
			}
			
			//open tree path
			if (rtActiveMode && rtActive) {
				rtObj = rextreejs.jstree(true);
				//bei Klick in Struktur
				
				/*
				console.log("> " +rtPage);
				console.log(rtPage.search("content/"));
				*/				
				
				if (rtObj && rtPage == "structure" || rtPage.search("content/") >= 0) {
					rextreejs.on("loaded.jstree", function(e,data){
						//console.log(rtCat);
						if (!rtCat && !rtPersistMode) { rtObj.close_all(); } 
						else { if (!rtPersistMode) { rtObj.close_all(rtCat); } rtObj.open_node(rtCat); rtObj._open_to(rtCat); rtObj.deselect_all(); }
					});
					rtObj.deselect_all();
					rextreejs.trigger("loaded.jstree");
				}
			}
			//update tree
			if ( (rtActive && rtActiveMode && !rtUpdated && rtStart && (!rtFunc || rtStatus)) ) {
				rextreejs.load("", "rex-api-call=a1510_getStructure", function(){
					//console.log("> refresh tree");
					rtUpdated = true;
					rextreejs.jstree("destroy", true);
					rtActive = false;
					getRexTree();
				});	
			}
		}
		
		//rextree@right on_resize
		$(window).on("resize", function(){
			if (rtPosition == "right"){
				if ($(window).width() <= 991) { rextree.insertBefore(".rex-page-main"); }
				else { rextree.insertAfter(".rex-page-main"); }
			}
		});
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
		elseif ($config['be_tree'] == 'right'):
			$search = '#</div>[\s]*<footer class="rex-global-footer#im';
			$cnt .= "\n".'</div><footer class="rex-global-footer';
			$op = preg_replace($search, $cnt, $op);
			$op = str_replace('class="rex-page"', 'class="rex-page rextree-left rextree-right"', $op);
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
	$hasMPoints = rex::getUser()->getComplexPerm('structure')->hasMountpoints();	

	//Vorgaben einlesen
	$config = rex_addon::get($a1510_mypage)->getConfig('config');			//Addon-Konfig einladen
	$maxchars = 25;
	
	
	//Kategorien + Artikel durchlaufen
	$cnt .= '<ul>';
	
	//Kategorien
	if ($hasMPoints && empty($lev)):
		//nur die gewählten Kategorien durchlaufen
		$_SESSION['be_tree']['lastcats'] = array();
		$cats = rex::getUser()->getComplexPerm('structure')->getMountpoints();									//=array
		
		//var_dump($cats);
	else:
		//alle Kategorien durchlaufen
		$cats = (empty($lev)) ? rex_category::getRootCategories(false, $actClang) : $lev->getChildren();		//=object
	endif;
		
	foreach ($cats as $cat):
		$cat = ($hasMPoints && !is_object($cat)) ? rex_category::get($cat) : $cat;
	
		$oid = $cat->getId();
		$oname = $cat->getName();
		$cid = $cat->getClang();
		//$pcid = (!empty($lev)) ? $cat->getParent()->getId() : 0;
		$pcid = $cat->getValue('parent_id');
		$hasCatPerm = rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($pcid);
		
		
		$show = (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($oid)) ? true : false;			//Kategorie ohne User-Berechtigung ausblenden
		if ($hasMPoints && empty($lev) && in_array($pcid, $_SESSION['be_tree']['lastcats'])) { continue; }		//Mountpoint ausblenden, wenn parentCat bereits vorhanden
				
		$css = "";
			$css .= ($cat->isOnline()) ? ' online' : ' offline';
			$css .= (sizeof($cat->getChildren()) > 0) ? ' haschilds' : ' nochilds';
			
			$name = a1510_getName($oname, $oid);
			$state = ($cat->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=structure&amp;category_id='.$oid.'&amp;clang='.$cid.'">'.$name.'</a>';
			
			
			//Addon StructureTweaks berücksichtigen
			$hide_status = $hide_func = false;
			if (rex_addon::get('structure_tweaks')->isAvailable()):
				/*
				Typ									gesetzte CSS-Klasse										DB-Wert								Bemerkung
				------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				kat ausblenden 						.structure-tweaks-category.is-hidden				 	hide_categories				
																											hide_categories_non_admin			außer Admins
				kat status deaktivieren:			structure-tweaks-status									hide_cat_functions					Button on/offline ist entfernt
																											hide_cat_functions_non_admin		außer Admins
				kat funktionen deaktivieren:		structure-tweaks-delete / structure-tweaks-status		hide_cat_functions_all				Button löschen + on/offline ist entfernt
																											hide_cat_functions_all_non_admin	außer Admins
				kat startartikel ausblenden			.rex-startarticle.is-hidden								hide_startarticle			
																											hide_startarticle_non_admin			außer Admins
				*/
			
				$db = rex_sql::factory();
				$tweaks = $db->getArray("SELECT type FROM ".rex::getTable('structure_tweaks')." WHERE article_id = '".$oid."'");
				
				foreach ($tweaks as $tweak):
					//$css .= ($tweak['type'] == 'hide_categories' || ($tweak['type'] == 'hide_categories_non_admin' && !$isAdmin)) ? ' structure-tweaks-category is-hidden' : '';
					if ($tweak['type'] == 'hide_categories' || ($tweak['type'] == 'hide_categories_non_admin' && !$isAdmin)) { continue 2; }
					
					$hide_status 	= ($tweak['type'] == 'hide_cat_functions' || ($tweak['type'] == 'hide_cat_functions_non_admin' && !$isAdmin)) ? 			true : false;
					$hide_func 		= ($tweak['type'] == 'hide_cat_functions_all' || ($tweak['type'] == 'hide_cat_functions_all_non_admin' && !$isAdmin)) ? 	true : false;
				endforeach;
			endif;			
			
			
			//Nutzerrechte prüfen und setzen
			$rights = array();
				//if ($isAdmin || (rex::getUser()->hasPerm('publishArticle[]') && $hasCatPerm) ) 									{ array_push($rights, "onoffArt"); }
				if (($isAdmin || rex::getUser()->hasPerm('publishCategory[]')) && !$hide_status && !$hide_func)		{ array_push($rights, "onoffCat"); }
				
				if ($isAdmin || rex::getUser()->hasPerm('addCategory[]') ) 											{ array_push($rights, "addCat"); }
				if ($isAdmin || rex::getUser()->hasPerm('editCategory[]') ) 										{ array_push($rights, "changeCat"); }
				if (($isAdmin || rex::getUser()->hasPerm('deleteCategory[]')) && !$hide_func) 						{ array_push($rights, "deleteCat"); }
				
				if ($isAdmin || rex::getUser()->hasPerm('addArticle[]') ) 											{ array_push($rights, "addArt"); }
			$rights = implode("|", $rights);
			
			
			//Tree-Eintrag ausgeben
			if ($show):	
				$cnt .= '<li id="rtCat'.$oid.'" class="folder '.$css.'" title="'.$title.'" data-aid="'.$oid.'" data-cid="'.$cid.'" data-path="'.$oid.'|'.$cid.'|'.$pcid.'" data-rights="'.$rights.'">'.$link;
				if ($hasMPoints) { @array_push($_SESSION['be_tree']['lastcats'], $oid); }
			endif;
				$cnt .= a1510_getRexTree($cat);				//nächste tiefere Ebene durchlaufen
			$cnt .= ($show) ? '</li>' : '';
	endforeach;
	
	
	//Artikel
	$arts = (empty($lev)) ? rex_article::getRootArticles(false, rex_clang::getCurrentId()) : $lev->getArticles(false);
	foreach ($arts as $art):
		$oid = $art->getId();
		$oname = $art->getName();
		$cid = $art->getClang();
		$pcid = $art->getCategoryId();

		if (!rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($pcid)) { continue; }					//Artikel ohne User-Berechtigung ausblenden

		$css = "";
			$css .= ($art->isOnline()) ? "online" : "offline";
			$css .= ($art->isStartArticle()) ? " startarticle" : "";
			$css .= ($art->isSiteStartArticle()) ? " sitestartarticle" : "";
	
			$name = a1510_getName($oname, $oid);
			$state = ($art->isOnline()) ? "" : " - offline";
			$title = str_replace('"', "&quot;", a1510_getName($oname, $oid, true)).' (ID: '.$oid.$state.')';
			$link = '<a href="index.php?page=content/edit&amp;article_id='.$oid.'&amp;mode=edit&amp;clang='.$cid.'&amp;category_id='.$art->getCategoryId().'">'.$name.'</a>';
			
			
			//Addon StructureTweaks berücksichtigen
			if (rex_addon::get('structure_tweaks')->isAvailable()):
				$db = rex_sql::factory();
				$tweaks = $db->getArray("SELECT type FROM ".rex::getTable('structure_tweaks')." WHERE article_id = '".$oid."'");
				
				foreach ($tweaks as $tweak):
					//$css .= ($tweak['type'] == 'hide_startarticle' || ($tweak['type'] == 'hide_startarticle_non_admin' && !$isAdmin)) ? ' rex-startarticle is-hidden' : '';
					if ($tweak['type'] == 'hide_startarticle' || ($tweak['type'] == 'hide_startarticle_non_admin' && !$isAdmin)) { continue 2; }
				endforeach;
			endif;			

			
			//Nutzerrechte prüfen und setzen
			$rights = array();
				if ($isAdmin || rex::getUser()->hasPerm('publishArticle[]')) 	{ array_push($rights, "onoffArt"); }
				//if ($isAdmin || rex::getUser()->hasPerm('publishCategory[]')) 	{ array_push($rights, "onoffCat"); }
				
				if ($isAdmin || rex::getUser()->hasPerm('addArticle[]')) 		{ array_push($rights, "addArt"); }
				if ($isAdmin || rex::getUser()->hasPerm('editArticle[]')) 		{ array_push($rights, "changeArt"); }
				if ($isAdmin || rex::getUser()->hasPerm('deleteArticle[]')) 	{ array_push($rights, "deleteArt"); }
				
				if ($isAdmin || rex::getUser()->hasPerm('copyContent[]') || rex::getUser()->hasPerm('copyArticle[]') || rex::getUser()->hasPerm('article2category[]') || rex::getUser()->hasPerm('article2startarticle[]') || rex::getUser()->hasPerm('moveArticle[]') || rex::getUser()->hasPerm('moveCategory[]') ) 	{ array_push($rights, "funcArt"); }				
			$rights = implode("|", $rights);
			
			$cnt .= '<li id="rtArt'.$oid.'" class="file '.$css.'" title="'.$title.'" data-aid="'.$oid.'" data-cid="'.$cid.'" data-path="'.$oid.'|'.$cid.'|'.$pcid.'" data-rights="'.$rights.'">'.$link.'</li>';
	endforeach;                 			    

	$cnt .= '</ul>';
	
	$cnt = preg_replace("#(<ul>){2,}#is", "<ul>", $cnt);
	$cnt = preg_replace("#(</ul>){2,}#is", "</ul>", $cnt);
	$cnt = str_replace(array('<ul></ul>', '<ul><ul>', '</ul></ul>'), array('', '<ul>', '</ul>'), $cnt);
	
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