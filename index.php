<html>
<header>
	<title>Steam ID Finder by NineteenEleven</title>
	<style type="text/css">
*{
	list-style: none;
}

.PlayerHeader{
margin-top: 40px;
}

.PlayerName{
	text-decoration: none;
	font-size: 30px;
	color: #333;
	margin-left: 10px;
	margin-right: 40px;
	vertical-align: top;

}
.PlayerAvatar{
	border: 1px solid #333;
	border-radius: 10px;
	vertical-align: top;
	display: inline-block;
	position: relative;
	margin-top: -20px;
}
.GamesPlayed{
	margin-top: 30px;
	padding: 20px;
}
.GamesPlayed li{
	display: inline-block;
	margin-right: 15px;
	position: relative;
	font-size: 14px;
}
</style>
</header>
<form id="SteamIDFinder" method="POST" action="index.php">
<input name="SteamID" type="text" id="SteamID" style="width:400;"></td>
<input type="submit" name="SteamIDFinder" value="FIND EM!" form='SteamIDFinder' />
<br />

<?php

include_once 'includes/class_lib.php';

if (isset($_POST['SteamID'])) {
	$SteamID = $_POST['SteamID'];

	$SteamQuery = new SteamQuery;

	$SteamIDConvert = new SteamIDConvert;

	$SteamArray = $SteamIDConvert->SteamIDCheck($SteamID);

	if (empty($SteamArray)) {
			exit("<h1>No Such User Found</h1>");
	}
	$Query = $SteamQuery->GetPlayerSummaries($SteamArray['steamID64']);

	echo "Steam ID: " . $SteamArray['steamid'] . "<br />";
	echo "Steam ID 64: " . $SteamArray['steamID64'] . "<br />";
	echo "Steam Link: " . $SteamArray['steam_link'] . "<br />";
	

foreach ($Query->response->players as $player) {
	echo "<li>";
	echo "<div class='PlayerHeader'>";
	if(!empty($player->gameid)){
		echo "<a href='". $player->profileurl . "' target='_blank'><img src='" . $player->avatarmedium . "' class=PlayerAvatar style='border: 5px solid #8bc53f;'/></a>";
	}else{
		echo "<a href='". $player->profileurl . "' target='_blank'><img src='" . $player->avatarmedium . "' class=PlayerAvatar style='border: 5px solid #62a7e3;'/></a>";

	}
	echo "<a href='". $player->profileurl . "' target='_blank' class='PlayerName'>". $player->personaname . "</a>";
	
	echo "<a href='steam://friends/add/". $player->steamid . "' style='font-size:8px;'> Add to Friends</a>";



	echo "</div>";
echo "<br />";
	$recentGames = @$SteamQuery->GetRecentlyPlayedGames($player->steamid);
	if ($recentGames->response->total_count > 0) {	
		echo "<ul class='GamesPlayed'>";

		foreach ($recentGames->response->games as $games) {
			echo "<li>";

			echo "<img src='http://media.steampowered.com/steamcommunity/public/images/apps/". $games->appid . "/" . $games->img_icon_url .".jpg' />";
			echo "  ";
			echo $games->name;
	   		echo "<br />";
			echo convertToHoursMins($games->playtime_2weeks,'%d hours %d minutes') . " played in the last 2 weeks.";
			echo "<br />";
			echo convertToHoursMins($games->playtime_forever,'%d hours %d minutes'). " played total.";

			echo "</li>";
		}
		echo "</ul>";
}else{
	echo "<br />No Recently played games";
}
	echo "<hr />";
	echo "</li>";
}

}
?>	