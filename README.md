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
    
### Command From Client

* `getToken($uid)`: Get login token
    * `uid`: **(long)** User ID
    * return：
      * string  
      
* `kickOut($uid, $ce = null)`: Kickout a user
    * `uid`: **(long)** User ID
    * `ce`: **(strring)** If ce is not empty, only one of the connections will be kicked out, used in multi-user login situation     

* `addDevice($uid, $appType, $deviceToken)`: Add device
    * `uid`: **(long)** User id
    * `appType`: **(strring)** app type, apns or fcm
    * `deviceToken`: **(strring)** deviceToken
    
* `removeDevice($uid, $deviceToken)`: Remove device
    * `uid`: **(long)** User id
    * `deviceToken`: **(strring)** deviceToken    
  
* `removeToken($uid)`: Remove Login token
    * `uid`: **(long)** User ID
    
### Message 

* `sendMessage($from, $to, $mtype, $msg, $attrs)`: Send P2P message
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `mtype`: **(byte)** Business message type (please use 51-127, and the value of 50 and below is prohibited)
    * `msg`: **(string)** Message content
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return：
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
* `sendMessages($from, $tos, $mtype, $msg, $attrs)`: Send multi-person messages
    * `from`: **(long)** User ID of sender
    * `tos`: **(long[])** User IDs of reciever
    * `mtype`: **(byte)** Business message type (please use 51-127, and the value of 50 and below is prohibited)
    * `msg`: **(string)** Message content
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return：
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
* `sendGroupMessage($from, $gid, $mtype, $msg, $attrs)`: Send group messages
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group id of reciever
    * `mtype`: **(byte)** Business message type (please use 51-127, and the value of 50 and below is prohibited)
    * `msg`: **(string)** Message content
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return：
      * `mtime`: Response timestamp
      * `mid` : Message ID  

* `sendRoomMessage($from, $rid, $mtype, $msg, $attrs)`: Send room messages
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID of reciever
    * `mtype`: **(byte)** Business message type (please use 51-127, and the value of 50 and below is prohibited)
    * `msg`: **(string)** Message content
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return：
      * `mtime`: Response timestamp
      * `mid` : Message ID  
      
* `broadcastMessage($from, $mtype, $msg, $attrs)`: Broadcast messages
    * `from`: **(long)** Admin User ID
    * `mtype`: **(byte)** Business message type (please use 51-127, and the value of 50 and below is prohibited)
    * `msg`: **(string)** Message content
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return：
      * `mtime`: Response timestamp
      * `mid` : Message ID  
      
* `getP2PMessage($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: Get P2P message history
    * `uid`: **(long)** User ID of reciever
    * `ouid`: **(long)** User ID of sender
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * `mtypes`: **([int])** mtype list
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<P2PMsg> }`

* `getGroupMessage($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: Get group message
    * `gid`: **(long)** Group ID
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * `mtypes`: **([int])** mtype list
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<GroupMsg> }`
      
* `getRoomMessage($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: Get room message
    * `rid`: **(long)** Room ID
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * `mtypes`: **([int])** mtype list
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<RoomMsg> }`
      
* `getBroadcastMessage($num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: Get broadcast message
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * `mtypes`: **([int])** mtype list
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<BroadcastMsg> }`
 
 * `getMessage($mid, $from, $xid, $type)`: Get Message
    * `mid`: **(long)**: Message ID
    * `from`: **(long)** User ID of sender
    * `xid`: **(long)** to/gid/rid 
    * `type`: **(int)** 1,p2p; 2,group; 3, room; 4, broadcast
     
* `deleteP2PMessage($mid, $from, $to)`: Delete P2P message
    * `mid`: **(long)**: Message ID
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever

* `deleteGroupMessage($mid, $from, $gid)`: Delete group message
    * `mid`: **(long)**: Message ID
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID
    
* `deleteRoomMessage($mid, $from, $rid)`: Delete room message
    * `mid`: **(long)**: Message ID
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID
    
* `deleteBroadcastMessage($mid, $from)`: Delete broadcast message
    * `mid`: **(long)**: Message ID
    * `from`: **(long)** User ID of sender
      
### Chat
      
* `sendChat($from, $to, $msg, $attrs)`: Send P2P chat message
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  

* `sendChats($from, $tos, $msg, $attrs)`: Send Muti-User chat message
    * `from`: **(long)** User ID of sender
    * `tos`: **(long[])** User IDs of reciever
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  
      
* `sendCmd($from, $to, $msg, $attrs)`: Send chat related system commands
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  

