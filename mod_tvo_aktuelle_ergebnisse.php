<?php

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
$application = JFactory::getApplication();

/*

$scorings = new stdClass;

// Get the status of the season [true = still actual | false = it's over]
$seasonStatus = boolval($params->get('seasonStatusSelector'));


if(!$seasonStatus)
{
	?>Die Saison ist vorbei<?php
}
else
{

	$numberSelectorFromConfig = "numberOfDays"; //$params->get('numberSelector');
	$numberOfFutureDays = $params->get("numberOfFutureDays");
	$numberOfPastDays =	 $params->get("numberOfPastDays");

	if($numberOfFutureDays != 0 &&
	   $numberOfFutureDays != "" &&
	   !is_null($numberOfFutureDays))
	{

	}
	else
	{
		echo 'Fehlerhafte Konfiguration in Zeile ' . __LINE__;
	}



	$season = json_decode(file_get_contents(ModTvoAktuelleErgebnisseHelper::getPathToDataFile()));

	$alleSpieleDerSaison = array();

	// Durchlaufe alle Teams in Array $season
	foreach($season as $team)
	{
		foreach($team->dataList as $game)
		{
			$game = ModTvoAktuelleErgebnisseHelper::getTimestamp($game);
			$game->lvIDPathStr = $team->lvIDPathStr;
			$game->lvTypeLabelStr = $team->lvTypeLabelStr;

			// Speichere alle Spiele aller Mannschaften in einem gemeinsamen Array
			foreach($params->get('teams') as $teamToDisplay)
			{
				if($team->lvIDPathStr == $teamToDisplay)
				{
					$alleSpieleDerSaison[] = $game;
				}
			}

		}
	}

	usort($alleSpieleDerSaison, "ModTvoAktuelleErgebnisseHelper::cmp");

	$games = $alleSpieleDerSaison;

}
*/



// Lade alle Teaminformation aus der Datenbank
$db    = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__tvo_teams');
$query->where('published = 1');
$db->setQuery((string) $query);
$alleTeams = $db->loadObjectList();

;

// Übernimm nur die Informationen zu den gewählten Teams
foreach($params->get('teams') as $teamToShow) {
  foreach($alleTeams as $team) {
    if( $teamToShow == $team->id ) {
      $teams[] = $team;
      break;
    }
  }
}


// Lade Spieldaten von gewählten Teams zur Prüfung, ob Teams gefunden werden
$db    = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select(array('a.teamGamesId', 'b.teamGamesId', 'b.gamesData'));
$query->from($db->quoteName('#__tvo_teams', 'a'));
$query->join('RIGHT', $db->quoteName('#__tvo_games', 'b') . ' ON ' . $db->quoteName('a.teamGamesId') . ' = ' . $db->quoteName('b.teamGamesId'));
$db->setQuery((string) $query);
$db->query();

// Prüfe, ob Spiele im Gesamt-Array vorhanden sind
if( $db->getNumRows() > 0 ) {
  // Lade Spieldaten von gewählten Teams
  $db    = JFactory::getDBO();
  $query = $db->getQuery(true);
  $query->select(array('a.teamGamesId', 'b.teamGamesId', 'b.gamesData', 'b.lastUpdated'));
  $query->from($db->quoteName('#__tvo_teams', 'a'));
  $query->join('RIGHT', $db->quoteName('#__tvo_games', 'b') . ' ON ' . $db->quoteName('a.teamGamesId') . ' = ' . $db->quoteName('b.teamGamesId'));
  $db->setQuery((string) $query);
  $data = $db->loadObjectList();
}
else {
  // Es wurden keine Spiele gefunden
  $application->enqueueMessage(JText::_('MOD_TVO_AKTUELLE_ERGEBNISSE_NO_TEAMS_FOUND'), 'error');
}


// ModTvoAktuelleErgebnisseHelper::varDump($data[1]->lastUpdated);
// ModTvoAktuelleErgebnisseHelper::varDump(strtotime( $data[1]->lastUpdated ));

$lastUpdated = 0;

// Fasse alle Spieldaten aller (gewählten) Teams in einerm Array zusammen
$allGames = array();
foreach($data as $team) {
  foreach(json_decode($team->gamesData) as $gameData) {
    //ModTvoAktuelleErgebnisseHelper::varDump($gameData->dataList);
    $allGames = array_merge($allGames, $gameData->dataList);
  }

  if( strtotime($team->lastUpdated) != FALSE && strtotime($team->lastUpdated) > $lastUpdated ) {
    $lastUpdated = strtotime($team->lastUpdated);
  }
}

// Generiere UNIX-Timestamp für jedes Spiel, um eine Sortierung zu ermöglichen
foreach($allGames as $game) {
  $game = ModTvoAktuelleErgebnisseHelper::getTimestamp($game);
}

// Sortiere alle Elemente im Array in aufsteigender Reihenfolge des Austragungszeitpunktes
usort($allGames, "ModTvoAktuelleErgebnisseHelper::cmp");

// Entferne Spiele, die außerhaöb des gewählten Zeitrahmens liegen
// Sollte '0' angegebenen sein, entfällt dieser Filter
$i =0;
foreach($allGames as $game) {
  if( ($game->gDateTS > (time() + $params->get('numberOfFutureDays')*24*60*60) && $params->get('numberOfFutureDays') > 0)
  || ($game->gDateTS < (time() - $params->get('numberOfPastDays')*24*60*60)  && $params->get('numberOfPastDays') > 0) ) {
    unset($allGames[$i]);
  }
  $i++;
}
// Re-indiziere das Array
array_values($allGames);


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


  // Erstelle Array mit allen anzuzeigenden Spalten
  $contentToDisplay = $params->get('columns');

  foreach($contentToDisplay as $key => $value)
	{
		$contentToDisplay[$value] = true;
		unset($contentToDisplay[$key]);
	}

	// Render output
	require JModuleHelper::getLayoutPath('mod_tvo_aktuelle_ergebnisse', $params->get('layout'));
}
else {
	echo 'Die Saison ist vorbei';
}
