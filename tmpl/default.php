<?php
// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE . '/modules' . DS . $module->module . DS . 'helper.php');

$renderTable = false;

?><jdoc:include type="message" /><?php

echo $params->get('header');


// Prüfe, ob die Saison noch läuft oder bereits vorüber ist
if( $params->get('seasonStatusSelector') == 1 ) {
  // Prüfe ob in der Modulkonfiguration Spalten zum Anzeigen ausgewählt wurden
  if( $params->get('columns') == NULL ) {
    // In der Modulkonfiguration wurde nichts angehakt
    $application->enqueueMessage(JText::_('MOD_TVO_AKTUELLE_ERGEBNISSE_NO_COLUMNS_CHOSEN'), 'error');
  }

  // // Prüfe, ob Spiele im Gesamt-Array vorhanden sind
  // if( empty($allGames) ) {
  //   // Es wurden keine Spiele gefunden
  //   $application->enqueueMessage(JText::_('MOD_TVO_AKTUELLE_ERGEBNISSE_NO_GAMES_FOUND'), 'error');
  // }

  if( $tablesNotFound ) {
    $application->enqueueMessage(JText::_('MOD_TVO_AKTUELLE_ERGEBNISSE_TABLES_NOT_FOUND'), 'error');
  }

  // Erstelle Array mit allen anzuzeigenden Spalten
  $contentToDisplay = $params->get('columns');

  foreach($contentToDisplay as $key => $value)
	{
		$contentToDisplay[$value] = true;
		unset($contentToDisplay[$key]);
	}

	// Render output
	$renderTable = true;
}
else {
	echo 'Die Saison ist vorbei';
}




if($renderTable) {

  if( isset($contentToDisplay['leagueAsHeader']) && $contentToDisplay['leagueAsHeader'] ) {
  ?>
  <span class="league">
  	<h4><?=$data[0]->teamLeague;?></h4>
  </span>
  <?php
  }

  if(empty($allGames)) {
    if( !$tablesNotFound ) {
		    echo 'In den nächsten ' . $params->get('numberOfFutureDays') . ' Tagen stehen keine Spiele an.';
    }
    else {
      echo 'Die Daten konnten nicht geladen werden.';
    }
  }
  else {
    if( $contentToDisplay['lastUpdated'] ) {
    ?>
    <span class="StandLetzteAenderung" style="font-size: 10px">Letzte Aktualisierung: <?=date("d.m.Y, H:i", $lastUpdated) . " Uhr";?></span>
    <?php
    }
    ?>


    <table class="table_ergebnisse" style="font-size: 12px">
      <tr>
        <?php
        if( isset($contentToDisplay['datetime']) && $contentToDisplay['datetime'] )   { ?><th>Datum</th><?php }
        if( isset($contentToDisplay['league']) && $contentToDisplay['league']  )    { ?><th>Klasse</th><?php }
        if( isset($contentToDisplay['place']) && $contentToDisplay['place'] )      { ?><th>Ort</th><?php }
        if( isset($contentToDisplay['teamname']) && $contentToDisplay['teamname'] )  { ?><th>Mannschaft</th><?php }
        if( isset($contentToDisplay['hometeam']) && $contentToDisplay['hometeam'] )   { ?><th>Heim</th><?php }
        if( isset($contentToDisplay['opponent']) && $contentToDisplay['opponent'] )  { ?><th>Gegen</th><?php }
        if( isset($contentToDisplay['scores']) && $contentToDisplay['scores'] )     { ?><th>Spielstand</th><?php }
        if( isset($contentToDisplay['guestteam']) && $contentToDisplay['guestteam'] )  { ?><th>Gast</th><?php }
        ?>
      </tr>

    <?php

    foreach($allGames as $game) {
      ?><tr><?php
          if(isset($contentToDisplay['datetime']) && $contentToDisplay['datetime']) {
            if($game->gTime == "00:00") { ?>
              <td><?=date("d.m.Y, ", $game->gDateTS);?>Zeit n. v.</td>
            <?php }
            else { ?>
            <td><?=date("d.m.Y, H:i", $game->gDateTS);?></td>
          <?php }}

          if(isset($contentToDisplay['league']) && $contentToDisplay['league']) { ?>
            <td><?=$game->gClassSname;?></td>
          <?php }

          if(isset($contentToDisplay['place']) && $contentToDisplay['place']) { ?>
            <td><?=$game->gGymnasiumName;?><br />
            <span style="font-size: 12px; text-align: center; align-content: center;"><?=$game->gGymnasiumStreet;?>, <?=$game->gGymnasiumPostal;?> <?=$game->gGymnasiumTown;?></span></td>
          <?php }

          if(isset($contentToDisplay['teamname']) && $contentToDisplay['teamname']) { ?>
            <td><?=$game->teamName;?></td>
          <?php }

          if(isset($contentToDisplay['hometeam']) && $contentToDisplay['hometeam']) { ?>
            <td><?=$game->gHomeTeam;?></td>
          <?php }

          if(isset($contentToDisplay['opponent']) && $contentToDisplay['opponent']) { ?>
            <td><?=$game->opponent . ' ' . $game->opponentType;?></td>
          <?php }

          if(isset($contentToDisplay['scores']) && $contentToDisplay['scores']) { ?>
            <td><?=modTvoAktuelleErgebnisseHelper::score($game->gHomeGoals, $game->gGuestGoals, $game->gHomeGoals_1, $game->gGuestGoals_1);?></td>
          <?php }

          if(isset($contentToDisplay['guestteam']) && $contentToDisplay['guestteam']) { ?>
            <td><?=$game->gGuestTeam;?></td>
          <?php }


      ?></tr><?php
    }
    ?>
    </table>
    <?php
    echo '<span style="font-size: 10px; line-height: 6px">'.$params->get('disclaimer').'</span>';
  }
} // if $renderTable is true