* `sendCmds($from, $tos, $msg, $attrs)`: Send chat related muti-user system commands
    * `from`: **(long)** User ID of sender
    * `tos`: **(long[])** User IDs of reciever
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  

* `sendGroupChat($from, $gid, $msg, $attrs)`: Send group chat message
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  

* `sendRoomChat($from, $rid, $msg, $attrs)`: Send room chat message
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID  
      
 * `broadcastChat($from, $msg, $attrs)`: Broadcast chat message
    * `from`: **(long)** Admin User ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
* `sendGroupCmd($from, $gid, $msg, $attrs)`: Send group chat related system commands
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendRoomCmd($from, $rid, $msg, $attrs)`: Send room chat related system commands
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
 * `broadcastCmd($from, $msg, $attrs)`: Broadcast chat related system commands
    * `from`: **(long)** Admin ID
    * `msg`: **(string)** The content of the chat message
    * `attrs`: **(string)** Additional information of business message, default is `""`
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

 * `getP2PChat($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: Get P2P chat/cmd/file history
    * `uid`: **(long)** User ID of reciever
    * `ouid`: **(long)** User ID of sender
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<P2PMsg> }`
      
 * `getGroupChat($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: Get group chat/cmd/file history
    * `gid`: **(long)** Group ID
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<GroupMsg> }`
      
 * `getRoomChat($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: Get room chat/cmd/file history
    * `rid`: **(long)** Room ID
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * return:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<RoomMsg> }`
      
 * `getBroadcastChat($num, $desc, $begin = 0, $end = 0, $lastId = 0)`: Get broadcast chat/cmd/file history
    * `desc`: **(bool)** `true`: Turn pages in reverse order starting from `end` timestamp, otherwise, turn pages in positive order starting from `start` timestamp
    * `num`: **(int)** Get number, **Get up to 20 items at a time, 10 items are recommended**
    * `begin`: **(long)** start timestamp, ms, default is `0`
    * `end`: **(long)** end timestamp, ms, default is `0`
    * `lastId`: **(long)** The id of the last message, the first time it defaults to pass `0`
    * return
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<BroadcastMsg> }`

* `deleteP2PChat($mid, $from, $to)`: Delete P2P chat
    * `mid`: **(long)**: Message ID 
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever

* `deleteGroupChat($mid, $from, $gid)`: Delete group chat
    * `mid`: **(long)**: Message ID 
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID of reciever
    
* `deleteRoomChat($mid, $from, $rid)`: Delete room chat
    * `mid`: **(long)**: Message ID 
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID of reciever
    
* `deleteBroadcastChat($mid, $from)`: Delete broadcast chat
    * `mid`: **(long)**: Message ID 
    * `from`: **(long)** User ID of sender
    
* `sendImageFile($from, $to, $file)`: Send image file
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `file`: **(string)** Image file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendAudioFile($from, $to, $file)`: Send audio file
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `file`: **(string)** Audio file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendVideoFile($from, $to, $file)`: Send video file
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `file`: **(string)** Video file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendAudioFiles($from, $tos, $file)`: Send muti-user audio file
    * `from`: **(long)** User ID of sender
    * `tos`: **(long[])** User IDs of reciever
    * `file`: **(string)** Audio file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendGroupAudioFile($from, $gid, $file)`: Send group audio file
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID of reciever
    * `file`: **(string)** Audio file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID

* `sendRoomAudioFile($from, $rid, $file)`: Send room audio file
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID of reciever
    * `file`: **(string)** Audio file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
 * `broadcastAudioFile($from, $file)`: Broadcast audio file
    * `from`: **(long)** User ID of sender
    * `file`: **(string)** Audio file
    * return:
      * `mtime`: Response timestamp
      * `mid` : Message ID
      
* `translate($text, $dst, $src = '', $type = 'chat', $profanity = 'off', $uid = NULL)`: Translate
    * `text`: **(string)**: Text content
    * `dst`: **(string)** Target language type
    * `src`: **(string)** Original language type
    * `type`: **(string)** The optional values are chat or mail. If not specified,'chat' is used by default
    * `profanity`: **(string)** Sensitive language filtering. Set to one of the following 3 items: off, stop, censor
    * return:
      * sourceText: Original chat message
      * source：Original chat message language type (checked by translation system)
      * targetText：Translated chat message
      * target：Language type after translation

* `profanity($text, $classify = false, $uid = NULL)`: Sensitive word filtering
    * `text`: **(string)**: Text content
    * `classify`: **(bool)** Whether to perform text classification detection
    * return:
      * text: Filtered chat messages
      
