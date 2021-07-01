# RTC Api

* `inviteUserIntoRTCRoom($rid, $toUids, $fromUid)`: Invite User Into RTC Room
    * `rid`: **(long)** Room ID
    * `toUids`: **(array)** List of the uids invited
    * `fromUid`: **(long)** Uid of the inviter

* `closeRTCRoom($rid)`: Close RTC Room
    * `rid`: **(long)** Room ID 
 
* `kickoutFromRTCRoom($uid, $rid, $fromUid)`: Kickout User From RTC Room
    * `uid`: **(long)** User ID
    * `rid`: **(long)** Room ID
    * `fromUid`: **(long)**  Kickout Uid
      
* `getRTCRoomList()`: Get List Of The RTC Rooms
    * `uid`: **(long)** User ID  
    * return：
      * List of rids     

* `getRTCRoomMembers($rid)`: Get Members Of The RTC Room
    * `rid`: **(long)** Room ID
    * return：
        * `uids`: **(array(long))** User IDs of members
        * `administrators`: **(array(long))** User IDs of administrators
        * `owner`: **(long)** User ID of owner      
 
* `getRTCRoomMemberCount($rid) `: Get Member Count of the RTC Room
    * `rid`: **(long)** Room ID
    * return:
      * Number of count
 
* `setRTCRoomMicStatus($rid, $status)`: Set the Default Microphone Status of the RTC Room
    * `rid`: **(long)** Room ID
    * `status`: **(bool)** default status, false for close, true for open

* `pullIntoRTCRoom($rid, $toUids, $type)`: Pull User Into the Voice Room
    * `rid`: **(long)** Room ID
    * `toUids`: **(array(long))** List of the uids pulled
    * `type`: **(int)**  1 voice, 2 video

* `adminCommand($rid, $uids, $type)`: Admin command
    * `rid`: **(long)** Room ID
    * `uids`: **(array(long))** List of the uids
    * `type`: **(int)**  administrator command
