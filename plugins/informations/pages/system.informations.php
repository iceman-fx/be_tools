<div class="row">
	<div class="col-sm-12">

	<?php
	$group_str = array(
		'article' 		=> $this->i18n('a1510_plinfo_title_article'),
		'cat' 			=> $this->i18n('a1510_plinfo_title_cat'),
		'media' 		=> $this->i18n('a1510_plinfo_title_media'),
		'forcal' 		=> $this->i18n('a1510_plinfo_title_forcal'),
	);
	$group_sys = array(
		'template'		=> $this->i18n('a1510_plinfo_title_template'),
		'module'		=> $this->i18n('a1510_plinfo_title_module'),
		'user'			=> $this->i18n('a1510_plinfo_title_user'),
		'addon'			=> $this->i18n('a1510_plinfo_title_addon'),
		'cronjob'		=> $this->i18n('a1510_plinfo_title_cronjob'),
		'yr_domain'		=> $this->i18n('a1510_plinfo_title_yrewrite_domain'),
		'yr_forward'	=> $this->i18n('a1510_plinfo_title_yrewrite_forward'),
	);
	
	
	//Strukturinfos
	$content = "";
	foreach($group_str as $type => $label):
	
		if ($type == 'article'):		
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('article')." WHERE status = '1'");
			$arts_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('article')." WHERE status = '0'");
			$arts_off = $db->getRows();
			$arts_off_info = ($arts_off > 0) ? $this->i18n('a1510_plinfo_offline').' '.$arts_off : '';

			$arts_redir = '';
			if (rex_addon::get('yrewrite')->isAvailable()):
				$db = rex_sql::factory();
					$db->setQuery("SELECT id FROM ".rex::getTable('article')." WHERE yrewrite_url_type LIKE 'REDIRECTION%' AND yrewrite_redirection <> ''");
				$arts_redir = $db->getRows();
				
				$arts_redir_info = ($arts_redir > 0) ? $this->i18n('a1510_plinfo_forward').' '.$arts_redir : '';
				$arts_redir_info = (!empty($arts_redir_info) && $arts_off > 0) ? '<br>'.$arts_redir_info : $arts_redir_info;
			endif;
			
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('clang'));		// WHERE status = '1'
			$clangs_on = $db->getRows();
			$clangs_on_info = ($clangs_on > 1) ? ' ('.str_replace("###clangcount###", $clangs_on, $this->i18n('a1510_plinfo_clangs')).')' : '';
			
			
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($arts_on + $arts_off).$clangs_on_info.'</td>
				</tr>
			';

			if (!empty($arts_off) || !empty($arts_redir)):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$arts_off_info.$arts_redir_info.'</td>
					</tr>
				';
			endif;
		endif;
		
		
		if ($type == 'cat'):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('article')." WHERE catpriority <> '0' AND status = '1'");
			$cats_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('article')." WHERE catpriority <> '0' AND status = '0'");
			$cats_off = $db->getRows();
			
	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($cats_on + $cats_off).'</td>
				</tr>
			';

			if ($cats_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_offline').' '.$cats_off.'</td>
					</tr>
				';
			endif;
		endif;
		

		if ($type == 'media'):		
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('media'));
			$media_on = $db->getRows();
			
			
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($media_on).'</td>
				</tr>
			';
		endif;
		
		
		if ($type == 'forcal' && rex_addon::get('forcal')->isAvailable()):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('forcal_entries')." WHERE status = '1'");
			$forcal_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('forcal_entries')." WHERE status = '0'");
			$forcal_off = $db->getRows();

	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($forcal_on + $forcal_off).'</td>
				</tr>
			';

			if ($forcal_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_offline').' '.$forcal_off.'</td>
					</tr>
				';
			endif;
		endif;

	endforeach;
	
    $content = (!empty($content)) ? '<table class="table table-hover table-bordered"><tbody>'.$content.'</tbody></table>' : '';
	
	$fragment = new rex_fragment();
		$fragment->setVar('title', rex_i18n::rawmsg('a1510_plinfo_block1'));
		$fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');



	//Systeminfos
	$content = "";
	foreach($group_sys as $type => $label):

		if ($type == 'template'):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('template')." WHERE active = '1'");
			$temp_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('template')." WHERE active = '0'");
			$temp_off = $db->getRows();
			
	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($temp_on + $temp_off).'</td>
				</tr>
			';

			if ($temp_on > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_active').' '.$temp_on.'</td>
					</tr>
				';
			endif;
		endif;


		if ($type == 'module'):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('module'));
			$mod_on = $db->getRows();
			
	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($mod_on).'</td>
				</tr>
			';
		endif;

		
		if ($type == 'user'):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('user')." WHERE status = '1'");
			$user_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('user')." WHERE status = '0'");
			$user_off = $db->getRows();
			
	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($user_on + $user_off).'</td>
				</tr>
			';

			if ($user_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_offline').' '.$user_off.'</td>
					</tr>
				';
			endif;
		endif;

		
		if ($type == 'addon'):
			$addon_on = count(rex_addon::getRegisteredAddons());
			$addon_off = 0;
				foreach (rex_addon::getRegisteredAddons() as $addon):
					if (!$addon->isAvailable()) { $addon_off++; }				//$package->isInstalled()
				endforeach;
			
			
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($addon_on).'</td>
				</tr>
			';

			if ($addon_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_notactive').' '.$addon_off.'</td>
					</tr>
				';
			endif;
		endif;


		if ($type == 'cronjob' && rex_addon::get('cronjob')->isAvailable()):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('cronjob')." WHERE status = '1'");
			$cron_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('cronjob')." WHERE status = '0'");
			$cron_off = $db->getRows();
			
	
			if ($cron_on > 0 || $cron_off > 0):
				$content .= '
					<tr>
						<th width="200">'.rex_escape($label).'</th>
						<td data-title="'.rex_escape($label).'">'.($cron_on + $cron_off).'</td>
					</tr>
				';
			endif;
			
			if ($cron_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>davon derzeit nicht aktiv: '.$cron_off.'</td>
					</tr>
				';
			endif;
		endif;

		
		if ($type == 'yr_domain' && rex_addon::get('yrewrite')->isAvailable()):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('yrewrite_domain'));
			$yrd_on = $db->getRows();
			
	
			$content .= '
				<tr>
					<th width="200">'.rex_escape($label).'</th>
					<td data-title="'.rex_escape($label).'">'.($yrd_on).'</td>
				</tr>
			';
		endif;

		
		if ($type == 'yr_forward' && rex_addon::get('yrewrite')->isAvailable()):
			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('yrewrite_forward')." WHERE status = '1'");
			$yrf_on = $db->getRows();

			$db = rex_sql::factory();
				$db->setQuery("SELECT id FROM ".rex::getTable('yrewrite_forward')." WHERE status = '0'");
			$yrf_off = $db->getRows();
			
	
			if ($yrf_on > 0 || $yrf_off > 0):
				$content .= '
					<tr>
						<th width="200">'.rex_escape($label).'</th>
						<td data-title="'.rex_escape($label).'">'.($yrf_on + $yrf_off).'</td>
					</tr>
				';
			endif;
			
			if ($yrf_off > 0):
				$content .= '
					<tr>
						<th width="200">&nbsp;</th>
						<td>'.$this->i18n('a1510_plinfo_notactive').' '.$yrf_off.'</td>
					</tr>
				';
			endif;
		endif;

	endforeach;
	
    $content = (!empty($content)) ? '<table class="table table-hover table-bordered"><tbody>'.$content.'</tbody></table>' : '';
	
	$fragment = new rex_fragment();
		$fragment->setVar('title', rex_i18n::rawmsg('a1510_plinfo_block2'));
		$fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');
	?>

	</div>
</div>