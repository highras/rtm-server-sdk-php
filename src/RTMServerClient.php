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

    private function genMid()
    {
        return (time() << 32) + ($this->midSeq++ & 0xffffff);
    }

    private function genSalt()
    {
        return (time() << 32) + ($this->saltSeq++ & 0xffffff);
    }

    private function genSign($salt)
    {
        return strtoupper(md5($this->pid . ':' . $this->secretKey . ':' . $salt));
    }

    public function sendMessage($from, $to, $mtype, $msg, $attrs)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("sendmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'mtype' => $mtype,
            'from' => $from,
            'to' => $to,
            'mid' => $this->genMid(),
            'msg' => $msg,
            'attrs' => $attrs
        ));
    }

    public function sendMessages($from, $tos, $mtype, $msg, $attrs)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("sendmsgs", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'mtype' => $mtype,
            'from' => $from,
            'tos' => $tos,
            'mid' => $this->genMid(),
            'msg' => $msg,
            'attrs' => $attrs
        ));
    }

    public function sendGroupMessage($from, $gid, $mtype, $msg, $attrs)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("sendgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'mtype' => $mtype,
            'from' => $from,
            'gid' => $gid,
            'mid' => $this->genMid(),
            'msg' => $msg,
            'attrs' => $attrs
        ));
    }

    public function sendRoomMessage($from, $rid, $mtype, $msg, $attrs)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("sendroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'mtype' => $mtype,
            'from' => $from,
            'rid' => $rid,
            'mid' => $this->genMid(),
            'msg' => $msg,
            'attrs' => $attrs
        ));
    }

    public function broadcastMessage($from, $mtype, $msg, $attrs)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("broadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'mtype' => $mtype,
            'from' => $from,
            'mid' => $this->genMid(),
            'msg' => $msg,
            'attrs' => $attrs
        ));
    }

    public function addfriends($uid, $friends)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("addfriends", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function delFriends($uid, $friends)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("delfriends", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'friends' => $friends
        ));
    }

    public function getFriends($uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("getfriends", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isFriend($uid, $fuid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isfriend", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'fuid' => $fuid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isFriends($uid, $fuids)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isfriends", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'fuids' => $fuids
        ));
        return isset($res['fuids']) ? $res['fuids'] : array();
    }

    public function addGroupMembers($gid, $uids)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("addgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroupMembers($gid, $uids)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("delgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uids' => $uids
        ));
    }

    public function deleteGroup($gid)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("delgroup", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid
        ));
    }

    public function getGroupMembers($gid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("getgroupmembers", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function isGroupMember($gid, $uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isgroupmember", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function getUserGroups($uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("getusergroups", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['gids']) ? $res['gids'] : array();
    }

    public function getToken($uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("gettoken", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['token']) ? $res['token'] : '';
    }

    public function getOnlineUsers($uids)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("getonlineusers", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uids' => $uids
        ));
        return isset($res['uids']) ? $res['uids'] : array();
    }

    public function addGroupBan($gid, $uid, $btime)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("addgroupban", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeGroupBan($gid, $uid)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("removegroupban", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
    }

    public function addRoomBan($rid, $uid, $btime)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("addroomban", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeRoomBan($rid, $uid)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("removeroomban", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
    }

    public function addProjectBlack($uid, $btime)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("addprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'btime' => $btime
        ));
    }

    public function removeProjectBlack($uid)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("removeprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
    }

    public function isBanOfGroup($gid, $uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isbanofgroup", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isBanOfRoom($rid, $uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isbanofroom", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'rid' => $rid,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function isProjectBlack($uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("isprojectblack", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['ok']) && ($res['ok'] == true);
    }

    public function setPushName($uid, $pushname)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("setpushname", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'pushname' => $pushname
        ));
    }

    public function getPushName($uid)
    {
        $salt = $this->genSalt();
        $res = $this->client->sendQuest("getpushname", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
        return isset($res['pushname']) ? $res['pushname'] : '';
    }

    public function setGeo($uid, $lat, $lng)
    {
        $salt = $this->genSalt();
        $this->client->sendQuest("setgeo", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'lat' => $lat,
            'lng' => $lng
        ));
    }

    public function getGeo($uid)
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getgeo", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid
        ));
    }

    public function getGeos($uids)
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getgeos", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uids' => $uids
        ));
    }

    public function sendFile($from, $to, $mtype, $file)
    {
        $salt = $this->genSalt();
        $sign = $this->genSign($salt);
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
            'mid' => $this->genMid(),
            'file' => $content,
            'attrs' => json_encode([
                'sign' => $sign,
                'ext' => $ext,
            ])
        ]);
    }

    public function getGroupMessage($gid, $num, $desc, $page, $localid = 0, $mtypes = array())
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getgroupmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'gid' => $gid,
            'num' => $num,
            'desc' => $desc,
            'page' => $page,
            'localid' => $localid,
            'mtypes' => $mtypes,
        ));
    }

    public function getRoomMessage($rid, $num, $desc, $page, $localid = 0, $mtypes = array())
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getroommsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'rid' => $rid,
            'num' => $num,
            'desc' => $desc,
            'page' => $page,
            'localid' => $localid,
            'mtypes' => $mtypes,
        ));
    }

    public function getBroadcastMessage($num, $desc, $page, $localid = 0, $mtypes = array())
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getbroadcastmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'num' => $num,
            'desc' => $desc,
            'page' => $page,
            'localid' => $localid,
            'mtypes' => $mtypes,
        ));
    }

    public function getP2PMessage($uid, $ouid, $num, $direction, $desc, $page, $localid = 0, $mtypes = array())
    {
        $salt = $this->genSalt();
        return $this->client->sendQuest("getp2pmsg", array(
            'pid' => $this->pid,
            'sign' => $this->genSign($salt),
            'salt' => $salt,
            'uid' => $uid,
            'ouid' => $ouid,
            'num' => $num,
            'direction' => $direction,
            'desc' => $desc,
            'page' => $page,
            'localid' => $localid,
            'mtypes' => $mtypes,
        ));
    }

}

