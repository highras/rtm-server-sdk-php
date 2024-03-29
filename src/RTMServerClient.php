<?php

namespace highras\rtm;

use highras\fpnn\TCPClient;

define("RTM_SDK_VERSION", "1.0.19");
define("RTM_API_VERSION", "2.7.0");

define("RTM_CHAT_MTYPE", 30);
define("RTM_CMD_MTYPE", 32);

define("RTM_FILE_MTYPE_DEFAULT", 50);
define("RTM_FILE_MTYPE_IMAGE", 40);
define("RTM_FILE_MTYPE_AUDIO", 41);
define("RTM_FILE_MTYPE_VIDEO", 42);

define("DELETE_MSG_TYPE_P2P", 1);
define("DELETE_MSG_TYPE_GROUP", 2);
define("DELETE_MSG_TYPE_ROOM", 3);
define("DELETE_MSG_TYPE_BROADCAST", 4);

class FileInfo {
    public $url;
    public $fileSize;
    public $surl;
    public $lang;
    public $duration;
    public $isRTMAudio;

    public function __construct() { $this->isRTMAudio = false; } 
}

class CommonMsg 
{
    public $id;
    public $mtype;
    public $mid;
    public $msg;
    public $attrs;
    public $mtime;
    public $fileInfo;

    public function __construct() { $this->fileInfo = NULL; } 
}

class GroupMsg extends CommonMsg  {
    public $from;
}

class RoomMsg extends CommonMsg {
    public $from;
}

class BroadcastMsg extends CommonMsg {
    public $from;
}

class P2PMsg extends CommonMsg {
    public $direction;
}

class RTMServerClient
{
    private $client = NULL;
    private $pid;
    private $secretKey;
    private $midSeq = 0;
    private $saltSeq = 0;
    private $incrementId = 0;

