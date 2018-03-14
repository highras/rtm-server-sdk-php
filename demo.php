<?php 

require_once "./vendor/autoload.php";

use highras\rtm\RTMServerClient;

$client = new RTMServerClient(1000001, '3a0023b6-bc80-488d-b312-c4a139b5ab1a', '117.50.4.158:13315');

try {
    var_dump($client->sendMessage(2, 3, 51, "我是新闻!", "attrs"));
} catch (Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getCode());
}
