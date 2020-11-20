# rtm-server-sdk-php

## Requirements

* PHP >= 5.4

* ext-mcrypt

* ext-gmp

* ext-msgpack

## Installations

The preferred way to install this sdk is through [composer](http://getcomposer.org/download/).

> Note: Check the [composer.json](https://github.com/highras/rtm-server-sdk-php/blob/master/composer.json) for this SDK's requirements and dependencies. 
Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

Either run

```
$ php composer.phar require highras/rtm "dev-master"
```

or add

```
"highras/rtm": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Notice

* Before using the SDK, please make sure the server time is correct, RTM-Server will check whether the signature time has expired

## API

* `__construct($pid, $secretKey, $endpoint, $timeout = 5000)`: RTMServerClient Construct
    * `pid`: **(int)** Project ID
    * `secretKey`: **(string)** Secret key
    * `endpoint`: **(string)** Server endpoint
    * `timeout`: **(int)** connection timeout(ms)
    
* `enableEncryptor($peerPubData)`: Enable encrypted connection
    * `peerPubData`: **(string)**  Certificate content
    
* `enableEncryptorByFile($file)`: Enable encrypted connection
    * `file`: **(string)**  Certificate file path
    
### Token Functions

Please refer [Token Functions](doc/Token.md)

### User Functions

Please refer [User Functions](doc/User.md)

### Room Functions

Please refer [Room Functions](doc/Room.md)

### Group Functions

Please refer [Group Functions](doc/Group.md)

### Friend Functions

Please refer [Friend Functions](doc/Friend.md)

### Message Functions

Please refer [Message Functions](doc/Message.md)
    
### Chat Functions

Please refer [Chat Functions](doc/Chat.md)
 
### File Functions

Please refer [File Functions](doc/File.md)

### Device Functions

Please refer [Device Functions](doc/Device.md)

### Data Functions

Please refer [Data Functions](doc/Data.md)

### Error Codes

Please refer [Error Codes](https://github.com/highras/rtm-server-sdk-php/blob/master/src/RTMErrorCode.php)
