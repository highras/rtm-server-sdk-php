<?php 

require_once "./vendor/autoload.php";

use highras\rtm\RTMServerClient;

$client = new RTMServerClient(11000001, 'xxxxx-xxxx-xxxx-xxxx', 'ENDPOINT_HOST');

try {
    var_dump($client->getRoomMembers(111));
    
} catch (Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getCode());
}
