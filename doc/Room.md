# Room Api

* `addRoomBan($rid, $uid, $btime)`: Add room ban
    * `rid`: **(long)** Room ID, if NULL is set, all room is banned for this user
    * `uid`: **(long)** User ID
    * `btime`: **(int)** Mute duration, starting from the current time, in seconds
  
* `removeRoomBan($rid, $uid)`: Cancel room ban
    * `rid`: **(long)** Room ID, if NULL is set, all room is removed ban for this user  
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
    * `rid`: **(long)** Room ID
    * `uid`: **(long)** Member User ID 
    
* `deleteRoomMember($rid, $uid)`: Delete member of room
    * `rid`: **(long)** Room ID     
    * `uid`: **(long)** User ID

* `getRoomMembers($rid)`: Get members of room
    * `rid`: **(long)** Room ID     
    * return:
      * array(int64) Members user ID room

* `getRoomCount($rid)`: Get room's members count
    * `rid`: **(long)** Room ID     
    * return:
      * int32 count of members