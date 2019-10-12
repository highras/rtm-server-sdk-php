<?php

namespace highras\rtm;

use highras\fpnn\TCPClient;

define("RTM_CHAT_MTYPE", 30);

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

    private function generateSignature($salt)
    {
        return strtoupper(md5($this->pid . ':' . $this->secretKey . ':' . $salt));
    }

    public function sendMessage($from, $to, $mtype, $msg, $attrs)
    {
        $salt = $this->generateSalt();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
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
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendmsgs", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
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
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
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
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("sendroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
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
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("broadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
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
        return sendMessage($from, $to, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendChats($from, $tos, $msg, $attrs)
    {
        return sendMessages($from, $tos, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendGroupChat($from, $gid, $msg, $attrs)
    {
        return sendGroupMessage($from, $gid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function sendRoomChat($from, $rid, $msg, $attrs)
    {
        return sendRoomMessage($from, $rid, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function broadcastChat($from, $msg, $attrs)
    {
        return broadcastMessage($from, RTM_CHAT_MTYPE, $msg, $attrs); 
    }
    
    public function getP2PChat($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
    {
        return getP2PMessage($uid, $ouid, $num, $desc, $begin, $end, $lastid, array(RTM_CHAT_MTYPE));
    }

    public function getGroupChat($gid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
    {
        return getGroupMessage($gid, $num, $desc, $begin, $end, $lastid, array(RTM_CHAT_MTYPE));
    }

    public function getRoomChat($rid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
    {
        return getRoomMessage($rid, $num, $desc, $begin, $end, $lastid, array(RTM_CHAT_MTYPE));
    }

    public function getBroadcastChat($num, $desc, $begin = 0, $end = 0, $lastid = 0)
    {
        return getBroadcastMessage($num, $desc, $begin, $end, $lastid, array(RTM_CHAT_MTYPE)); 
    }

    public function deleteChat($mid, $from, $xid, $type) {
        return deleteMessage($mid, $from, $xid, $type);
    }

    public function translate($text, $dst, $src = '', $type = 'chat', $profanity = '') {
        $salt = $this->generateSalt();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("translate", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'text' => $text,
            'src' => $src,
            'dst' => $dst,
            'type' => $type,
            'profanity' => $profanity
        ));
        return [
            'source' => $response['source'],
            'target' => $response['target'],
            'sourceText' => $response['sourceText'],
            'targetText' => $response['targetText']
        ];
    }

    public function profanity($text, $action = '') {
        $salt = $this->generateSalt();
        $mid = $this->generateMessageId();
        $response = $this->client->sendQuest("profanity", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'text' => $text,
            'action' => $action
        ));
        return [
            'text' => $response['text']
        ];
    }

    public function addfriends($uid, $friends)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function delFriends($uid, $friends)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("delfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function getFriends($uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("getfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isFriend($uid, $fuid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isfriend", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'fuid' => $fuid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isFriends($uid, $fuids)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isfriends", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'fuids' => $fuids
        ));
        return isset($res['fuids']) ? $res['fuids'] : array();
    }

    public function addGroupMembers($gid, $uids)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroupMembers($gid, $uids)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("delgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroup($gid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("delgroup", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid
        ));
    }

    public function getGroupMembers($gid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("getgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isGroupMember($gid, $uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isgroupmember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function getUserGroups($uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("getusergroups", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['gids']) ? $res['gids'] : array();
    }

    public function getToken($uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("gettoken", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['token']) ? $res['token'] : '';
    }

    public function getOnlineUsers($uids)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("getonlineusers", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uids' => $uids
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function addGroupBan($gid, $uid, $btime)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addgroupban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeGroupBan($gid, $uid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("removegroupban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
    }

    public function addRoomBan($rid, $uid, $btime)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addroomban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeRoomBan($rid, $uid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("removeroomban", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function addProjectBlack($uid, $btime)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeProjectBlack($uid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("removeprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
    }

    public function isBanOfGroup($gid, $uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isbanofgroup", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

	public function setGroupInfo($gid, $oinfo = '', $pinfo = '')
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("setgroupinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'oinfo' => $oinfo,
			'pinfo' => $pinfo
        ));
    }

	public function getGroupInfo($gid) 
	{
		$salt = $this->generateSalt();
        $res = $this->client->sendQuest("getgroupinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

    public function isBanOfRoom($rid, $uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isbanofroom", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

	public function setRoomInfo($rid, $oinfo = '', $pinfo = '')
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("setroominfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'oinfo' => $oinfo,
			'pinfo' => $pinfo
        ));
    }

	public function getRoomInfo($rid) 
	{
		$salt = $this->generateSalt();
        $res = $this->client->sendQuest("getroominfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

    public function isProjectBlack($uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("isprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function setUserInfo($uid, $oinfo = '', $pinfo = '')
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("setuserinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'oinfo' => $oinfo,
			'pinfo' => $pinfo
        ));
    }

	public function getUserInfo($uid) 
	{
		$salt = $this->generateSalt();
        $res = $this->client->sendQuest("getuserinfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
		return array('oinfo' => $res['oinfo'], 'pinfo' => $res['pinfo']);
	}

	public function getUserOpenInfo($uids) 
	{
		$salt = $this->generateSalt();
        $res = $this->client->sendQuest("getuseropeninfo", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uids' => $uids
        ));
		return $res['info'];
	}
    
    public function addRoomMember($pid, $rid, $uid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("addroommember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function deleteRoomMember($pid, $rid, $uid)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("delroommember", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function sendFile($from, $to, $mtype, $file)
    {
        $salt = $this->generateSalt();
        $sign = $this->generateSignature($salt);
        $answer = $this->client->sendQuest('filetoken', [
            'pid' => $this->pid,
            'sign' => $sign,
            'salt' => $salt,
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
     * @param int $lastid
     * @return mixed
     * @throws \Exception
     */
    public function getP2PMessage($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastid = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest("getp2pmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'ouid' => $ouid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastid,
            'mtypes' => $mtypes
        ));
    }

    /**
     * @param int $gid
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastid
     * @return mixed
     * @throws \Exception
     */
    public function getGroupMessage($gid, $num, $desc, $begin = 0, $end = 0, $lastid = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest("getgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'gid' => $gid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastid,
            'mtypes' => $mtypes
        ));
    }

    /**
     * @param int $rid
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastid
     * @return mixed
     * @throws \Exception
     */
    public function getRoomMessage($rid, $num, $desc, $begin = 0, $end = 0, $lastid = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest("getroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'rid' => $rid,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastid,
            'mtypes' => $mtypes
        ));
    }

    /**
     * @param int $num
     * @param int $desc
     * @param int $begin
     * @param int $end
     * @param int $lastid
     * @return mixed
     * @throws \Exception
     */
    public function getBroadcastMessage($num, $desc, $begin = 0, $end = 0, $lastid = 0, $mtypes = array())
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest("getbroadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'num' => $num,
            'desc' => $desc,
            'begin' => $begin,
            'end' => $end,
            'lastid' => $lastid,
            'mtypes' => $mtypes
        ));
    }

    /**
     * @param integer $mid
     * @param integer $from
     * @param integer $xid
     * @param integer $type
     * @throws \Exception
     */
    public function deleteMessage($mid, $from, $xid, $type)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest('delmsg', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'mid' => (int)$mid,
            'from' => (int)$from,
            'xid' => (int)$xid,
            'type' => (int)$type
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
        $this->client->sendQuest('kickout', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => (int)$uid,
            'ce' => $ce
        ]);
    }

    public function dbGet($uid, $key) 
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest('dbget', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => (int)$uid,
            'key' => $key
        ]);
    }

    public function dbGets($uid, $keys)
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest('dbgets', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => (int)$uid,
            'keys' => $keys
        ]);
    }
    
    public function dbSet($uid, $key, $value)
    {
        $salt = $this->generateSalt();
        return $this->client->sendQuest('dbset', [
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => (int)$uid,
            'key' => $key,
            'val' => $value
        ]);
    }

}
