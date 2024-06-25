<?php
/*
	Redaxo-Addon Backend-Tools
	Klasse: Slicertimer
	v1.9.0
	by Falko Müller @ 2018-2024
*/

class be_tools_slicetimer
{
	private static $mypage 		= 'be_tools';
	private static $isAdmin 	= false;


    public function __construct()
    {
		self::$isAdmin = (is_object(rex::getUser()) AND (rex::getUser()->hasPerm(self::$mypage.'[admin]') OR rex::getUser()->isAdmin()) ) ? true : false;
    }


	//Onlinestatus holen
	public static function getOnlineStatus($sid = 0)
	{	
		$sid 	= intval($sid);
		$return = true;
		
		if ($sid > 0):
			$status	= intval(self::getSettings($sid, 'status'));
			$from 	= intval(self::getSettings($sid, 'from'));
			$to 	= intval(self::getSettings($sid, 'to'));
			$now 	= time();
		
			if (be_tools::getConfig('be_slicetimer') == 'checked' && $status == 1):
				$return = (($from == 0 || $now >= $from) && ($to == 0 || $now <= $to)) ? true : false;
			endif;
		endif;
		
		return $return;
	}
	
	
	//Onlinestatus prüfen und Info im BE ausgeben bzw. FE je nach Ausgabe blockieren
	public static function checkOnlineStatus($ep)
	{	
		$op 		= $ep->getSubject();
		$sid 		= intval($ep->getParam('slice_id'));
				
		$isVisible 	= self::getOnlineStatus($sid);
		
		if (!rex::isBackend()):
			//Frontend > Ausgabe blocken/zulassen
			if (isset($_GET['rex_version']) && $_GET['rex_version'] == 1):
				//Vorschau-Version
				if (be_tools::getConfig('be_slicetimer') == 'checked' && be_tools::getConfig('be_slicetimer_workversion') != 'checked'):
					if (!$isVisible): return false; endif;
				endif;
				
			else:
				//Live-Version
				$op = '
					if (class_exists("be_tools_slicetimer")): 
						if (be_tools_slicetimer::getOnlineStatus('.$sid.')):
							'.$op.'
						endif;
					else:
						'.$op.'
					endif;';
			endif;				
		endif;
				
		return $op;
	}
	

	//Settings holen
	public static function getSettings($sid = 0, $field = '')
	{	
		$sid = intval($sid);
		$val = "";
		
		if ($sid > 0):
			$db = rex_sql::factory();
			$db->setQuery("SELECT bet_slicetimer FROM ".rex::getTable('article_slice')." WHERE id = '".$sid."' LIMIT 0,1"); 
	
			$val = ($db->getRows() > 0) ? $db->getValue('bet_slicetimer') : '';
			$val = json_decode($val, true);
			
			$val = (!empty($field)) ? @$val[$field] : $val;
		endif;
		
		return $val;
	}
	

