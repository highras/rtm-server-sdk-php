# Room Api

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
