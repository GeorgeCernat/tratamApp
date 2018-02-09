<?php
    /*
     * Function to show an Alert type Message Box
     *
     * @param string $message	The Alert Message
     * @param string $icon		The Font Awesome Icon
     * @param string $type		The CSS style to apply
     * @return string			The Alert Box
     */
    function alertBox($message, $icon = "", $type = "") {
        return "<div class=\"alertMsg $type\"><span>$icon</span> $message <a class=\"alert-close\" href=\"#\">x</a></div>";
    }

	/*
     * Function to convert a UNIX Timestamp to a Time Ago
     *
     * @param string $datetime	The Unix Timestamp
     */
	function timeago($date) {
		if (empty($date)) {
			return "No date provided";
		}
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();
		$unix_date = strtotime($date);

		// check validity of date
		if (empty($unix_date)) {
			return "";
		}

		// is it future date or past date
		if ($now > $unix_date) {
			$difference = $now - $unix_date;
			$tense = "ago";
		} else {
			$difference = $unix_date - $now;
			$tense = "from now";
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1) {
			$periods[$j].= "s";
		}

		return "$difference $periods[$j] {$tense}";
	}

	/*
     * Function to filter profanity words
	 * Replace profanity word with FontAwesome asterisks
     *
     * @param string	$text			The text to be filtered
     * @array			$filterWords	The profanity to filter for
     * @variable		$filterCount	The character length of the profanity word
     * @return string					The filtered text
     */
	function filterwords($text) {
		$filterWords = array(
			'arsehole','asshole','blow job','blow-job','blowjob','cum','cunt','dick','fuck','fucker',
			'fuckface','fuckhead','fuckin','fucking','mother fucker','motherfucker','penis','pussy',
			'titty fuck','titty-fuck','tittyfuck','twat','motherfuckin','nigger','ass','shit','shitty'
		);
		$filterCount = sizeof($filterWords);
		for($i = 0; $i < $filterCount; $i++) {
			$text = preg_replace('/\b'.preg_quote($filterWords[$i]).'\b/ie',"str_repeat('<i class=\"fa fa-asterisk filtered\"></i>',strlen('$0'))",$text);
		}
		return $text;
	}

    /*
     * Function to ellipse-ify text to a specific length
     *
     * @param string $text		The text to be ellipsified
     * @param int    $max		The maximum number of characters (to the word) that should be allowed
     * @param string $append	The text to append to $text
     * @return string			The shortened text
     */
    function ellipsis($text, $max = '', $append = '&hellip;') {
        if (strlen($text) <= $max) return $text;

        $replacements = array(
            '|<br /><br />|' => ' ',
            '|&nbsp;|' => ' ',
            '|&rsquo;|' => '\'',
            '|&lsquo;|' => '\'',
            '|&ldquo;|' => '"',
            '|&rdquo;|' => '"',
        );

        $patterns = array_keys($replacements);
        $replacements = array_values($replacements);

        // Convert double newlines to spaces.
        $text = preg_replace($patterns, $replacements, $text);
        // Remove any HTML.  We only want text.
        $text = strip_tags($text);
        $out = substr($text, 0, $max);
        if (strpos($text, ' ') === false) return $out.$append;
        return preg_replace('/(\W)&(\W)/', '$1&amp;$2', (preg_replace('/\W+$/', ' ', preg_replace('/\w+$/', '', $out)))).$append;
    }

    /*
     * Function to Encrypt sensitive data for storing in the database
     *
     * @param string	$value		The text to be encrypted
	 * @param			$encodeKey	The Key to use in the encryption
     * @return						The encrypted text
     */
	function encryptIt($value) {
		// The encodeKey MUST match the decodeKey
		$encodeKey = '0z%E4!3I1C#5y@9&qTx@swGn@78ePqViI1C#5y@';
		$encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encodeKey), $value, MCRYPT_MODE_CBC, md5(md5($encodeKey))));
		return($encoded);
	}

    /*
     * Function to decrypt sensitive data from the database for displaying
     *
     * @param string	$value		The text to be decrypted
	 * @param			$decodeKey	The Key to use for decryption
     * @return						The decrypted text
     */
	function decryptIt($value) {
		// The decodeKey MUST match the encodeKey
		$decodeKey = '0z%E4!3I1C#5y@9&qTx@swGn@78ePqViI1C#5y@';
		$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decodeKey), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($decodeKey))), "\0");
		return($decoded);
	}

	/*
     * Function to strip slashes for displaying database content
     *
     * @param string	$value		The string to be stripped
     * @return						The stripped text
     */
	function clean($value) {
		$str = str_replace('\\', '', $value);
		return $str;
	}
?>