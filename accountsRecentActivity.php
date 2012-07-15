<?php

	include "lib/helpers.php";
	include "lib/totangoAPI.php";

	/************************************************************************************************/
	/* Sample PHP code for using the Totango Data API. 												*/
	/* Please see: http://www.totango.com/developer/data-api/examples/ for more docs & examples     */
	/* 																								*/
	/* this script print outs all accounts in an activite list (by default "all accounts")          */
	/* for each account on the list, will print out the number of time it has done the supplied list*/
	/* of activities in the last 90, 30 and 7 days.													*/
	/* If no activities are provided, will print the total number of all activities done			*/
	/* 																								*/
	/* Run this with no extra arguments for help 													*/
	/************************************************************************************************/
	
	$PAGE_SIZE = 100; // how many accounts to fetch per api call fetch. You can increase the number for better performance and more memory..
	$API_KEY = getMandatoryParam("-apikey", "missing param: -apikey");
	$activityNames = getArrayParams("-activity");
	$activeListID = getOptionalParam("-activelistID", 1); 

	echoLog( "key=$API_KEY\n" );
	echoLog ("listId=$activeListID\n");
	
	date_default_timezone_set('UTC');

	// Print out CSV file header
	echo "count, account-id, account-name, first-activity, last-activity, ";
	foreach ($activityNames as $printActivity) 
		echo "{$printActivity}_7d,{$printActivity}_30d,{$printActivity}_90d,"; 
	echo "\n";
	// ......
		
	$max = 0xffff; 
	$total = 0; $count=0;
	
	// run through account list, print line for each account
	for ($page=0; $total < $max; $page++) {
		
		// API call to fetch next page of accounts in list
		echoLog ("...reading accounts (" . ($page * $PAGE_SIZE + 1)  . " .. " . (($page+1) * $PAGE_SIZE) . ")...\n");
		$res = totangoActiveListCurrent($API_KEY, $activeListID, $page, "stats",$PAGE_SIZE); 
		
		if ($res == null) {
			die("invalid API response. Please make sure you are using a valid API KEY\n\tres= " . $lastRawResult . "\n");
		}
		
		if ($res->_error) {
			die("error fetching list: {$res->_error}\n");
		}
		
		if ($max == 0xffff) 
			$max = $res->total_items;
		
		$total += $res->current_item_count;
		
		$accounts = $res->accounts;
		
		// go through each account in the page
		foreach ($accounts as $account) {
			$count++;
			
			echo "{$account->account_id},{$account->name}, ";
			echo date($DATE_OUT,$account->stats->first_activity/1000) . "," . date($DATE_OUT,$account->stats->last_activity/1000) . ",";
			$stats = $account->stats->activities;
			
			// for each activity in the activity list, print out 7d, 30d and 90d counts
			foreach ($activityNames as $printActivity) {
				echo echoActivity($printActivity, $stats, "past_7d") . ",";
				echo echoActivity($printActivity, $stats, "past_30d") . ",";
				echo echoActivity($printActivity, $stats, "past_90d") . ",";
			}
			echo "\n";
			
			
		}
		
	}
	
	
function echoActivity($activityName, $stats,$scope) {
	
	$activities = $stats->{$scope}->activities;
	
	if ($activities)
		foreach ($activities as $activity) {
			if (strcasecmp($activityName,$activity->name) == 0)
				return $activity->current;
		}
	return "0";
}

function printUsage() {
	global $argv;
	
	echo "php {$argv[0]} -apikey <totangoAPIToken> [-activity <activityName>]* -activeListID\n";
	echo "\t-apikey: API Key (see https://app.totango.com/app.html#!/updateProfile?tab=Profile)\n";
	echo "\t-activity: name of the activity on Totango to report on. You can add several -activity params to print out multiple values\n\tIf none are provided, will print out total number of activities\n";
	echo "\t-activeListID: ID of the active list you want to report on. By default is '1' meaning 'All Accounts'\n";
	echo "----------------------------------------------------------------------------------------------------------------\n";
	echo "example:\n";
	echo "\tphp {$argv[0]} -apikey MYAPIKEY -activity Login -activity \"Add Task\" -activity Clear -activtyListID 1004\n";
	exit;
}
	

?>