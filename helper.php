<?php

class ModTvoAktuelleErgebnisseHelper
{


	public static function getPathToDataFile()
	{
		return __DIR__ . '/data.json';
	}

	public static function getSeasonDataFromFile($file)
	{
		return json_decode(file_get_contents($file));
	}


	public static function getSeasonDataForTeam($id)
	{
		$teams = self::getSeasonDataFromFile(self::getPathToDataFile());
		foreach($teams as $team)
		{
			if($team->lvIDPathStr == $id)
			{
				return $team;
			}
		}
	}




    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */
    public static function getCurrentIDs($id)
    {
		// create curl ressource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, self::getCurrentUrl()."?cmd=data&lvTypeNext=club&lvIDNext=" . $id);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
		// Return the result
		return $output;
    }

    public static function getCurrentGames($id)
    {
	    // TVO: 986
	    // HSG: 1005

		// create curl ressource
        $ch = curl_init();

        // set url
        // club (!) id = 986
        curl_setopt($ch, CURLOPT_URL, self::getCurrentUrl()."?cmd=data&lvTypeNext=club&lvIDNext=" . $id);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
		// Return the result
		return json_decode($output)[0];
    }


    public static function getLatestScorings($id)
    {
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
		return json_decode($output)[0];
    }

	public static function getCurrentUrl()
    {
		// create curl ressource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, "http://www.handball4all.de/api/url/spo_vereine-01.php");

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
		// Return the result
		return $output;
    }


	public static function cmp($a, $b)
	{
		return strcmp($a->gDateTS, $b->gDateTS);
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

    public static function varDump($var)
    {
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
		return;
    }
}
