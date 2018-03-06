<?php 

namespace Fpnn;

use Elliptic\EC;

define('FPNN_SOCKET_READ_RETRY', 10);

// flag constants
define('FPNN_PHP_FLAG_MSGPACK', 0x80);
define('FPNN_PHP_FLAG_JSON', 0x40);
define('FPNN_PHP_FLAG_ZIP', 0x20);
define('FPNN_PHP_FLAG_ENCRYPT', 0x10);

// package types
define('FPNN_PHP_PACK_MSGPACK', 0);
define('FPNN_PHP_PACK_JSON', 1);

// message types
define('FPNN_PHP_MT_ONEWAY', 0);
define('FPNN_PHP_MT_TWOWAY', 1);
define('FPNN_PHP_MT_ANSWER', 2);

define('FPNN_PHP_VERSION', 1);

define('FPNN_PHP_SEQNUM_ERROR', 201);
define('FPNN_PHP_STATUS_ERROR', 202);
define('FPNN_PHP_JSON_ENCODE_ERROR', 203);
define('FPNN_PHP_JSON_DECODE_ERROR', 204);
define('FPNN_PHP_TIMEOUT_ERROR', 205);
define('FPNN_PHP_MSGPACK_UNPACK_ERROR', 206);
define('FPNN_PHP_ENCRYPTOR_ERROR', 207);

class TCPClient {
    private $socket;
    private $ip;
    private $port;
    private $timeout;

    private $isEncryptor;
    private $canEncryptor;
    private $key;
    private $iv;
    private $strength;

