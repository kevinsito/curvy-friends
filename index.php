<?php
    
    require 'facebook-php-sdk/src/facebook.php';
    
    // app id and app secret from here: https://developers.facebook.com/apps
    $fb = new Facebook(array(
                             'appId' => '188051731337789',
                             'secret' => '901c0ec46e1d69eb2a0b28eb2ea2704f',
                             ));

	$user = $fb->getUser();
	if (!$user) { // if user has not authenticated your app
        $params = array(
                        'scope' => 'user_relationships,friends_relationships,read_stream',
                        );
        $login_url = $fb->getLoginUrl($params);
        print '<script>top.location.href = "' . $login_url . '"</script>'; //redirect the user to the permissions dialog
        exit();
    }

	$friendToMeParam = array(
		'method' => 'fql.query',
		'query' => 'SELECT post_id, actor_id, created_time, message FROM stream WHERE message != "" AND source_id = me() AND actor_id = 1651350470 LIMIT 5000',
	);

	$friendsToMe = $fb->api($friendToMeParam);
	$friendsToMe = array_reverse($friendsToMe);

	$firstPost = reset($friendsToMe);

	$counter = 0;
	$totalRanges = 1;
	$SECONDS_IN_WEEK = 7 * 3600 * 24;
	$graphData = array();

	foreach($friendsToMe as $ToMe) {
		if (round(($ToMe['created_time'] - $firstPost['created_time'] - (($totalRanges - 1) * $SECONDS_IN_WEEK)) / 86400) <= 7) {
			$counter++;
		}
		else {
			print "Range " . ($totalRanges -1) * 7 . " - " . ($totalRanges * 7) . ": " . $counter . "<br />";
			array_push($graphData, array(($totalRanges - 1) * 7 . " - " . ($totalRanges * 7), $counter));
			$totalRanges++;
			$counter = 0;
			while(round(($ToMe['created_time'] - $firstPost['created_time'] - (($totalRanges - 1) * $SECONDS_IN_WEEK)) / 86400) >= 7) {
				print "Range " . ($totalRanges -1) * 7 . " - " . ($totalRanges * 7) . ": " . $counter . "<br />";
				array_push($graphData, array(($totalRanges - 1) * 7 . " - " . ($totalRanges * 7), $counter));
				$totalRanges++;
			}
			$counter = 1;
		}
	}
	print "Range " . ($totalRanges -1) * 7 . " - " . ($totalRanges * 7) . ": " . $counter . "<br />";
	array_push($graphData, array(($totalRanges - 1) * 7 . " - " . ($totalRanges * 7), $counter));
	print_r($graphData);

    print "<img src='https://graph.facebook.com/" . $user . "/picture?type=large'>" . '<br />' ;

    $data = $fb->api('/me?fields=friends.fields(name)');
    $friends = $data['friends']['data']; // a proper app would also check for errors, this assumes it worked
    //shuffle($friends); // randomize the array
    /*foreach ($friends as $friend) {
		print $friend['name'] . '<br />';
	}*/
    
    	print_r($fb->getAccessToken());
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Curvy Friends</title>

<script type="text/javascript" src="jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
	function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			$.post("rpc.php", {queryString: ""+inputString+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}
			});
		}
	} // lookup
	
	function fill(thisValue) {
		$('#inputString').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
</script>

<style type="text/css">
	body {
		font-family: Helvetica;
		font-size: 11px;
		color: #000;
	}
	
	h3 {
		margin: 0px;
		padding: 0px;	
	}

	.suggestionsBox {
		position: relative;
		left: 30px;
		margin: 10px 0px 0px 0px;
		width: 200px;
		background-color: #212427;
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border: 2px solid #000;	
		color: #fff;
	}
	
	.suggestionList {
		margin: 0px;
		padding: 0px;
	}
	
	.suggestionList li {
		
		margin: 0px 0px 3px 0px;
		padding: 3px;
		cursor: pointer;
	}
	
	.suggestionList li:hover {
		background-color: #659CD8;
	}
</style>

</head>

<body>


	<div>
		<form>
			<div>
				Type your county:
				<br />
				<input type="text" size="30" value="" id="inputString" onkeyup="lookup(this.value);" onblur="fill();" />
			</div>
			
			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<img src="upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />
				<div class="suggestionList" id="autoSuggestionsList">
					&nbsp;
				</div>
			</div>
		</form>
	</div>
</body>
</html>
