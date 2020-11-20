# Chat Api

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

