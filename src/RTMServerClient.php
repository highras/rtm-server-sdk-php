<?php

namespace highras\rtm;

use highras\fpnn\TCPClient;

define("RTM_CHAT_MTYPE", 30);
define("RTM_AUDIO_MTYPE", 31);
define("RTM_CMD_MTYPE", 32);

define("DELETE_MSG_TYPE_P2P", 1);
define("DELETE_MSG_TYPE_GROUP", 2);
define("DELETE_MSG_TYPE_ROOM", 3);
define("DELETE_MSG_TYPE_BROADCAST", 4);


class CommonMsg 
{
    public $id;
    public $from;
    public $mtype;
    public $mid;
    public $msg;
    public $attrs;
    public $mtime;
}

class GroupMsg extends CommonMsg  {}
class RoomMsg extends CommonMsg {}
class BroadcastMsg extends CommonMsg {}

class P2PMsg
{
    public $id;
    public $direction;
    public $mtype;
    public $mid;
    public $msg;
    public $attrs;
    public $mtime;
}

class RTMServerClient
{
    private $client = null;
    private $pid;
    private $secretKey;
    private $midSeq = 0;
    private $saltSeq = 0;

    function __construct($pid, $secretKey, $endpoint, $timeout = 5000)
    {
        $arr = explode(':', $endpoint);
        $this->pid = $pid;
        $this->secretKey = $secretKey;
        $this->client = new TCPClient($arr[0], $arr[1], $timeout);
    }

    public function enableEncryptor($peerPubData)
    {
        $this->client->enableEncryptor($peerPubData, "secp256k1", 128);
    }

    public function enableEncryptorByFile($file)
    {
        $peerPubData = file_get_contents($file);
        $this->enableEncryptor($peerPubData);
    }

    private function generateMessageId()
    {
        return (int)((int)(microtime(true) * 1000) . mt_rand(10, 99));
    }

    private function generateSalt()
    {
        return (int)((int)(microtime(true) * 1000) . mt_rand(10000, 99999));
    }

    private function generateSignature($salt, $cmd, $ts)
    {
        return strtoupper(md5($this->pid . ':' . $this->secretKey . ':' . $salt . ':' . $cmd . ':' . $ts));
    }

