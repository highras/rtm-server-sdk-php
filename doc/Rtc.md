# RTC Api

* `inviteUserIntoVoiceRoom($rid, $toUids, $fromUid)`: Invite User Into Voice Room
    * `rid`: **(long)** Room ID
    * `toUids`: **(array)** List of the uids invited
    * `fromUid`: **(long)** Uid of the inviter

* `closeVoiceRoom($rid)`: Close Voice Room
    * `rid`: **(long)** Room ID 
 
* `kickoutFromVoiceRoom($uid, $rid, $fromUid)`: Kickout User From Voice Room
    * `uid`: **(long)** User ID
    * `rid`: **(long)** Room ID
    * `fromUid`: **(long)**  Kickout Uid
      
* `getVoiceRoomList()`: Get List Of The Voice Rooms
    * `uid`: **(long)** User ID  
    * return：
      * List of rids     

* `getVoiceRoomMembers($rid)`: Get Members Of The Voice Room
    * `rid`: **(long)** Room ID
    * return：
        * `uids`: **(array(long))** User IDs of members
        * `managers`: **(array(long))** User IDs of managers      
 
* `getVoiceRoomMemberCount($rid) `: Get Member Count of the Voice Room
    * `rid`: **(long)** Room ID
    * return:
      * Number of count
 
* `setVoiceRoomMicStatus($rid, $status)`: Set the Default Microphone Status of the Voice Room
    * `rid`: **(long)** Room ID
    * `status`: **(bool)** default status, false for close, true for open

* `pullIntoVoiceRoom($rid, $toUids)`: Pull User Into the Voice Room
    * `rid`: **(long)** Room ID
    * `toUids`: **(array(long))** List of the uids pulled
