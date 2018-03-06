<?php 

namespace Fpnn;

class Quest {

    private $header;
    private $cTime;           // int64_t
    private $seqNum;         // uint32_t
    private $payload;        // binary data

    public function __construct($method, array $params, $oneway=false)
    {
        $this->header = new Header("FPNN", FPNN_PHP_VERSION, 0, 0, 0, 0);
        $this->header->setMType($oneway? FPNN_PHP_MT_ONEWAY : FPNN_PHP_MT_TWOWAY);
        $this->header->setSS(strlen($method));
        if(!$oneway) $this->setSeqNum($this->nextSeqNum());
        $this->setMethod($method);
        $payload="";
        $this->header->setFlag(FPNN_PHP_FLAG_MSGPACK);
        $payload = count($params) == 0 ? msgpack_pack(array("__void__" => 1)) : msgpack_pack($params);
        $this->setPayload($payload);
        $this->header->setPayloadSize(strlen($payload));
        $milliSecs = round(microtime(true) * 1000);
        $this->setCTime($milliSecs);
    }

	private function nextSeqNum()
    {
        static $nextSeq = 0;
        if ($nextSeq == 0)
            $nextSeq = intval(substr(microtime(true) * 1000, 5));
        if ($nextSeq >= 2147483647)
            $nextSeq = 1;
        return $nextSeq++;
    }
    public function getSeqNum()                 { return $this->seqNum; }
    public function setSeqNum($seqNum)          { $this->seqNum = $seqNum; }
    public function setPayload($payload)        {$this->payload = $payload; }
    public function setMethod($method)          { $this->method = $method; }
    public function setCTime($cTime)            { $this->cTime = $cTime; }

    public function raw()
    {
        $packet = "";
        $packet .= $this->header->packHeader();
        if($this->header->isTwoWay()){
            $packet .= pack("V", $this->seqNum);
        }
        $packet .= pack("A*A*", $this->method, $this->payload);
        return $packet;
    }
}