* `speech2Text($audio, $type, $lang, $codec = "", $srate = 0, $uid = NULL)`: Speech to text
    * `audio`: **(string)**: Audio URL or content (lang&codec&srate is required)
    * If codec is empty, it will default to AMR_WB, if srate is 0 or empty, it will default to 16000.
    * `uid`: **(int)**: User ID 
    * return:
      * text: Text Content
      * lang: Language
      
* `textCheck($text, $uid = NULL)`: Text review, return the filtered string or return an error
    * `text`: **(string)**: Text content
    * `uid`: **(int)**: User ID 
    * return:
      * result: 0: pass，2，not pass
      * text: The text content after the sensitive word filtering, the sensitive words contained in it will be replaced with *, if it is not marked with a star, there is no such field
      * tags: Triggered categories, such as pornography and politics, etc., see text review category for details
      * wlist: Sensitive word list
    
* `imageCheck($image, $type, $uid = NULL)`: Picture review
    * `image`: **(string)**: The url or content of the picture
    * `type`: **(int)**: 1, url, 2, content
    * `uid`: **(int)**: User ID 
    * return:
      * result: 0: pass，2，not pass
      * tags: Triggered categories, such as pornography and politics, etc., see the picture review category for details
      
* `audioCheck($audio, $type, $lang, $codec = "", $srate = 0, $uid = NULL)`: Audio review
    * `audio`: **(string)**: Audio URL or content (lang&codec&srate is required)
    * If codec is empty, it will default to AMR_WB, if srate is 0 or empty, it will default to 16000.
    * `type`: **(int)**: 1, url, 2, content
    * `lang`: **(string)**: Language
    * `uid`: **(int)**: User ID 
    * return:
      * result: 0: pass，2，not pass
      * tags: Triggered categories, such as pornography and politics, etc., see the picture review category for details

* `videoCheck($video, $type, $videoName, $uid = NULL)`: Video review
    * `video`: **(string)**: Audio URL or content
    * `type`: **(int)**: 1, url, 2, content
    * `videoName`: **(string)**: Video name
    * `uid`: **(int)**: User ID 
    * return:
      * result: 0: pass，2，not pass
      * tags: Triggered categories, such as pornography and politics, etc., see the picture review category for details


### File

* `sendFile($from, $to, $mtype, $file)`: Send file
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path

* `sendFiles($from, $tos, $mtype, $file)`: Send multi-user file
    * `from`: **(long)** User ID of sender
    * `tos`: **(long)** User IDs of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path

* `sendRoomFile($from, $rid, $mtype, $file)`: Send room file
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path

* `sendGroupFile($from, $gid, $mtype, $file)`: Send group file
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path

* `broadcastFile($from, $mtype, $file)`: Send broadcast file
    * `from`: **(long)** User ID of sender
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    
    
### User 

* `getOnlineUsers($uids)`: Get online users
    * `uids`: **(long)** List of user ids
    * return:
      * array(uid)   

* `addProjectBlack($uid, $btime)`: Add project blacklist
    * `uid`: **(long)** User ID 
    * `btime`: **(int)** Mute duration, starting from the current time, in seconds    
 
* `removeProjectBlack($uid)`: Cancel project blacklist  
    * `uid`: **(long)** User ID   
      
* `isProjectBlack($uid)`: Determine whether it is a project blacklist
    * `uid`: **(long)** User ID  
    * return：
      * bool        

* `setUserInfo($uid, $oinfo = NULL, $pinfo = NULL)`: Set user's public or private information
    * `uid`: **(long)** User ID        
    * `oinfo`: **(string)** Public info
    * `pinfo`: **(string)** Private info 
 
* `getUserInfo($uid) `: Get public or private information of users
    * `uid`: **(long)** User ID   
    * return:
      * oinfo
      * pinfo 
 
* `getUserOpenInfo($uids)`: Get public information of users
    * `uid`: **(long)** User ID   
    * return:
      * oinfo      
      
### Friend

* `addFriends($uid, $friends)`: Add friend
    * `uid`: **(long)** User ID
    * `friends`: **(long[])** Friends user ID

* `delFriends($uid, $friends)`: Delete friend
    * `uid`: **(long)** User ID
    * `friends`: **(long[])** Friends user ID
    
* `getFriends($uid)`: Get friend
    * `uid`: **(long)** User ID
    * return:
      * array(uid)
      
* `isFriend($uid, $fuid)`: Check is friend
    * `uid`: **(long)** User ID
    * `fuid`: **(long)** other user id
    * return:
      * bool 
    
* `isFriends($uid, $fuids)`: Check is friend
    * `uid`: **(long)** User ID
    * 返回：
      * array(uid)

