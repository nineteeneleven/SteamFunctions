<?php
/*
*Written By NineteenEleven, http://nineteeneleven.info
*
*        Resources
*https://developer.valvesoftware.com/wiki/Steam_Web_API
*http://wiki.teamfortress.com/wiki/WebAPI
*
*/

define("API_KEY" , "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
define("cache_time", "15"); //Time in minutes to resolve cache


function convertToHoursMins($time, $format = '%d:%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time/60);
    $minutes = $time%60;
    return sprintf($format, $hours, $minutes);
}

 

class SteamQuery
{
    public function getJson($url) {
        // make cache directory if it doesnt exist
        if (!file_exists('cache')) {
            mkdir('cache', 0755, true);
        }
         // cache files are created like cache/abcdef123456...
        $cacheFile = 'cache' . DIRECTORY_SEPARATOR . md5($url);

        if (file_exists($cacheFile)) {
            $fh = fopen($cacheFile, 'r');
            $cacheTime = trim(fgets($fh));

            // if data was cached recently, return cached data
            if ($cacheTime > strtotime('-' . cache_time .' minutes')) {
                return fread($fh, filesize($cacheFile));
            }

            // else delete cache file
            fclose($fh);
            unlink($cacheFile);
        }

        $json = file_get_contents($url);

        $fh = fopen($cacheFile, 'w');
        fwrite($fh, time() . "\n");
        fwrite($fh, $json);
        fclose($fh);

        return $json;
    }

    public function GetPlayerSummaries($steamID64){
        $API_link = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . API_KEY . "&format=json&steamids=" . $steamID64;
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }

    public function GetFriendsList($steamID64){
        $API_link = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=". API_KEY ."&steamid=". $steamID64 . "&relationship=friend&format=json";
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }
    public function GetPlayerAchievements($steamID64,$appid){
        $API_link = "http://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v0001/?appid=". $appid ."&key=" . API_KEY . "&steamid=" . $steamID64 ."&format=json";
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }
    public function GetUserStatsForGame($steamID64,$appid){
        $API_link = "http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=". $appid ."&key=" . API_KEY . "&steamid=" . $steamID64 ."&format=json";
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }
    public function GetOwnedGames($steamID64){
        $API_link = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=" . API_KEY . "&format=json&steamid=" . $steamID64;
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }
    public function GetRecentlyPlayedGames($steamID64){
        $API_link = "http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=" . API_KEY . "&format=json&steamid=" . $steamID64;
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }
    public function GetInv($appid,$steamID64){
        $API_link = "http://api.steampowered.com/IEconItems_".$appid."/GetPlayerItems/v0001/?key=" . API_KEY . "&format=json&steamid=" . $steamID64;
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }   
    public function ConvertVanityURL($playerName){
        $API_link = "http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=" . API_KEY . "&format=json&vanityurl=" . $playerName;
        $json = $this->getJson($API_link);
        $query=json_decode($json);
        if ($query->response->success === 1) {
            $ID64=$query->response->steamid;
            return $ID64;
        }else{
            return false;
        }
    }
//Returns the item schema for an app id
    public function GetSchema($appid){
        $API_link = "http://api.steampowered.com/IEconItems_".$appid."/GetSchema/v0001/?key=" . API_KEY . "&format=json";
        $json = $this->getJson($API_link);
        $json_output=json_decode($json);
        return $json_output;
    }

//returns arrway with list of items in a players backpack.
    public function FindItem($appid,$defindex){
        $Schema = $this->GetSchema($appid);
        foreach ($Schema->result->items as $item) {
            if ($item->defindex == $defindex) {

                return array('name' => $item->name,
                                    'defindex' => $item->defindex,
                                    'item_class' => $item->item_class,
                                    'item_type_name' => $item->item_type_name,
                                    'item_name' => $item->item_name,
                                    'proper_name' => $item->proper_name,
                                    'item_slot' => $item->item_slot,
                                    'model_player' => stripslashes($item->model_player),
                                    'item_quality' => $item->item_quality,
                                    'image_inventory' => stripslashes($item->image_inventory),
                                    'min_ilevel' => $item->min_ilevel,
                                    'max_ilevel' => $item->max_ilevel,
                                    'image_url' => stripslashes($item->image_url),
                                    'image_url_large' => stripslashes($item->image_url_large),
                                    'craft_class' => $item->craft_class,
                                    'craft_material_type' => $item->craft_material_type,
                                    'capabilities' => $item->capabilities,
                                    'used_by_classes' => $item->used_by_classes,
                                    );
            }
        }
    }
}





class SteamIDConvert
{
    //Get 76561197973578969 from STEAM_0:1:6656620
    function IDto64($steamId) {
        $iServer = "0";
        $iAuthID = "0";
         
        $szTmp = strtok($steamId, ":");
         
        while(($szTmp = strtok(":")) !== false)
        {
            $szTmp2 = strtok(":");
            if($szTmp2 !== false)
            {
                $iServer = $szTmp;
                $iAuthID = $szTmp2;
            }
        }
        if($iAuthID == "0")
            return "0";
     
        $steamId64 = bcmul($iAuthID, "2");
        $steamId64 = bcadd($steamId64, bcadd("76561197960265728", $iServer));
            if (strpos($steamId64, ".")) {
                $steamId64=strstr($steamId64,'.', true);
            }     
        return $steamId64;
    }
    
    ////Get STEAM_0:1:6656620 from 76561197973578969
    function IDfrom64($steamId64) {
        $iServer = "1";
        if(bcmod($steamId64, "2") == "0") {
            $iServer = "0";
        }
        $steamId64 = bcsub($steamId64,$iServer);
        if(bccomp("76561197960265728",$steamId64) == -1) {
            $steamId64 = bcsub($steamId64,"76561197960265728");
        }
        $steamId64 = bcdiv($steamId64, "2");
        if (strpos($steamId64, ".")) {
                $steamId64=strstr($steamId64,'.', true);
            }     
        return ("STEAM_0:" . $iServer . ":" . $steamId64);
    }

    function getSteamLink($steamId64){
        return "http://steamcommunity.com/profiles/".$steamId64;
    }


    // this function is not used, old code.
    function getSteam64Xml($steam_link_xml){
        $xml = @simplexml_load_file($steam_link_xml);
        if(!empty($xml)) {
            $steamID64 = $xml->steamID64;
        }
        return $steamID64;
    }

    function SteamIDCheck($steamiduser){
        $steamiduser = rtrim($steamiduser , "/" ); // remove trailing backslash

        //Look for STEAM_0:1:6656620 variation
        if(preg_match("/^STEAM_/i", $steamiduser)){
            $steamId64= $this->IDto64($steamiduser);
            $steam_link = $this->getSteamLink($steamId64);
            $steam_id = strtoupper($steamiduser);
            $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamId64, 'steam_link'=>$steam_link);
            return $steamArray;


         //look for just steam id 64, 76561197973578969
        }elseif (preg_match("/^[0-9]/i", $steamiduser)) {
            $steamID64 = $steamiduser;
            $steam_link = $this->getSteamLink($steamID64);
            $steamid = $this->IDfrom64($steamID64);
            $SteamQuery = new SteamQuery;
            $Query = $SteamQuery->GetPlayerSummaries($steamID64);
            $test = $Query->response->players;
                if (empty($test)){
                    return false;
                }else{
                    $steamArray = array('steamid'=>$steamid, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                    return $steamArray;
                }
        }else{


            //Look for characters
            if (preg_match("/^[a-z]/i", $steamiduser)) {

                //Find steamcommunity link
                if (preg_match("/(steamcommunity.com)+/i",$steamiduser)) {

                    //look for 64 url http://steamcommunity.com/profiles/76561197973578969
                    if (preg_match("/(\/profiles\/)+/i", $steamiduser)) {

                        $steamiduser = rtrim($steamiduser , "/" );
                        $i = preg_split("/\//i", $steamiduser);
                        $size = count($i) - 1;
                        $steamID64 = $i[$size];
                        $steam_link = $this->getSteamLink($steamID64);
                        $steam_id=$this->IDfrom64($steamID64);
                        $steamArray = array('steamid'=>$steam_id, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                        return $steamArray;

                    } elseif (preg_match("/(\/id\/)+/i",$steamiduser)) {

                        //look for vanity url http://steamcommunity.com/id/nineteeneleven
                        $i = preg_split("/\//i", $steamiduser);
                        $size = count($i) - 1;
                        $SteamQuery = new SteamQuery;
                        $steamID64 = $SteamQuery->ConvertVanityURL($i[$size]);
                        $steamid = $this->IDfrom64($steamID64);
                        $steam_link = $this->getSteamLink($steamID64);
                        $steamArray = array('steamid'=>$steamid, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);

                        
                        
                        return $steamArray;

                    } else {
                        return false;
                    }
                }else{
                    //check if its just vanity url, nineteeneleven
                    $SteamQuery = new SteamQuery;
                    $steamID64 = $SteamQuery->ConvertVanityURL($steamiduser);
                    $steamid = $this->IDfrom64($steamID64);
                    $steam_link = $this->getSteamLink($steamID64);
                        if ($steamid=="STEAM_0:0:0") {
                            return false;
                        }else{
                        $steamArray = array('steamid'=>$steamid, 'steamID64' =>$steamID64, 'steam_link'=>$steam_link);
                        return $steamArray;
                        }

                }
            }else{
                //found nothing
                return false;
            }
        }
    }
}

?>