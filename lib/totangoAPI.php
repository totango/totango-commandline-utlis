<?
/************************************************************************************************************/
/* This function fetches a page of accounts from an active-list									            */
/* - $API_KEY : Your API token. Get it from: get it from: https://app.totango.com/app.html#!/updateProfile  */
/* - $activeListID: ID of the list you are fetching. You can find it at the bottom of the 					*/
/*                  Active Lists's  page on totango 														*/
/* - $page, $pageLength: Used to page through results. 														*/
/*				See: http://www.totango.com/developer/data-api/reference/active_list-api/#paging			*/
/* - $returnType: the type of data you want back. "summary" by default										*/  
/************************************************************************************************************/
	
function totangoActiveListCurrent($API_KEY, $activeListID, $page, $returnType = "summary", $pageLength = 1000) {
	
	global $lastRawResult; 
	$ch = curl_init(); 
	curl_setopt($ch,CURLOPT_HTTPHEADER, array("Authorization: $API_KEY"));
	curl_setopt($ch, CURLOPT_URL, "https://app.totango.com/api/v1/accounts/active_list/$activeListID/current.json?length=$pageLength&offset=$page&return=$returnType");
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);


	$lastRawResult = curl_exec($ch);

	$resObj = json_decode($lastRawResult);

	return $resObj;
}
?>