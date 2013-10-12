Steam Functions for the VALVe Web API.

This is an assortment of classes and methods written in php. 

SteamIDCheck:

-The SteamIDCheck method in the SteamIDConvert class will accept any variation of a Steam ID or Community link and return an array with the STEAM:0:0:123456 (key 'steam_id', Steam ID 64 format (key 'steamID64', and a link to their community page(key 'steam_link'). 

-There is also an assortment of functions for coverstion within the SteamIDConvert class used to support the SteamIDCheck method.


SteamQuery:

-The methods inside the SteamQuery class will send an API request to VALVe's server and return decoded JSON for use in your PHP script. It will also cache the information retrieved from VALVe's servers to speed up load times, and reduce queries. The cached files will refresh every 15 minutes by default, but you can adjust the cache time with the 'cache_time' variable.


-ConvertVanityURL will return the 64 bit Steam ID for the player

Everything you need is in the class_lib.php file, you will also need an API key from valve, put it in the API_KEY variable in class_lib.php. The index.php is just sample content to show what you can do with the library provided. It is in no way needed to use these functions.