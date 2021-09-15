<?php
// No direct access
defined('_JEXEC') or die;

require_once(Joomla\CMS\Uri\Uri::root() . 'modules' . DS . $module->module . DS . 'helper.php');




/*



// CODE FÜR KOMPONENTE
//
// Prüfe obder Datensatz zur angegebenen ID passt
foreach($data as $record) {
  $gamesData = json_decode($record->gamesData);
  if( $record->teamGamesId != $gamesData[0]->lvIDPathStr) {
    echo 'FEHLER!';
    continue;
  }
  //ModTvoAktuelleErgebnisseHelper::varDump($gamesData[0]->dataList);
}


foreach($alleTeams as $team) {

  // Lade alle Spieldaten der Mannschaft herunter
  $team->newGamesData = ModTvoAktuelleErgebnisseHelper::getLatestScorings($team->teamGamesId);

  // Lade alle Informationen der Mannschaft aus der Datenbank
  $db    = JFactory::getDBO();
  $query = $db->getQuery(true);
  $query->select('*');
  $query->from('#__tvo_games');
  $query->where('teamGamesId' . '=' . $team->teamGamesId);
  $db->setQuery((string) $query);
  $db->query();

  if( $db->getNumRows() >= 1 ) {
    // Mindestens ein Datensatz mit der entsprechenden ID wurde gefunden => Update möglich

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    // Fields to update.
    $fields = array(
      $db->quoteName('gamesData') . ' = ' . $db->quote($team->newGamesData),
      $db->quoteName('lastUpdated') . ' = ' . $db->quote( date('Y-m-d H:i:s', time() )) UPDATE-TIME AUCH BEI NICHT DURCHGEFÜHRTEM UPDATE EINTRAGEN
    );

    // Conditions for which records should be updated.
    $conditions = array(
      $db->quoteName('teamGamesId') . ' = ' . $db->quote($team->teamGamesId)
    );

    $query->update( $db->quoteName('#__tvo_games') )->set($fields)->where($conditions);
    $db->setQuery($query);

    $db->query();
    break;
  }

  break;
}
*/
  ?><jdoc:include type="message" /><?php

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
    <span class="StandLetzteAenderung">Letzte Aktualisierung: <?=date("d.m.Y, H:i", $lastUpdated) . " Uhr";?></span>
    <?php
    }
    ?>


    <table class="table_ergebnisse">
      <tr>
        <?php
        if( $contentToDisplay['datetime'] )   { ?><th>Datum</th><?php }
        if( $contentToDisplay['league']  )    { ?><th>Klasse</th><?php }
        if( $contentToDisplay['place'] )      { ?><th>Ort</th><?php }
        if( $contentToDisplay['hometeam'] )   { ?><th>Heim</th><?php }
        if( $contentToDisplay['scores'] )     { ?><th>Spielstand</th><?php }
        if( $contentToDisplay['guestteam'] )  { ?><th>Gast</th><?php }
        ?>
      </tr>

    <?php

    foreach($allGames as $game) {
      ?><tr><?php
          if($contentToDisplay['datetime']) { ?>
            <td><?=date("d.m.Y, H:i", $game->gDateTS);?></td>
          <?php }

          if($contentToDisplay['league']) { ?>
            <td><?=$game->gClassSname;?></td>
          <?php }

          if($contentToDisplay['place']) { ?>
            <td><?=$game->gGymnasiumName;?><br />
            <span style="font-size: 12px; text-align: center; align-content: center;"><?=$game->gGymnasiumStreet;?>, <?=$game->gGymnasiumPostal;?> <?=$game->gGymnasiumTown;?></span></td>
          <?php }

          if($contentToDisplay['hometeam']) { ?>
            <td><?=$game->gHomeTeam;?></td>
          <?php }

          if($contentToDisplay['scores']) { ?>
            <td><?=modTvoAktuelleErgebnisseHelper::score($game->gHomeGoals, $game->gGuestGoals, $game->gHomeGoals_1, $game->gGuestGoals_1);?></td>
          <?php }

          if($contentToDisplay['guestteam']) { ?>
            <td><?=$game->gGuestTeam;?></td>
          <?php }

      ?></tr><?php
    }
    ?>
    </table>
    <?php
  }