    function __construct($ip, $port, $timeout=5000) // timeout in milliseconds
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->timeout = array('sec'=>floor($timeout/1000),'usec'=>($timeout%1000)*1000);
        $this->socket = null;
        $this->isEncryptor = false;
        $this->canEncryptor = true;
        $hasMsgpack = function_exists("msgpack_pack") && function_exists("msgpack_unpack");       // test msgpack api available
        if($hasMsgpack){
            $version = phpversion("msgpack");
            if($version == false || version_compare($version, "0.5.7", "<"))
                throw new \Exception("requires php msgpack version >= 0.5.7", FPNN_PHP_MSGPACK_UNPACK_ERROR);
        } else
            throw new \Exception("requires php msgpack extension", FPNN_PHP_MSGPACK_UNPACK_ERROR);
    }

    function __destruct() {
        if(!is_null($this->socket))
            socket_close($this->socket);
    }
    
    private function encrypt($buf, $isEncrypt) {
        $strength = 'AES-128-CFB';
        if ($this->strength == 256)
            $strength = 'AES-256-CFB';
        $return = '';
        if ($isEncrypt) {
            $return = openssl_encrypt($buf, $strength, $this->key, OPENSSL_RAW_DATA, $this->iv);
        } else {
	    $return = openssl_decrypt($buf, $strength, $this->key, OPENSSL_RAW_DATA, $this->iv);
        }
        return $return;
    }

    public function enableEncryptor($peerPubData, $curveName = 'secp256k1', $strength = 128) {
        if ($this->canEncryptor == false) {
            throw new \Exception("enableEncryptor can only be called once, and must before any sendQuest", FPNN_PHP_ENCRYPTOR_ERROR);
        }
        $curveName = in_array($curveName, array('secp256k1')) ? $curveName : 'secp256k1';
        $this->strength = ($strength == 128) ? $strength : 256;
        
        $ec = new EC($curveName); 
        $keyPair = $ec->genKeyPair();

        $peerPubKeyPair = $ec->keyFromPublic('04' . bin2hex($peerPubData), 'hex');

        $secret = hex2bin($keyPair->derive($peerPubKeyPair->getPublic())->toString(16));
        
        $this->iv = hex2bin(md5($secret));
        if ($this->strength == 128)
	        $this->key = substr($secret, 0, 16);
        else {
            if (strlen($secret) == 32)
                $this->key = $secret;
            else
                $this->key = hash('sha256', $secret, true);
        }
        $pubKey = $keyPair->getPublic();
        $sendPubKeyData = hex2bin($pubKey->getX()->toString(16)) . hex2bin($pubKey->getY()->toString(16));

        $this->isEncryptor = true;
        $this->canEncryptor = false;

        try { 
            $answer = $this->sendQuest("*key", array("publicKey" => $sendPubKeyData, "streamMode" => false, "bits" => $this->strength));
        } catch (\Exception $e) {
            throw new \Exception("enableEncryptor error: " . $e->getMessage(), FPNN_PHP_ENCRYPTOR_ERROR);  
        }
    }

    private function reconnectServer() {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            throw new \Exception(socket_strerror(socket_last_error()), socket_last_error());
        }
        if(false === socket_set_option($this->socket,SOL_SOCKET, SO_RCVTIMEO, $this->timeout))
            throw new \Exception(socket_strerror(socket_last_error()), socket_last_error());
        if(false === socket_set_option($this->socket,SOL_SOCKET, SO_SNDTIMEO, $this->timeout))
            throw new \Exception(socket_strerror(socket_last_error()), socket_last_error());
        if (@socket_connect($this->socket, $this->ip, $this->port) === false)
            throw new \Exception(socket_strerror(socket_last_error()), socket_last_error());
    }

    private function readBytes($len) {
        $nbytes = 0;
        $buf = "";
        for ($i = 0; $i < FPNN_SOCKET_READ_RETRY; $i++) {
            if (false === ($nbytes = socket_recv($this->socket, $buf, $len, MSG_WAITALL))) {
                $errno = socket_last_error();
                if ($errno == SOCKET_EINTR || $errno == SOCKET_EAGAIN)
                    continue;
                else
                    throw new \Exception(socket_strerror($errno), $errno);
            } else break;
        }
        if($nbytes < $len){
            throw new \Exception("socke_recv timeout", FPNN_PHP_TIMEOUT_ERROR);
        }
        return $buf;
    }

    public function sendQuest($method, array $params, $oneway=false) {
        $quest = new Quest($method, $params, $oneway);

        if(is_null($this->socket))
            $this->reconnectServer();

        $st = $quest->raw();

	if ($this->isEncryptor && $method != "*key") {
            $st = pack("VA*",strlen($st),  $this->encrypt($st, true));
        }

        $length = strlen($st);

        while ($length > 0) {
            $sent = socket_write($this->socket, $st, $length);
            if ($sent === false) {
                throw new \Exception(socket_strerror(socket_last_error()), socket_last_error());
            }
            if ($sent < $length) {
                // If not sent the entire message.
                // Get the part of the message that has not yet been sented as message
                $st = substr($st, $sent);
            }
            $length -= $sent;
        }

        if($oneway)
            return;                // one way methods has no response.

        $this->canEncryptor = false;

        // read server response

        $arr = array();
        if ($this->isEncryptor) {
            $buf = $this->readBytes(4);
            $arr = unpack("Vlen", $buf);
            $buf = $this->readBytes($arr['len']);
            $buf = $this->encrypt($buf, false);
            $arr = unpack("A4magic/Cversion/Cflag/Cmtype/Css/Vpsize/VseqNum/A*payload", $buf);
        } else {
            $buf = $this->readBytes(16); // header size + sequence number
            $arr = unpack("A4magic/Cversion/Cflag/Cmtype/Css/Vpsize/VseqNum", $buf);
        }

        if($arr["seqNum"] != $quest->getSeqNum()) {
            throw new \Exception("Server returned unmatched seqNum, quest seqNum: "
                .$quest->getSeqNum().", server returned seqNum: ".$arr["seqNum"], FPNN_PHP_SEQNUM_ERROR);
        }

        $payload = "";
        if ($this->isEncryptor)
	        $payload = $arr['payload'];
        else
            $payload = $this->readBytes($arr["psize"]);

        $anwser = msgpack_unpack($payload);
        if($anwser == NULL)
            throw new \Exception("msgpack unpack error while unpack data: ".$payload, FPNN_PHP_MSGPACK_UNPACK_ERROR);
        if($arr["ss"]){
            $e = new \Exception($anwser["ex"], $anwser["code"]);
            throw $e;
        }
        return $anwser;
    }
}