* `addBlacks($uid, $blacks)`: Block users, add up to 100 people each time. After blocking, the other party cannot send messages to himself, but he can send messages to the other party. Both parties can get the session and historical messages normally.
    * `uid`: **(long)** User ID
    * `blacks`: **(long[])** other user ids

* `delBlacks($uid, $blacks)`: Unblocking, up to 100 people each time
    * `uid`: **(long)** User ID
    * `blacks`: **(long[])** other user ids
    
* `getBlacks($uid)`: Get the list of users who have been banned by me (uid)
    * `uid`: **(long)** User ID
    * return:
      * array(uid)
      
* `isBlack($uid, $buid)`: Judge the black relationship, whether the uid is blacked by the user of the buid, used when sending a single message
    * `uid`: **(long)** User ID
    * `buid`: **(long)** other user ids
    * return:
      * bool 
    
* `isBlacks($uid, $buids)`: Judge the black relationship, get the friendship of up to 100 people each time, whether the uid is blacked by the user in the buids, used when sending multi-person messages
    * `uid`: **(long)** User ID
    * return:
      * array(buids)

### Group

* `addGroupMembers($gid, $uids)`: Add group members
    * `gid`: **(long)** Group ID
    * `uids`: **(long[])** Members ID

* `deleteGroupMembers($gid, $uids)`: Delete group members
    * `gid`: **(long)** Group ID
    * `uids`: **(long[])** Members ID
    
* `deleteGroup($gid)`: Delete group
    * `gid`: **(long)** Group ID

* `getGroupMembers($gid)`: Get group members
    * `gid`: **(long)** Group ID
    * return:
      * array(uid)

* `isGroupMember($gid, $uid)`: Determine whether you are a group member
    * `gid`: **(long)** Group ID
    * `uid`: **(long)** User ID
    * return:
      * bool 
      
* `getUserGroups($uid)`: Get group list of user
    * `uid`: **(long)** User ID
    * return:
      * array(gid)           
      
* `addGroupBan($gid, $uid, $btime)`: Add group ban
    * `gid`: **(long)** Group ID       
    * `uid`: **(long)** User ID   
    * `btime`: **(int)** Mute duration, starting from the current time, in seconds     
      
* `removeGroupBan($gid, $uid)`: Cancel group ban
    * `gid`: **(long)** Group ID       
    * `uid`: **(long)** User ID        
    
* `isBanOfGroup($gid, $uid)`: Check is group ban
    * `gid`: **(long)** Group ID  
    * `uid`: **(long)** User ID 
    * return:
      * bool  
      
* `setGroupInfo($gid, $oinfo = NULL, $pinfo = NULL)`: Set public or private information of the group
    * `gid`: **(long)** Group ID          
    * `oinfo`: **(string)** Public info
    * `pinfo`: **(string)** Private info   
 
* `getGroupInfo($gid)`: Get public or private information of the group
    * `gid`: **(long)** Group ID  
    * return:
      * oinfo
      * pinfo      
  
### Room  
  
* `addRoomBan($rid, $uid, $btime)`: Add room ban
    * `rid`: **(long)** Room ID       
    * `uid`: **(long)** User ID     
    * `btime`: **(int)** Mute duration, starting from the current time, in seconds
  
* `removeRoomBan($rid, $uid)`: Cancel room ban
    * `rid`: **(long)** Room ID         
    * `uid`: **(long)** User ID        
 
* `isBanOfRoom($rid, $uid)`: Determine whether to be muted by the room
    * `rid`: **(long)** Room ID   
    * `uid`: **(long)** User ID   
    * return:
      * bool  
 
* `setRoomInfo($rid, $oinfo = NULL, $pinfo = NULL)`: Set public or private information of the room
    * `rid`: **(long)** Room ID       
    * `oinfo`: **(string)** Public info
    * `pinfo`: **(string)** Private info
 
* `getRoomInfo($rid) `: Get public or private information of the room
    * `rid`: **(long)** Room ID     
    * return:
      * oinfo
      * pinfo 

* `addRoomMember($rid, $uid)`: Add room member
    * `rid`: **(long)** 房间 id
    * `uid`: **(long)** 成员用户id 
    
* `deleteRoomMember($rid, $uid)`: 删除房间成员
    * `rid`: **(long)** Room ID     
    * `uid`: **(long)** User ID


### Data

* `dataGet($uid, $key)`: Get stored data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key
    * return:
      * val    
      
* `dataSet($uid, $key, $value)`: Store data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key    
    * `value`: **(string)** value  
    
* `dataDelete($uid, $key)`: Delete stored data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key    



    
    
 
 
