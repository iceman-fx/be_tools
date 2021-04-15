<?php
/*
	Redaxo-Addon Backend-Tools
	Verwaltung: Hilfe
	v1.5.4
	by Falko Müller @ 2018-2021 (based on 1.0@rex4)
	package: redaxo5
*/
?>

<section class="rex-page-section">
	<div class="panel panel-default">

		<header class="panel-heading"><div class="panel-title"><?php echo $this->i18n('a1510_head_help'); ?></div></header>
        
		<div class="panel-body">
			<div class="rex-docs">
            
           		<!--
				<div class="rex-docs-sidebar">
                	<nav class="rex-nav-toc">
                    	<ul>
                        	<li><a href="#default">Allgemein</a>
                            <li><a href="#tree">Strukturbaum</a>
                      </ul>
                    </nav>
        	    </div>
                -->
                
				<div class="rex-docs-content">
                
					<h1>Addon: <?php echo $this->i18n('a1510_title'); ?></h1>

					<p>Mit dieser Erweiterung binden Sie verschiedene Erweiterungen in das Backend von Redaxo ein.<br>
					  Die Aktivierung der einzelnen Tools nehmen Sie dabei über die entsprechenden Einstellungen vor.
					</p>
<p>&nbsp;</p>
                  <h2>Erklärung wichtiger Eigenschaften</h2>
              
                    
                    <!-- Allgemein -->
                    <a name="default"></a>
                    <h3>Bereich &quot;Allgemein&quot;:</h3>
                    <p>Die Aktivierung &quot;Homepagelink anzeigen&quot; schaltet eine Option zum direkten Aufruf der Homepage (Frontend) im Menü oben rechts frei.</p>
                    <p>Die Aktivierung &quot;Navigation minimieren&quot; verringert die Breite der Hauptnavigation, um diese beim Überfahren mit der Maus vollständig anzuzeigen.<br>
                      Über das kleine Stecknadel-Symbol in der Hauptnavigation kann diese temporär wieder fixiert werden.
                    </p>
                    <p>Die Aktivierung &quot;Sidebar minimieren&quot; verringert die Breite der Sidebar, um diese beim Überfahren mit der Maus vollständig anzuzeigen.</p>
<p>&nbsp;</p>
                    
                    
                    <!-- Strukturbaum -->
                  <h4>Konfiguration &quot;Strukturbaum&quot; (ehemals rexTree):</h4>
                    <p>In diesem Bereich finden Sie alle Optionen zur Aktivierung und Konfiguration eines Strukturbaumes mit strukturierter Ansicht der Kategorien und Artikel der Homepagestruktur.<br>
                      Die Einbindung des Strukturbaumes erfolgt entweder links oder oberhalb des Inhaltsbereiches des Backends.
                    </p>
                    <p> Über die zusätzlichen Optionen kann die Ausgabe und das Verhalten des Strukturbaumes und dessen Einträge beeinflusst werden.                    </p>
                    <p><u>Hinweis:</u><br>
                      Sofern Sie die Option
                    &quot;ActiveMode aktivieren&quot; in Verbindung mit &quot;Offene Strukturen beibehalten&quot; einsetzen, so schließen sich die Baumpfade beim Verlassen der jeweiligen Kategorie nicht mehr selbstständig.</p>
                    <p>&nbsp;</p>
                      
                      
				</div>
			</div>

	  </div>
	</div>
</section>