<?php 

namespace Fpnn;

class Header {

    private $magic;     // string
    private $version;   // uint8_t
    private $flag;      // uint8_t
    private $mtype;     // uint8_t
    private $ss;        // quest method size or answer status    // uint8_t
    private $psize;     // uint32_t

    public function __construct($magic, $version, $flag, $mtype, $ss, $psize)
    {
        $this->magic = $magic;
        $this->version = $version;
        $this->flag = $flag;
        $this->mtype = $mtype;
        $this->ss = $ss;
        $this->psize = $psize;
    }
    public function setVersion($version)        { $this->version = $version; }
    public function setFlag($flag)              { $this->flag |= $flag; }
    public function setMType($mtype)            { $this->mtype = $mtype; }
    public function setSS($ss)                  { $this->ss = $ss; }
    public function setPayloadSize($size)       { $this->psize = $size; }
    public function isTwoWay() { return $this->mtype == FPNN_PHP_MT_TWOWAY; }

    public function packHeader()
    {
        $ret = pack("A*CCCCV", $this->magic, $this->version, $this->flag, $this->mtype, $this->ss, $this->psize);
        return $ret;
    }
}
