# User Api

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
      
* `kickOut($uid)`: Kickout a user
    * `uid`: **(long)** User ID

* `addSessions($uid, $uids)`: Add sessions
    * `uid`: **(long)** User ID
    * `uids`: **(array(long))** Target User IDs

* `removeSessions($uid, $uids, $oneway = false)`: Remove sessions
    * `uid`: **(long)** User ID
    * `uids`: **(array(long))** Target User IDs
    * `oneway`: **(bool)** Whether to remove in one direction
