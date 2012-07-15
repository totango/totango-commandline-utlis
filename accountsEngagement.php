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
	$activeListID = getOptionalParam("-activelistID", 1); 

	echoLog( "key=$API_KEY\n" );
	echoLog ("listId=$activeListID\n");

	date_default_timezone_set('UTC');

	// Print out CSV file header
	echo "count, account-id, account-name, engagement\n ";
	
		// some basic code to cycle through returned accounts and print them out. interesting stuff is
	// happening in the totangoActiveListCurrent() function below 
	$max = 0xffff; $total = 0; $count=0;
	for ($page=0; $total < $max; $page++) {
		$res = totangoActiveListCurrent($API_KEY, $activeListID, $page, "summary", $PAGE_SIZE);
		
		if ($res == null) {
			die("invalid API response. Please make sure you are using a valid API KEY\n" . $lastRawResult . "\n"); 
		}
		
		if ($res->_error) {
			die("error fetching list: {$res->_error}\n");
		}
		
		$max = $res->total_items;
		$total += $res->current_item_count;
		
		$accounts = $res->accounts;
		
		foreach ($accounts as $account) {
			$count++;
			echo "{$account->account_id},{$account->name},{$account->engagement->score->current} \n";
		}
		
	}
	

function printUsage() {
	global $argv;

	echo "php {$argv[0]} -apikey <totangoAPIToken> activeListID\n";
	echo "\t-apikey: API Key (see https://app.totango.com/app.html#!/updateProfile?tab=Profile)\n";
	echo "\t-activeListID: ID of the active list you want to report on. By default is '1' meaning 'All Accounts'\n";
	echo "----------------------------------------------------------------------------------------------------------------\n";
	echo "example:\n";
	echo "\tphp {$argv[0]} -apikey MYAPIKEY  -activtyListID 1004\n";
	exit;
}
