<?php
/*
	Redaxo-Addon Backend-Tools
	API-Anbindung
	v1.9.0
	by Falko Müller @ 2018-2024
*/

//Formular speichern
class rex_api_bet_slicetimer_save extends rex_api_function
{
    public function execute()
    {
		$action	= rex_request::get('bet_sform_save', 'int', null);
        $aid 	= rex_request::get('bet_sform_aid', 'int', null);
		$sid 	= rex_request::get('bet_sform_sid', 'int', null);
		
        if ($action && $aid && $sid):
            $st = new be_tools_slicetimer();
			$st->saveSettings($aid, $sid);
            exit;
        endif;
		
        throw new rex_functional_exception('Article-ID and slice-ID parameters are required!');
    }
}


//Infoblock einblenden
class rex_api_bet_slicetimer_getInfo extends rex_api_function
{
    public function execute()
    {
		$sid 	= rex_request::get('bet_sid', 'int', null);
		
        if ($sid):
            echo be_tools_slicetimer::getInfoBlock($sid, false);
            exit;
        endif;
		
        throw new rex_functional_exception('Slice-ID parameter required!');
    }
}

?>