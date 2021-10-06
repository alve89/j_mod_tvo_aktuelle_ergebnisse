<?php

class ModTvoAktuelleErgebnisseHelper {

	// Retrieve the current API URL
	public static function getCurrentUrl()
  {
		return "https://api.h4a.mobi/spo/spo-proxy_public.php";
  }

	// Retrieve all team data for the given ID
	public static function getCurrentGamesData($id) {
		// create curl ressource
		$ch = curl_init();

		// set url
		// club (!) id = 986
		curl_setopt($ch, CURLOPT_URL, self::getCurrentUrl()."?cmd=data&lvTypeNext=team&lvIDNext=" . $id);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,  CURLOPT_ENCODING, 'gzip');

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);
		// Return the result
		return $output;
	}

	// Retrieve all table data for the given ID
	public static function getCurrentTableData($id) {
		// create curl ressource
		$ch = curl_init();

		// set url
		curl_setopt($ch, CURLOPT_URL, self::getCurrentUrl()."?cmd=data&lvTypeNext=class&subType=table&lvIDNext=" . $id);

		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,  CURLOPT_ENCODING, 'gzip');

		// $output contains the output string
		$output = curl_exec($ch);

		// close curl resource to free up system resources
		curl_close($ch);
		// Return the result
		return $output;
	}


	// User defined comparison and sort function
	public static function cmp($a, $b)
	{
		return strcmp($a->gDateTS, $b->gDateTS);
	}


	public static function varDump($var)
  {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
	return;
  }


	public static function getTimestamp($game)
	{

		$dateAsArr = explode(".", $game->gDate);
		$dateAsArr[2] = 2000 + $dateAsArr[2];
		$timeAsArr = explode(":", $game->gTime);

		$game->gDateTS = mktime($timeAsArr[0], $timeAsArr[1], 0, $dateAsArr[1], $dateAsArr[0], $dateAsArr[2]);

		return $game;
	}


	/*
	 *
	 * Create current score
	 *
	 */
	public static function score($homegoals, $guestgoals, $homegoals1, $guestgoals1)
	{
		if((empty($homegoals) || empty($guestgoals)) || $homegoals == " " || $guestgoals == " ") // || (empty($homegoals) && empty($guestgoals))
		{
			$return = "n. v.";
		}
		else
		{
			$return = $homegoals . ' : ' . $guestgoals;
			if((empty($homegoals1) || empty($guestgoals1)) || $homegoals1 == " " || $guestgoals1 == " ")
			{
				$return .= " (n. v.)";
			}
			else
			{
				$return .= ' (' . $homegoals1 . ' : ' . $guestgoals1 . ')';
			}
		}
		return $return;
	}


}
