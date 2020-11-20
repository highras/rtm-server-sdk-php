# Friend Api

* `addFriends($uid, $friends)`: Add friend
    * `uid`: **(long)** User ID
    * `friends`: **(long[])** Friends user ID

* `delFriends($uid, $friends)`: Delete friend
    * `uid`: **(long)** User ID
    * `friends`: **(long[])** Friends user ID
    
* `getFriends($uid)`: Get friend
    * `uid`: **(long)** User ID
    * return:
      * array(uid)
      
* `isFriend($uid, $fuid)`: Check is friend
    * `uid`: **(long)** User ID
    * `fuid`: **(long)** other user id
    * return:
      * bool 
    
* `isFriends($uid, $fuids)`: Check is friend
    * `uid`: **(long)** User ID
    * 返回：
      * array(uid)

* `addBlacks($uid, $blacks)`: Block users, add up to 100 people each time. After blocking, the other party cannot send messages to himself, but he can send messages to the other party. Both parties can get the session and historical messages normally.
    * `uid`: **(long)** User ID
    * `blacks`: **(long[])** other user ids

* `delBlacks($uid, $blacks)`: Unblocking, up to 100 people each time
    * `uid`: **(long)** User ID
    * `blacks`: **(long[])** other user ids
    
* `getBlacks($uid)`: Get the list of users who have been banned by me (uid)
    * `uid`: **(long)** User ID
    * return:
      * array(uid)
      
* `isBlack($uid, $buid)`: Judge the black relationship, whether the uid is blacked by the user of the buid, used when sending a single message
    * `uid`: **(long)** User ID
    * `buid`: **(long)** other user ids
    * return:
      * bool 
    
* `isBlacks($uid, $buids)`: Judge the black relationship, get the friendship of up to 100 people each time, whether the uid is blacked by the user in the buids, used when sending multi-person messages
    * `uid`: **(long)** User ID
    * return:
      * array(buids)