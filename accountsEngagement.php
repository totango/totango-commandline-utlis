<?php

	include "lib/helpers.php";
	include "lib/totangoAPI.php";

	/************************************************************************************************/
	/* Sample PHP code for using the Totango Data API. 												*/
	/* Please see: http://www.totango.com/developer/data-api/examples/ for more docs & examples     */
	/* 																								*/
	/* this script print outs all accounts in an activite list (by default "all accounts")          */
	/* for each account on the list, will print out their engagement KPI							*/
	/*    A. account-id,
	/*	  B. account-name,
	/*	  C. current-engagement-score, 
	/*	  D. current_health_score, 	
	/*	  W. current_visit-frequency, 
	/*    F. active-users-3d, 
	/*	  G. active-users-7d, 
	/*	  H. active-users-14d, 
	/*	  I. active-users-30d, 
	/*	  J. active-users-90d 
	/* Run this with no extra arguments for help 													*/
	/************************************************************************************************/

	$PAGE_SIZE = 100; // how many accounts to fetch per api call fetch. You can increase the number for better performance and more memory..
	$API_KEY = getMandatoryParam("-apikey", "missing param: -apikey");
	$activeListID = getOptionalParam("-activeListID", 1); 

	echoLog( "key=$API_KEY\n" );
	echoLog ("listId=$activeListID\n");

	date_default_timezone_set('UTC');

	// Print out CSV file header
	echo "account-id,account-name,current-engagement-score,current_health_score,current_visit-frequency,active-users-3d,active-users-7d,active-users-14d,active-users-30d,active-users-90d\n";
	
		// some basic code to cycle through returned accounts and print them out. interesting stuff is
	// happening in the totangoActiveListCurrent() function below 
	$max = 0xffff; $total = 0; $count=0;
	for ($page=0; $total < $max; $page++) {
		$res = totangoActiveListCurrent($API_KEY, $activeListID, $page, "all", $PAGE_SIZE);
		
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
			echo "\"{$account->account_id}\",\"{$account->name}\",{$account->engagement->score->current},{$account->engagement->health->current}, {$account->frequency->current},{$account->stats->users->past_3d->all},{$account->stats->users->past_7d->all},{$account->stats->users->past_14d->all},{$account->stats->users->past_30d->all},{$account->stats->users->past_90d->all}\n";
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
