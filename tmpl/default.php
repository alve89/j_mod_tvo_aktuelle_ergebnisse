<?php
// No direct access
defined('_JEXEC') or die;

require_once(Joomla\CMS\Uri\Uri::root() . 'modules' . DS . $module->module . DS . 'helper.php');


  ?><jdoc:include type="message" /><?php

  echo $params->get('header');

  if( $contentToDisplay['leagueAsHeader'] ) {
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
        if( $contentToDisplay['datetime'] )   { ?><th>Datum</th><?php }
        if( $contentToDisplay['league']  )    { ?><th>Klasse</th><?php }
        if( $contentToDisplay['place'] )      { ?><th>Ort</th><?php }
        if( $contentToDisplay['teamname'] )  { ?><th>Mannschaft</th><?php }
        if( $contentToDisplay['hometeam'] )   { ?><th>Heim</th><?php }
        if( $contentToDisplay['opponent'] )  { ?><th>Gegen</th><?php }
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

          if($contentToDisplay['teamname']) { ?>
            <td><?=$game->teamName;?></td>
          <?php }

          if($contentToDisplay['hometeam']) { ?>
            <td><?=$game->gHomeTeam;?></td>
          <?php }

          if($contentToDisplay['opponent']) { ?>
            <td><?=$game->opponent . ' ' . $game->opponentType;?></td>
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
    echo '<span style="font-size: 10px; line-height: 6px">'.$params->get('disclaimer').'</span>';
  }
