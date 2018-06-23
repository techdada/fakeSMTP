#!/usr/bin/php
<?php

/**
 *  triggers a mqtt publishing each time an email is received
 *  Requires the phpMQTT class from https://github.com/techdada/phpMQTT
 *  or its original at https://github.com/bluerhinos/phpMQTT
 */

use techdada\fakeSMTP;
use techdada\fakeSMTPSession;

set_time_limit(0);
declare(ticks = 1);

require_once '../fakeSMTP.php';

$fsmtp = new fakeSMTP(2500);
$fsmtp->listen(function ($data,&$output,fakeSMTPSession $session) {
	// example callback.
	echo "$data \n";
	if (strtoupper($data) == 'MAIL FROM:<SUSPECT@DEVICE.COM>') {
		echo "Received mail from my device";
		$session->keepLines(true); // fetches all data for this session
		
	}
	if ($data == '.') {
		echo "End of mail \n";
		$received = $session->flushLines();
		//echo $received;
	}
});
