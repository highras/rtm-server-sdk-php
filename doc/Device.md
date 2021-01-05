# Device Api

* `addDevice($uid, $appType, $deviceToken)`: Add device
    * `uid`: **(long)** User id
    * `appType`: **(strring)** app type, apns or fcm
    * `deviceToken`: **(strring)** deviceToken
    
* `removeDevice($uid, $deviceToken)`: Remove device
    * `uid`: **(long)** User id
    * `deviceToken`: **(strring)** deviceToken  

* `addDevicePushOption($uid, $type, $xid, $mtypes = null)`: Add device push option
    * `uid`: **(long)** User id
    * `type`: **(int)** 0: p2p, 1: group
    * `xid`: **(long)** user id for p2p, group id for group
    * `mtypes`: **(array(int))** Disabled message types. If mTypes is null or empty, means all message types are disalbed for push.

* `removeDevicePushOption($uid, $type, $xid, $mtypes = null)`: Remove device push option
    * `uid`: **(long)** User id
    * `type`: **(int)** 0: p2p, 1: group
    * `xid`: **(long)** user id for p2p, group id for group
    * `mtypes`: **(array(int))** Disabled message types. If mTypes is null or empty, means all message types are removed disalbe attributes for push.

* `getDevicePushOption($uid)`: get device push option
    * `uid`: **(long)** User id
    * return:
      * array('p2p' => array($uid => array($mtype)), 'group' => array($gid => array($mtype))) 
