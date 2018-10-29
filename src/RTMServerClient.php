<?php

namespace highras\rtm;

use highras\fpnn\TCPClient;

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
        return (int)((int)(microtime(true) * 1000) . mt_rand(10000, 99999));
    }

    private function generateSalt()
    {
        return (int)(microtime(true) * 1000);
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

    public function setPushName($uid, $pushname)
    {
        $salt = $this->generateSalt();
        $this->client->sendQuest("setpushname", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid,
            'pushname' => $pushname
        ));
    }

    public function getPushName($uid)
    {
        $salt = $this->generateSalt();
        $res = $this->client->sendQuest("getpushname", array(
            'pid' => $this->pid,
            'sign' => $this->generateSignature($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['pushname']) ? $res['pushname'] : '';
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
    public function getP2PMessage($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
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
    public function getGroupMessage($gid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
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
    public function getRoomMessage($rid, $num, $desc, $begin = 0, $end = 0, $lastid = 0)
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
    public function getBroadcastMessage($num, $desc, $begin = 0, $end = 0, $lastid = 0)
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
}