    public function sendMessage($from, $to, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'sendmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mtype' => $mtype,
            'from' => $from,
            'to' => $to,
            'mid' => $mid,
            'msg' => $msg,
            'attrs' => $attrs
        ));

        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }

    /**
     * @param $from
     * @param $tos
     * @param $mtype
     * @param $msg
     * @param $attrs
     * @return array
     * @throws \Exception
     */
    public function sendMessages($from, $tos, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendmsgs", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'sendmsgs', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mtype' => $mtype,
            'from' => $from,
            'tos' => $tos,
            'mid' => $mid,
            'msg' => $msg,
            'attrs' => $attrs
        ));
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }

    /**
     * @param $from
     * @param $gid
     * @param $mtype
     * @param $msg
     * @param $attrs
     * @return array
     * @throws \Exception
     */
    public function sendGroupMessage($from, $gid, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'sendgroupmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mtype' => $mtype,
            'from' => $from,
            'gid' => $gid,
            'mid' => $mid,
            'msg' => $msg,
            'attrs' => $attrs
        ));
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }

    /**
     * @param $from
     * @param $rid
     * @param $mtype
     * @param $msg
     * @param $attrs
     * @return array
     * @throws \Exception
     */
    public function sendRoomMessage($from, $rid, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'sendroommsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mtype' => $mtype,
            'from' => $from,
            'rid' => $rid,
            'mid' => $mid,
            'msg' => $msg,
            'attrs' => $attrs
        ));
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }

    /**
     * @param $from
     * @param $mtype
     * @param $msg
     * @param $attrs
     * @return array
     * @throws \Exception
     */
    public function broadcastMessage($from, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("broadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'broadcastmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mtype' => $mtype,
            'from' => $from,
            'mid' => $mid,
            'msg' => $msg,
            'attrs' => $attrs
        ));
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }
    
    public function sendChat($from, $to, $msg, $attrs)
    {
        return $this->sendMessage($from, $to, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendAudio($from, $to, $msg, $attrs)
    {
        return $this->sendMessage($from, $to, RTM_AUDIO_MTYPE, base64_encode($msg), $attrs); 
    }
    
    public function sendCmd($from, $to, $msg, $attrs)
    {
        return $this->sendMessage($from, $to, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendChats($from, $tos, $msg, $attrs)
    {
        return $this->sendMessages($from, $tos, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendAudios($from, $tos, $msg, $attrs)
    {
        return $this->sendMessages($from, $tos, RTM_AUDIO_MTYPE, base64_encode($msg), $attrs); 
    }
    
    public function sendCmds($from, $tos, $msg, $attrs)
    {
        return $this->sendMessages($from, $tos, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendGroupChat($from, $gid, $msg, $attrs)
    {
        return $this->sendGroupMessage($from, $gid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendGroupAudio($from, $gid, $msg, $attrs)
    {
        return $this->sendGroupMessage($from, $gid, RTM_AUDIO_MTYPE, base64_encode($msg), $attrs); 
    }
    
    public function sendGroupCmd($from, $gid, $msg, $attrs)
    {
        return $this->sendGroupMessage($from, $gid, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendRoomChat($from, $rid, $msg, $attrs)
    {
        return $this->sendRoomMessage($from, $rid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendRoomAudio($from, $rid, $msg, $attrs)
    {
        return $this->sendRoomMessage($from, $rid, RTM_AUDIO_MTYPE, base64_encode($msg), $attrs); 
    }
    
    public function sendRoomCmd($from, $rid, $msg, $attrs)
    {
        return $this->sendRoomMessage($from, $rid, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function broadcastChat($from, $msg, $attrs)
    {
        return $this->broadcastMessage($from, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function broadcastAudio($from, $msg, $attrs)
    {
        return $this->broadcastMessage($from, RTM_AUDIO_MTYPE, base64_encode($msg), $attrs); 
    }
    
    public function broadcastCmd($from, $msg, $attrs)
    {
        return $this->broadcastMessage($from, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function getP2PChat($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getP2PMessage($uid, $ouid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_AUDIO_MTYPE, RTM_CMD_MTYPE));
    }
    
    public function getGroupChat($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getGroupMessage($gid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_AUDIO_MTYPE));
    }
    
    public function getRoomChat($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getRoomMessage($rid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_AUDIO_MTYPE));
    }
    
    public function getBroadcastChat($num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getBroadcastMessage($num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_AUDIO_MTYPE)); 
    }
    
    public function deleteP2PChat($mid, $from, $to) {
        return $this->deleteP2PMessage($mid, $from, $to);
    }
    
    public function deleteGroupChat($mid, $from, $gid) {
        return $this->deleteGroupMessage($mid, $from, $gid);
    }
    
    public function deleteRoomChat($mid, $from, $rid) {
        return $this->deleteRoomMessage($mid, $from, $rid);
    }
    
    public function deleteBroadcastChat($mid, $from) {
        return $this->deleteBroadcastMessage($mid, $from);
    }
    
    public function translate($text, $dst, $src = '', $type = 'chat', $profanity = '', $postProfanity = false, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $param = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'translate', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'text' => $text,
            'src' => $src,
            'dst' => $dst,
            'type' => $type,
            'profanity' => $profanity,
            'postProfanity' => $postProfanity
        );
        if ($uid !== NULL)
            $param['uid'] = $uid;
        $response = $this->client->sendQuest("translate", $param);
        return [
            'source' => $response['source'],
            'target' => $response['target'],
            'sourceText' => $response['sourceText'],
            'targetText' => $response['targetText']
        ];
    }

    public function profanity($text, $classify = false, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $param = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'profanity', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'text' => $text,
            'classify' => $classify,
        );
        if ($uid !== NULL)
            $param['uid'] = $uid;
        $response = $this->client->sendQuest("profanity", $param);
        return $response;
    }

    public function transcribe($audio, $lang, $uid = NULL, $codec = NULL, $srate = 16000) {
        $salt = $this->generateSalt();
        $ts = time();
        $mid = $this->generateMessageId();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'transcribe', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'audio' => $audio,
            'lang' => $lang,
            'srate' => $srate,
        );
        if ($uid !== NULL)
            $params['uid'] = $uid;
        if ($codec !== NULL)
            $params['codec'] = $codec;
        $response = $this->client->sendQuest("transcribe", $params);
        return [
            'text' => $response['text'],
            'lang' => $response['lang']
        ];
    }

    public function addFriends($uid, $friends)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addfriends', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function delFriends($uid, $friends)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("delfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delfriends', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function getFriends($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getfriends', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isFriend($uid, $fuid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isfriend", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isfriend', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'fuid' => $fuid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isFriends($uid, $fuids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isfriends', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'fuids' => $fuids
        ));
        return isset($res['fuids']) ? $res['fuids'] : array();
    }

    public function addGroupMembers($gid, $uids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addgroupmembers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroupMembers($gid, $uids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("delgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delgroupmembers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroup($gid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("delgroup", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delgroup', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid
        ));
    }

    public function getGroupMembers($gid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getgroupmembers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isGroupMember($gid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isgroupmember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isgroupmember', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function getUserGroups($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getusergroups", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getusergroups', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        return isset($res['gids']) ? $res['gids'] : array();
    }

    public function getToken($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("gettoken", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'gettoken', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        return isset($res['token']) ? $res['token'] : '';
    }

    public function getOnlineUsers($uids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getonlineusers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getonlineusers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uids' => $uids
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function addGroupBan($gid, $uid, $btime)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addgroupban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addgroupban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeGroupBan($gid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("removegroupban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removegroupban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uid' => $uid
        ));
    }

    public function addRoomBan($rid, $uid, $btime)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addroomban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addroomban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeRoomBan($rid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("removeroomban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removeroomban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function addProjectBlack($uid, $btime)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addprojectblack', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeProjectBlack($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("removeprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removeprojectblack', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
    }

    public function isBanOfGroup($gid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isbanofgroup", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isbanofgroup', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

	public function setGroupInfo($gid, $oinfo = NULL, $pinfo = NULL)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'setgroupinfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid
        );
        if ($oinfo !== NULL)
            $params['oinfo'] = $oinfo;
        if ($pinfo !== NULL)
            $params['pinfo'] = $pinfo;
        $res = $this->client->sendQuest("setgroupinfo", $params);
    }

	public function getGroupInfo($gid) 
	{
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getgroupinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getgroupinfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

    public function isBanOfRoom($rid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isbanofroom", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isbanofroom', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

	public function setRoomInfo($rid, $oinfo = NULL, $pinfo = NULL)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'setroominfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
        );
        if ($oinfo !== NULL)
            $params['oinfo'] = $oinfo;
        if ($pinfo !== NULL)
            $params['pinfo'] = $pinfo;
        $res = $this->client->sendQuest("setroominfo", $params);
    }

	public function getRoomInfo($rid) 
	{
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getroominfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getroominfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

    public function isProjectBlack($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isprojectblack', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function setUserInfo($uid, $oinfo = NULL, $pinfo = NULL)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'setuserinfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        );
        if ($oinfo !== NULL)
            $params['oinfo'] = $oinfo;
        if ($pinfo !== NULL)
            $params['pinfo'] = $pinfo;
        $res = $this->client->sendQuest("setuserinfo", $params);
    }

	public function getUserInfo($uid) 
	{
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getuserinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getuserinfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

	public function getUserOpenInfo($uids) 
	{
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getuseropeninfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getuseropeninfo', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uids' => $uids
        ));
		return $res['info'];
	}
    
    public function addRoomMember($rid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addroommember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addroommember', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function deleteRoomMember($rid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("delroommember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delroommember', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function sendFile($from, $to, $mtype, $file)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
            'cmd' => 'sendfile',
            'from' => $from,
            'to' => $to
        ]);

        $token = $answer["token"];
        $endpoint = $answer["endpoint"];
        $ipport = explode(":", $endpoint);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $fileClient->sendQuest("sendfile", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'to' => $to,
            'mid' => $this->generateMessageId(),
            'file' => $content,
            'attrs' => json_encode([
                'sign' => $sign,
                'ext' => $ext,
            ])
        ]);
    }

    /**
     * @param int $uid
     * @param int $ouid
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastId
     * @return mixed
     * @throws \Exception
     */
    public function getP2PMessage($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getp2pmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getp2pmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'ouid' => $ouid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastId,
            'mtypes' => $mtypes
        ));
        $msgs = array();
        foreach ($res['msgs'] as $v) {
            $msgStruct = new P2PMsg();
            $msgStruct->id = (int)$v[0];
            $msgStruct->direction = (bool)$v[1];
            $msgStruct->mtype = (int)$v[2];
            $msgStruct->mid = (int)$v[3];
            $msgStruct->msg = $v[5];
            $msgStruct->attrs = $v[6];
            $msgStruct->mtime = (int)$v[7];
            $msgs[] = $msgStruct;
        }
        return array(
            'num' => (int)$res['num'],
            'lastId' => (int)$res['lastid'],
            'begin' => (int)$res['begin'],
            'end' => (int)$res['end'],
            'msgs' => $msgs
        );
    }

    /**
     * @param int $gid
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastId
     * @return mixed
     * @throws \Exception
     */
    public function getGroupMessage($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getgroupmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'gid' => $gid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastId,
            'mtypes' => $mtypes
        ));
        $msgs = array();
        foreach ($res['msgs'] as $v) {
            $msgStruct = new GroupMsg();
            $msgStruct->id = (int)$v[0];
            $msgStruct->from = (int)$v[1];
            $msgStruct->mtype = (int)$v[2];
            $msgStruct->mid = (int)$v[3];
            $msgStruct->msg = $v[5];
            $msgStruct->attrs = $v[6];
            $msgStruct->mtime = (int)$v[7];
            $msgs[] = $msgStruct;
        }
        return array(
            'num' => (int)$res['num'],
            'lastId' => (int)$res['lastid'],
            'begin' => (int)$res['begin'],
            'end' => (int)$res['end'],
            'msgs' => $msgs
        );
    }

    /**
     * @param int $rid
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastId
     * @return mixed
     * @throws \Exception
     */
    public function getRoomMessage($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getroommsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastId,
            'mtypes' => $mtypes
        ));
        $msgs = array();
        foreach ($res['msgs'] as $v) {
            $msgStruct = new RoomMsg();
            $msgStruct->id = (int)$v[0];
            $msgStruct->from = (int)$v[1];
            $msgStruct->mtype = (int)$v[2];
            $msgStruct->mid = (int)$v[3];
            $msgStruct->msg = $v[5];
            $msgStruct->attrs = $v[6];
            $msgStruct->mtime = (int)$v[7];
            $msgs[] = $msgStruct;
        }
        return array(
            'num' => (int)$res['num'],
            'lastId' => (int)$res['lastid'],
            'begin' => (int)$res['begin'],
            'end' => (int)$res['end'],
            'msgs' => $msgs
        );
    }

    /**
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastId
     * @return mixed
     * @throws \Exception
     */
    public function getBroadcastMessage($num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        return $this->client->sendQuest("getbroadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getbroadcastmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastId,
            'mtypes' => $mtypes
        ));
        $msgs = array();
        foreach ($res['msgs'] as $v) {
            $msgStruct = new BroadcastMsg();
            $msgStruct->id = (int)$v[0];
            $msgStruct->from = (int)$v[1];
            $msgStruct->mtype = (int)$v[2];
            $msgStruct->mid = (int)$v[3];
            $msgStruct->msg = $v[5];
            $msgStruct->attrs = $v[6];
            $msgStruct->mtime = (int)$v[7];
            $msgs[] = $msgStruct;
        }
        return array(
            'num' => (int)$res['num'],
            'lastId' => (int)$res['lastid'],
            'begin' => (int)$res['begin'],
            'end' => (int)$res['end'],
            'msgs' => $msgs
        );
    }

    public function getMessage($mid, $from, $xid, $type)
    {
        $salt = $this->generateSalt();
        $ts = time();
        return $this->client->sendQuest('getmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$xid,
            'type' => $type 
        ]);
    }

    public function deleteP2PMessage($mid, $from, $to)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('delmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$to,
            'type' => DELETE_MSG_TYPE_P2P 
        ]);
    }
    
    public function deleteGroupMessage($mid, $from, $gid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('delmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$gid,
            'type' => DELETE_MSG_TYPE_GROUP 
        ]);
    }
    
    public function deleteRoomMessage($mid, $from, $rid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('delmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$rid,
            'type' => DELETE_MSG_TYPE_ROOM 
        ]);
    }
    
    public function deleteBroadcastMessage($mid, $from)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('delmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'type' => DELETE_MSG_TYPE_BROADCAST,
            'xid' => 0
        ]);
    }

    /**
     * @param integer $uid
     * @param null|string $ce client endpoint
     * @throws \Exception
     */
    public function kickOut($uid, $ce = null)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('kickout', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'kickout', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'ce' => $ce
        ]);
    }

    public function addDevice($uid, $appType, $deviceToken)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('adddevice', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'adddevice', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'apptype' => $appType,
            'adddevice' => $deviceToken
        ]);
    }
    
    public function removeDevice($uid, $deviceToken)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('removedevice', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removedevice', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'adddevice' => $deviceToken
        ]);
    }
    
    public function removeToken($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('removetoken', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removetoken', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid
        ]);
    }

    public function dataGet($uid, $key) 
    {
        $salt = $this->generateSalt();
        $ts = time();
        return $this->client->sendQuest('dataget', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'dataget', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'key' => $key
        ]);
    }

    public function dataSet($uid, $key, $value)
    {
        $salt = $this->generateSalt();
        $ts = time();
        return $this->client->sendQuest('dataset', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'dataset', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'key' => $key,
            'val' => $value
        ]);
    }
    
    public function dataDelete($uid, $key)
    {
        $salt = $this->generateSalt();
        $ts = time();
        return $this->client->sendQuest('datadel', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'datadel', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'key' => $key
        ]);
    }


}
