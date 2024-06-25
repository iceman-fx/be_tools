<?php
/*
	Redaxo-Addon Backend-Tools
	Basisklasse
	v1.9.0
	by Falko Müller @ 2018-2024
*/

class be_tools
{
    public function __construct()
    {
    }


	//Config des Addons auslesen
    public static function getConfig($sKey=null)
	{	global $a1510_mypage;
	
        $aConfig = rex_addon::get($a1510_mypage)->getConfig('config');
	        if ($sKey != ""):
				return (isset($aConfig[$sKey])) ? $aConfig[$sKey] : null;
			endif;
        return $aConfig;
    }
	
}
?>