    function __construct($pid, $secretKey, $endpoint, $timeout = 5000, $autoReconnect = true)
    {
        $arr = explode(':', $endpoint);
        $this->pid = $pid;
        $this->secretKey = $secretKey;
        $this->client = new TCPClient($arr[0], $arr[1], $timeout, $autoReconnect);
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
        $milliseconds = round(microtime(true) * 1000);
        $this->incrementId++;
        if ($this->incrementId < 0)
            $this->incrementId = 0;
        return ($milliseconds << 22) | (crc32(uniqid()) << 12) | (mt_rand(100000, 999999) << 10) | $this->incrementId;
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
    
    public function sendCmd($from, $to, $msg, $attrs)
    {
        return $this->sendMessage($from, $to, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendChats($from, $tos, $msg, $attrs)
    {
        return $this->sendMessages($from, $tos, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendCmds($from, $tos, $msg, $attrs)
    {
        return $this->sendMessages($from, $tos, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendGroupChat($from, $gid, $msg, $attrs)
    {
        return $this->sendGroupMessage($from, $gid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendGroupCmd($from, $gid, $msg, $attrs)
    {
        return $this->sendGroupMessage($from, $gid, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function sendRoomChat($from, $rid, $msg, $attrs)
    {
        return $this->sendRoomMessage($from, $rid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendRoomCmd($from, $rid, $msg, $attrs)
    {
        return $this->sendRoomMessage($from, $rid, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function broadcastChat($from, $msg, $attrs)
    {
        return $this->broadcastMessage($from, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function broadcastCmd($from, $msg, $attrs)
    {
        return $this->broadcastMessage($from, RTM_CMD_MTYPE, $msg, $attrs); 
    }
    
    public function getP2PChat($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getP2PMessage($uid, $ouid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_CMD_MTYPE, RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO));
    }
    
    public function getGroupChat($uid, $gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getGroupMessage($uid, $gid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_CMD_MTYPE, RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO));
    }
    
    public function getRoomChat($uid, $rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getRoomMessage($uid, $rid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_CMD_MTYPE, RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO));
    }
    
    public function getBroadcastChat($uid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)
    {
        return $this->getBroadcastMessage($uid, $num, $desc, $begin, $end, $lastId, array(RTM_CHAT_MTYPE, RTM_CMD_MTYPE, RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO)); 
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
    
    public function translate($text, $dst, $src = '', $type = 'chat', $profanity = 'off', $uid = NULL) {
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

    public function speech2Text($audio, $type, $lang, $codec = "", $srate = 0, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'speech2text', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'audio' => $audio,
            'type' => $type,
            'lang' => $lang
        );
        if ($codec != "")
            $params['codec'] = $codec;
        if ($srate > 0)
            $params['srate'] = $srate;
        if ($uid !== NULL)
            $params['uid'] = $uid;
        $response = $this->client->sendQuest("speech2text", $params);
        return [
            'text' => $response['text'],
            'lang' => $response['lang']
        ];
    }

    public function textCheck($text, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'tcheck', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'text' => $text
        );
        if ($uid !== NULL)
            $params['uid'] = $uid;
        $response = $this->client->sendQuest("tcheck", $params);
        $result = array(
            'result' => $response['result']
        );
        if (isset($response['text']))
            $result['text'] = $response['text'];
        if (isset($response['tags']))
            $result['tags'] = $response['tags'];
        if (isset($response['wlist']))
            $result['wlist'] = $response['wlist'];
        return $result;
    }

    public function imageCheck($image, $type, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'icheck', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'image' => $image,
            'type' => $type
        );
        if ($uid !== NULL)
            $params['uid'] = $uid;
        $response = $this->client->sendQuest("icheck", $params);
        $result = array(
            'result' => $response['result']
        );
        if (isset($response['tags']))
            $result['tags'] = $response['tags'];
        return $result;
    }

    public function audioCheck($audio, $type, $lang, $codec = "", $srate = 0, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'acheck', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'audio' => $audio,
            'type' => $type,
            'lang' => $lang
        );
        if ($codec !== "")
            $params['codec'] = $codec;
        if ($srate > 0)
            $params['srate'] = $srate;
        if ($uid !== NULL)
            $params['uid'] = $uid;
        $response = $this->client->sendQuest("acheck", $params);
        $result = array(
            'result' => $response['result']
        );
        if (isset($response['tags']))
            $result['tags'] = $response['tags'];
        return $result;
    }

    public function videoCheck($video, $type, $videoName, $uid = NULL) {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'vcheck', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'video' => $video,
            'type' => $type,
            'videoName' => $videoName
        );
        if ($uid !== NULL)
            $params['uid'] = $uid;
        $response = $this->client->sendQuest("vcheck", $params);
        $result = array(
            'result' => $response['result']
        );
        if (isset($response['tags']))
            $result['tags'] = $response['tags'];
        return $result;
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

    public function addBlacks($uid, $blacks)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("addblacks", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addblacks', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'blacks' => $blacks
        ));
    }

    public function delBlacks($uid, $blacks)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("delblacks", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'delblacks', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'blacks' => $blacks
        ));
    }

    public function getBlacks($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getblacks", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getblacks', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isBlack($uid, $buid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isblack', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'buid' => $buid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isBlacks($uid, $buids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("isblacks", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'isblacks', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'buids' => $buids
        ));
        return isset($res['buids']) ? $res['buids'] : array();
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
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addgroupban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'btime' => $btime
        );
        if ($gid != NULL)
            $params['gid'] = $gid;
        $this->client->sendQuest("addgroupban", $params);
    }

    public function removeGroupBan($gid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removegroupban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        );
        if ($gid != NULL)
            $params['gid'] = $gid;
        $this->client->sendQuest("removegroupban", $params);
    }

    public function addRoomBan($rid, $uid, $btime)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addroomban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid,
            'btime' => $btime
        );
        if ($rid != NULL)
            $params['rid'] = $rid;
        $this->client->sendQuest("addroomban", $params);
    }

    public function removeRoomBan($rid, $uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removeroomban', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        );
        if ($rid != NULL)
            $params['rid'] = $rid;
        $this->client->sendQuest("removeroomban", $params);
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

		return array('oinfo' => isset($res['oinfo']) ? $res['oinfo'] : NULL, 'pinfo' => isset($res['pinfo']) ? $res['pinfo'] : NULL);
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
        return array('oinfo' => isset($res['oinfo']) ? $res['oinfo'] : NULL, 'pinfo' => isset($res['pinfo']) ? $res['pinfo'] : NULL);
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
        return array('oinfo' => isset($res['oinfo']) ? $res['oinfo'] : NULL, 'pinfo' => isset($res['pinfo']) ? $res['pinfo'] : NULL);
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

    public function addUserRooms($uid, $rids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("adduserrooms", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'adduserrooms', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rids' => $rids,
            'uid' => $uid
        ));
    }

    public function deleteUserRooms($uid, $rids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest("deleteuserrooms", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'deleteuserrooms', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rids' => $rids,
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

    public function fileToken($from, $cmd, $to)
    {
        if (!in_array($cmd, array('sendfile', 'sendfiles', 'sendroomfile', 'sendgroupfile', 'broadcastfile')))
			throw new \Exception('cmd not support');
        
		$salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $param = array(
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
			'from' => $from,
            'cmd' => $cmd
        );
		if ($cmd == 'sendfile')
			$param['to'] = $to;
		if ($cmd == 'sendfiles')
			$param['tos'] = $to;
		if ($cmd == 'sendroomfile')
			$param['rid'] = $to;
		if ($cmd == 'sendgroupfile')
			$param['gid'] = $to;
		
        $answer = $this->client->sendQuest('filetoken', $param);
		return $answer;
    }

    public function sendFile($from, $to, $mtype, $file, $attrs = array())
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
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $mid = $this->generateMessageId();

        $fileAttrs = array(
                "rtm" => array(
                    'sign' => $sign,
                    'ext' => $ext,
                    'filename' => $fileName
                )
            );
        if (!empty($attrs))
            $fileAttrs["custom"] = $attrs;

        $response = $fileClient->sendQuest("sendfile", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'to' => $to,
            'mid' => $mid,
            'file' => $content,
            'attrs' => json_encode($fileAttrs)
        ]);
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }
    
    public function sendFiles($from, $tos, $mtype, $file, $attrs = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
            'cmd' => 'sendfiles',
            'from' => $from,
            'tos' => $tos
        ]);

        $token = $answer["token"];
        $endpoint = $answer["endpoint"];
        $ipport = explode(":", $endpoint);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $mid = $this->generateMessageId();

        $fileAttrs = array(
            "rtm" => array(
                'sign' => $sign,
                'ext' => $ext,
                'filename' => $fileName
            )
        );
        if (!empty($attrs))
            $fileAttrs["custom"] = $attrs;

        $response = $fileClient->sendQuest("sendfiles", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'tos' => $tos,
            'mid' => $mid,
            'file' => $content,
            'attrs' => json_encode($fileAttrs)
        ]);
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }
    
    public function sendRoomFile($from, $rid, $mtype, $file, $attrs = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
            'cmd' => 'sendroomfile',
            'from' => $from,
            'rid' => $rid
        ]);

        $token = $answer["token"];
        $endpoint = $answer["endpoint"];
        $ipport = explode(":", $endpoint);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $mid = $this->generateMessageId();

        $fileAttrs = array(
            "rtm" => array(
                'sign' => $sign,
                'ext' => $ext,
                'filename' => $fileName
            )
        );
        if (!empty($attrs))
            $fileAttrs["custom"] = $attrs;

        $response = $fileClient->sendQuest("sendroomfile", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'rid' => $rid,
            'mid' => $mid,
            'file' => $content,
            'attrs' => json_encode($fileAttrs)
        ]);
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }
    
    public function sendGroupFile($from, $gid, $mtype, $file, $attrs = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
            'cmd' => 'sendgroupfile',
            'from' => $from,
            'gid' => $gid
        ]);

        $token = $answer["token"];
        $endpoint = $answer["endpoint"];
        $ipport = explode(":", $endpoint);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $mid = $this->generateMessageId();

        $fileAttrs = array(
            "rtm" => array(
                'sign' => $sign,
                'ext' => $ext,
                'filename' => $fileName
            )
        );
        if (!empty($attrs))
            $fileAttrs["custom"] = $attrs;

        $response = $fileClient->sendQuest("sendgroupfile", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'gid' => $gid,
            'mid' => $mid,
            'file' => $content,
            'attrs' => json_encode($fileAttrs)
        ]);
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }
    
    public function broadcastFile($from, $mtype, $file, $attrs = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $sign = $this->generateSignature($salt, 'filetoken', $ts);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
            'ts' => $ts,
            'cmd' => 'broadcastfile',
            'from' => $from
        ]);

        $token = $answer["token"];
        $endpoint = $answer["endpoint"];
        $ipport = explode(":", $endpoint);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $content = file_get_contents($file);
        $sign = md5(md5($content) . ":" . $token);
        $fileClient = new TCPClient($ipport[0], $ipport[1]);
        $mid = $this->generateMessageId();

        $fileAttrs = array(
            "rtm" => array(
                'sign' => $sign,
                'ext' => $ext,
                'filename' => $fileName
            )
        );
        if (!empty($attrs))
            $fileAttrs["custom"] = $attrs;

        $response = $fileClient->sendQuest("broadcastfile", [
            'pid' => $this->pid,
            'token' => $token,
            'mtype' => $mtype,
            'from' => $from,
            'mid' => $mid,
            'file' => $content,
            'attrs' => json_encode($fileAttrs)
        ]);
        return [
            'mtime' => $response['mtime'],
            'mid' => $mid,
        ];
    }

    private function buildFileInfo($mtype, $msg, &$attrs) {
        $fileInfo = NULL;
        if (in_array($mtype, array(RTM_FILE_MTYPE_DEFAULT, RTM_FILE_MTYPE_IMAGE, RTM_FILE_MTYPE_AUDIO, RTM_FILE_MTYPE_VIDEO))) {
            $fileMsgArray = json_decode($msg, true);
            if ($fileMsgArray) {
                $fileInfo = new FileInfo();
                $fileInfo->url = isset($fileMsgArray["url"]) ? $fileMsgArray["url"] : NULL;
                $fileInfo->fileSize = isset($fileMsgArray["size"]) ? $fileMsgArray["size"] : 0;
                if ($mtype == RTM_FILE_MTYPE_IMAGE)
                    $fileInfo->surl = isset($fileMsgArray["surl"]) ? $fileMsgArray["surl"] : NULL;
            }
            $fileAttrsArray = json_decode($attrs, true);
            if ($fileAttrsArray) {
                if (isset($fileAttrsArray["rtm"]) && isset($fileAttrsArray["rtm"]["type"]) && $fileAttrsArray["rtm"]["type"] == "audiomsg") {
                    $fileInfo->lang = isset($fileAttrsArray["rtm"]["lang"]) ? $fileAttrsArray["rtm"]["lang"] : NULL;
                    $fileInfo->duration = isset($fileAttrsArray["rtm"]["duration"]) ? $fileAttrsArray["rtm"]["lang"] : 0;
                    $fileInfo->isRTMAudio = true;
                }
                if (isset($fileAttrsArray["custom"]))
                    $attrs = json_encode($fileAttrsArray["custom"]); 
            }
        }
        return $fileInfo;
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
            $msgStruct->direction = $v[1];
            $msgStruct->mtype = (int)$v[2];
            $msgStruct->mid = (int)$v[3];
            $msgStruct->msg = $v[5];
            $msgStruct->attrs = $v[6];
            $msgStruct->mtime = (int)$v[7];
            $msgStruct->fileInfo = $this->buildFileInfo($msgStruct->mtype, $msgStruct->msg, $msgStruct->attrs);
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
    public function getGroupMessage($uid, $gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
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
            'mtypes' => $mtypes,
            'uid' => $uid
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
            $msgStruct->fileInfo = $this->buildFileInfo($msgStruct->mtype, $msgStruct->msg, $msgStruct->attrs);
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
    public function getRoomMessage($uid, $rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
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
            'mtypes' => $mtypes,
            'uid' => $uid
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
            $msgStruct->fileInfo = $this->buildFileInfo($msgStruct->mtype, $msgStruct->msg, $msgStruct->attrs);
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
    public function getBroadcastMessage($uid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getbroadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getbroadcastmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastId,
            'mtypes' => $mtypes,
            'uid' => $uid
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
            $msgStruct->fileInfo = $this->buildFileInfo($msgStruct->mtype, $msgStruct->msg, $msgStruct->attrs);
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
        $result = $this->client->sendQuest('getmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getmsg', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$xid,
            'type' => $type 
        ]);

        if (empty($result))
            return $result;

        $result['fileInfo'] = $this->buildFileInfo($result['mtype'], $result['msg'], $result['attrs']);

        return $result;
    }
    
    public function getChat($mid, $from, $xid, $type)
    {
        return $this->getMessage($mid, $from, $xid, $type);
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

    public function getRoomMembers($rid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getroommembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getroommembers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function getRoomCount($rids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getroomcount", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getroomcount', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rids' => $rids
        ));
        $count = array();
        if (isset($res['cn']) && is_array($res['cn'])) {
            foreach ($res['cn'] as $k => $v)
                $count[intval($k)] = $v;
        }
        return $count;
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
    public function kickOut($uid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('kickout', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'kickout', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid
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
            'devicetoken' => $deviceToken
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
            'devicetoken' => $deviceToken
        ]);
    }

    public function addDevicePushOption($uid, $type, $xid, $mtypes = null)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addoption', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'type' => $type,
            'xid' => (int)$xid
        );
        if ($mtypes != null)
            $params['mtypes'] = $mtypes;
        $this->client->sendQuest('addoption', $params);
    }

    public function removeDevicePushOption($uid, $type, $xid, $mtypes = null)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removeoption', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'type' => $type,
            'xid' => (int)$xid
        );
        if ($mtypes != null)
            $params['mtypes'] = $mtypes;
        $this->client->sendQuest('removeoption', $params);
    }

    public function getDevicePushOption($uid) 
	{
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getoption", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getoption', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => $uid
        ));
        $p2p = array();
        $group = array();
        if (isset($res['p2p']) && is_array($res['p2p'])) {
            foreach ($res['p2p'] as $k => $v)
                $p2p[intval($k)] = $v;
        }
        if (isset($res['group']) && is_array($res['group'])) {
            foreach ($res['group'] as $k => $v)
                $group[intval($k)] = $v;
        }
        return array('p2p' => $p2p, 'group' => $group);
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

    public function getMessageNum($type, $xid, $mtypes = NULL, $begin = NULL, $end = NULL)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $params = array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getmsgnum', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'type' => $type,
            'xid' => $xid
        );
        if ($mtypes != NULL)
            $params['mtypes'] = $mtypes;
        if ($begin != NULL)
            $params['begin'] = $begin;
        if ($end != NULL)
            $params['end'] = $end;

        $res = $this->client->sendQuest("getmsgnum", $params);
        return array(
            'sender' => isset($res['sender']) ? intval($res['sender']) : 0,
            'num' => isset($res['num']) ? intval($res['num']) : 0
        );
    }

    public function addSessions($uid, $uids)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('addsessions', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'addsessions', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'uids' => $uids,
        ]);
    }

    public function removeSessions($uid, $uids, $oneway = false)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('removesessions', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'removesessions', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'uids' => $uids,
            'oneway' => $oneway,
        ]);
    }

    public function inviteUserIntoRTCRoom($rid, $toUids, $fromUid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('inviteUserIntoRTCRoom', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'inviteUserIntoRTCRoom', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => (int)$rid,
            'toUids' => $toUids,
            'fromUid' => $fromUid
        ]);
    }

    public function closeRTCRoom($rid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('closeRTCRoom', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'closeRTCRoom', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => (int)$rid
        ]);
    }

    public function kickoutFromRTCRoom($uid, $rid, $fromUid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('kickoutFromRTCRoom', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'kickoutFromRTCRoom', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'uid' => (int)$uid,
            'rid' => (int)$rid,
            'fromUid' => (int)$fromUid
        ]);
    }

    public function getRTCRoomList()
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getRTCRoomList", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getRTCRoomList', $ts),
            'salt' => $salt,
            'ts' => $ts
        ));
        return isset($res['rids']) ? $res['rids'] : array();
    }

    public function getRTCRoomMembers($rid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getRTCRoomMembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getRTCRoomMembers', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid
        ));
        $result = array();
        $result['uids'] = isset($res['uids']) ? $res['uids'] : array();
        $result['administrators'] = isset($res['administrators']) ? $res['administrators'] : array();
        $result['owner'] = isset($res['owner']) ? $res['owner'] : 0;
        return $result;
    }

    public function getRTCRoomMemberCount($rid)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $res = $this->client->sendQuest("getRTCRoomMemberCount", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'getRTCRoomMemberCount', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => $rid
        ));
        return isset($res['count']) ? $res['count'] : 0;
    }

    public function setRTCRoomMicStatus($rid, $status)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('setRTCRoomMicStatus', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'setRTCRoomMicStatus', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => (int)$rid,
            'status' => $status
        ]);
    }

    public function pullIntoRTCRoom($rid, $toUids, $type)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('pullIntoRTCRoom', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'pullIntoRTCRoom', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => (int)$rid,
            'toUids' => $toUids,
            'type' => $type
        ]);
    }

    public function adminCommand($rid, $uids, $command)
    {
        $salt = $this->generateSalt();
        $ts = time();
        $this->client->sendQuest('adminCommand', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt, 'adminCommand', $ts),
            'salt' => $salt,
            'ts' => $ts,
            'rid' => (int)$rid,
            'uids' => $uids,
            'command' => $command
        ]);
    }

}
