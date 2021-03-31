<?php 

require_once "./vendor/autoload.php";

use highras\rtm\RTMServerClient;

$client = new RTMServerClient(11000001, 'ef3617e5-e886-4a4e-9eef-7263c0320628', '161.189.171.91:13315');

try {
    var_dump($client->getRoomMembers(111));
    
} catch (Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getCode());
}
