# Group Api

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
    * `gid`: **(long)** Group ID, if NULL is set, all group is banned for this user
    * `uid`: **(long)** User ID   
    * `btime`: **(int)** Mute duration, starting from the current time, in seconds     
      
* `removeGroupBan($gid, $uid)`: Cancel group ban,
    * `gid`: **(long)** Group ID, if NULL is set, all group is removed ban for this user
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