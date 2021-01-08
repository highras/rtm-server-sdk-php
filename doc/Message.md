# Message Api

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

* `getMessageNum($type, $xid, $mtypes = NULL, $begin = NULL, $end = NULL)`: Get message num
    * `type`: **(int)**: 2: group, 3: room
    * `xid`: **(long)** Group ID or Room ID
    * `mtypes`: **(array(int))** mtype list default is NULL
    * `begin`: **(long)** begin timestamp in milliseconds, default is NULL
    * `end`: **(long)** end timestamp in milliseconds, default is NULL
    * return:
      * `{ sender:int16, num:int64 }`   sender: user num,  num: message num

