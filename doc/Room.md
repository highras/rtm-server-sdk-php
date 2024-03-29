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

* `addUserRooms($uid, $rids)`: Add user to rooms
    * `uid`: **(long)** Member User ID
    * `rids`: **(array(long))** Room IDs

* `deleteUserRooms($uid, $rids)`: Delete user from rooms
    * `uid`: **(long)** Member User ID
    * `rids`: **(array(long))** Room IDs

* `getRoomMembers($rid)`: Get members of room
    * `rid`: **(long)** Room ID     
    * return:
      * array(int64) User ID list of room members

* `getRoomCount($rids)`: Get room's members count
    * `rids`: **(long)** Room IDs    
    * return:
      * map<long, int>: rid => num
