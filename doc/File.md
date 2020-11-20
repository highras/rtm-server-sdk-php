# File Api

* `sendFile($from, $to, $mtype, $file, $attrs = array())`: Send file
    * `from`: **(long)** User ID of sender
    * `to`: **(long)** User ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    * `attrs`: Additional information of business message, default is `array()`

* `sendFiles($from, $tos, $mtype, $file, $attrs = array())`: Send multi-user file
    * `from`: **(long)** User ID of sender
    * `tos`: **(long)** User IDs of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    * `attrs`: Additional information of business message, default is `array()`

* `sendRoomFile($from, $rid, $mtype, $file, $attrs = array())`: Send room file
    * `from`: **(long)** User ID of sender
    * `rid`: **(long)** Room ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    * `attrs`: Additional information of business message, default is `array()`

* `sendGroupFile($from, $gid, $mtype, $file, $attrs = array())`: Send group file
    * `from`: **(long)** User ID of sender
    * `gid`: **(long)** Group ID of reciever
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    * `attrs`: Additional information of business message, default is `array()`

* `broadcastFile($from, $mtype, $file, $attrs = array())`: Send broadcast file
    * `from`: **(long)** User ID of sender
    * `mtype`: **(byte)** Message type (RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)
    * `file` : File path
    * `attrs`: Additional information of business message, default is `array()`