	//Settings speichern
	public function saveSettings($aid = 0, $sid = 0)
	{	
		$sid 	= intval($sid);
		$return = false;
		
		if ($aid > 0 && $sid > 0):
			$from	= rex_request::get('bet_sform_from');
				if (is_string($from)):
					$from = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4}$/i", $from)) ? $from.' 00:00' : $from;
					$from = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $from)) ? intval(@date_format(date_create_from_format('d.m.Y H:i', $from), 'U')) : 0;
				endif;
			$to		= rex_request::get('bet_stform_to');
				if (is_string($to)):
					$to = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4}$/i", $to)) ? $to.' 00:00' : $to;
					$to = (preg_match("/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4} [0-9]{1,2}:[0-9]{1,2}$/i", $to)) ? intval(@date_format(date_create_from_format('d.m.Y H:i', $to), 'U')) : 0;
				endif;
				
			$status	= rex_request::get('bet_sform_status', 'int', null);
				$status = (empty($from) && empty($to)) ? 0 : $status;
			
			$vars 	= json_encode(array("status"=>$status, "from"=>$from, "to"=>$to));
			
			//Settings im Slice speichern
			$db = rex_sql::factory();
			$db->setTable(rex::getTable('article_slice'));
						
			$db->setValue("bet_slicetimer", $vars);

			$db->setWhere("id = '".$sid."'");
			$dbreturn = $db->update();
			
			if ($dbreturn):
				rex_article_cache::delete($aid);
				
				echo 'saved';
				$return = true;
			endif;
		endif;			
		
		return $return;
	}


	//SettingForm einfügen
	public static function appendForm($ep)
	{	
		$op = $ep->getSubject();
		$p	= $ep->getParams();
		
		//Parameter holen
		$aid = $p['article_id'];
		$sid = $p['slice_id'];
		
		/*
		echo "-----------Params $sid:";
		dump($op);
		dump($p);
		*/		
		
		$isSlice 	= ($sid > 0 && !empty($op) && !empty($p)) ? true : false;
			
		if ($isSlice):
			//$form = self::getInfoBlock($sid);				//--> geht nicht, da bei Add/Edit-Slices diese nicht sauber trennbar sind von bestehenden Slices
			$form = '';
		
			if (self::$isAdmin || rex::getUser()->hasPerm(self::$mypage.'[slicetimer]')):
				if (isset($p['module_id']) && rex::getUser()->getComplexPerm('modules')->hasPerm($p['module_id'])):
		
					//Settings holen
					$status	= intval(self::getSettings($sid, 'status'));
						$status = ($status == 1) ? 'checked' : '';
					$from 	= intval(self::getSettings($sid, 'from'));
						$from = (!empty($from)) ? date("d.m.Y H:i", $from) : '';
					$to 	= intval(self::getSettings($sid, 'to'));
						$to = (!empty($to)) ? date("d.m.Y H:i", $to) : '';
					
					//Formular vorbereiten
					$lang 	= rex_addon::get('be_tools');			

					//Formular erstellen
					$form .= '<div class="bet-slicetimer">';
						$form .= '<form method="post" class="bet-slicetimer-form">';
							$form .= '<input type="hidden" name="bet_sform_aid" value="'.$aid.'">';
							$form .= '<input type="hidden" name="bet_sform_sid" value="'.$sid.'">';
							
							$form .= '<div class="bet_sform_dates">';						
								$form .= '<span>'.$lang->i18n('a1510_bas_slicetimer_visibility').' '.$lang->i18n('a1510_bas_slicetimer_visibility_from').'</span>';							
								$form .= '<div class="input-group bet_st_datepicker-widget"><input type="text" name="bet_sform_from" id="bet_sform_from'.$sid.'" value="'.$from.'" maxlength="20" class="form-control" data-datepicker-time="true" data-datepicker-mask="true"><span class="input-group-btn"><a class="btn btn-popup" onclick="return false;" title="'.$lang->i18n('a1510_bas_slicetimer_calendar').'" data-datepicker-dst="bet_sform_from'.$sid.'"><i class="rex-icon fa-calendar"></i></a><div></div></span></div>';
								$form .= '<span>bis</span>';
								$form .= '<div class="input-group bet_st_datepicker-widget"><input type="text" name="bet_stform_to" id="bet_stform_to'.$sid.'" value="'.$to.'" maxlength="20" class="form-control" data-datepicker-time="true" data-datepicker-mask="true"><span class="input-group-btn"><a class="btn btn-popup" onclick="return false;" title="'.$lang->i18n('a1510_bas_slicetimer_calendar').'" data-datepicker-dst="bet_stform_to'.$sid.'"><i class="rex-icon fa-calendar"></i></a><div></div></span></div>';
							$form .= '</div>';
							
							$form .= '<div class="bet_sform_actions">';
								$form .= '<div class="checkbox toggle toggleSmall"><label for="bet_sform_status'.$sid.'"><input name="bet_sform_status" type="checkbox" id="bet_sform_status'.$sid.'" value="1" '.$status.'> '.$lang->i18n('a1510_bas_slicetimer_active').'</label></div>';
								$form .= '<button class="btn btn-primary" type="submit" name="bet_sform_save" title="'.$lang->i18n('a1510_bas_slicetimer_save_title').'" value="1">'.$lang->i18n('a1510_bas_slicetimer_save').'</button>';
							$form .= '</div>';
							
						$form .= '</form>';
					$form .= '</div>';
			
				endif;
			endif;
			
			//Formular einbetten
			if (preg_match('/<div class="panel-body">/i', $op)):
				$op = preg_replace('/<div class="panel-body">/i', $form.'<div class="panel-body">', $op);
			else:
				$op = preg_replace('/<\/header>/i', '</header>'.$form.'<div class="bet-slicetimerFakebody"></div>', $op);
			endif;
		endif;
			
		return $op;
	}


	//Form-Button einfügen
    public static function addButton($ep)
    {
		$op = (array) $ep->getSubject();
		$p	= $ep->getParams();
				
		$btn = ['hidden_label' => 'Zeitsteuerung',
				'url' => 'javascript:;',
				'attributes' => [
					'class' 	=> ['btn-slicetimer'],
					'title' 	=> 'Zeitsteuerung',
				],
				'icon' => ' fa-clock-o',
		];
		
		if (isset($p['module_id']) && rex::getUser()->getComplexPerm('modules')->hasPerm($p['module_id'])):
			$op[] = $btn;
		endif;
		
        $ep->setSubject($op);
    }


	//Infoblock einblenden
	public static function getInfoBlock($sid = 0, $withScript = true)
	{	
		$op	 		= '';
		$sid 		= intval($sid);
		$cfgInfo 	= be_tools::getConfig('be_slicetimer_infoblock');

		if ($cfgInfo != 'none' && $sid > 0 && rex::isBackend()):
			$isVisible 	= self::getOnlineStatus($sid);
			
			$lang 	= rex_addon::get('be_tools');
			$status	= intval(self::getSettings($sid, 'status'));
			$from 	= intval(self::getSettings($sid, 'from'));
			$to 	= intval(self::getSettings($sid, 'to'));

			//Texte vorbereiten
			$t_clock = $lang->i18n('a1510_bas_slicetimer_visibility_clock');
			
			$d_from = ($from > 0 && $to > 0) 		? $lang->i18n('a1510_bas_slicetimer_visibility_from').' <strong>'.date("d.m.Y H:i", $from).' '.$t_clock.'</strong>' : '';
			$d_from = ($from > 0 && empty($to)) 	? $lang->i18n('a1510_bas_slicetimer_visibility_asof').' <strong>'.date("d.m.Y H:i", $from).' '.$t_clock.'</strong>' : $d_from;
			$d_to 	= ($to > 0) 					? $lang->i18n('a1510_bas_slicetimer_visibility_to').' <strong>'.date("d.m.Y H:i", $to).' '.$t_clock.'</strong>' : '';
			
			//Infoblock ausgeben
			$opJS = (!empty($d_from) || !empty($d_to)) ? '<div class="bet-slicetimerInfo" id="bet-slicetimerInfo'.$sid.'"><span class="bet-slicetimerInfo-icon" title="'.$lang->i18n('a1510_bas_slicetimer_slicetimer_active').'"><i class="rex-icon fa-clock-o"></i>'.$lang->i18n('a1510_bas_slicetimer_active').'</span><span class="bet-slicetimerInfo-content">'.$lang->i18n('a1510_bas_slicetimer_visibility').' '.$d_from.' '.$d_to.'</span></div>' : '';
			
			$op .= ($status && (empty($cfgInfo) || ($cfgInfo == 'isnotvisible' && !$isVisible))) ? $opJS : '';
		endif;
		
		return $op;
	}
	
}
?>