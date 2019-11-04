# rtm-server-sdk-php

## Requirements

* PHP >= 5.4

* ext-mcrypt

* ext-gmp

* ext-msgpack

## Installations

The preferred way to install this sdk is through [composer](http://getcomposer.org/download/).

> Note: Check the [composer.json](https://github.com/highras/rtm-server-sdk-php/blob/master/composer.json) for this SDK's requirements and dependencies. 
Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

Either run

```
$ php composer.phar require highras/rtm "dev-master"
```

or add

```
"highras/rtm": "dev-master"
```

to the ```require``` section of your `composer.json` file.


## API

* `__construct($pid, $secretKey, $endpoint, $timeout = 5000)`: 构造RTMServerClient
    * `pid`: **(int)** 应用编号, RTM提供
    * `secretKey`: **(string)** 应用密钥, RTM提供
    * `endpoint`: **(string)** 地址, RTM提供
    * `timeout`: **(int)** 连接超时时间(ms)
    
* `enableEncryptor($peerPubData)`: 启用加密链接
    * `peerPubData`: **(string)**  证书内容
    
* `enableEncryptorByFile($file)`: 启用加密链接
    * `file`: **(string)**  证书文件路径
    
### 客户端发起命令

* `getToken($uid)`: 获取token
    * `uid`: **(long)** 用户 id
    * 返回：
      * string  
      
* `kickOut($uid, $ce = null)`: 踢掉用户
    * `uid`: **(long)** 用户 id
    * `ce`: **(strring)** 如果 ce不为空，则只踢掉其中一个链接，多用户登录情况      

* `addDevice($uid, $appType, $deviceToken)`: 添加设备
    * `uid`: **(long)** 用户 id
    * `appType`: **(strring)** app类型
    * `deviceToken`: **(strring)** deviceToken
    
* `removeDevice($uid, $deviceToken)`: 删除设备
    * `uid`: **(long)** 用户 id
    * `deviceToken`: **(strring)** deviceToken    
  
* `removeToken($uid)`: 删除token
    * `uid`: **(long)** 用户 id
    
### 消息相关   

* `sendMessage($from, $to, $mtype, $msg, $attrs)`: 发送P2P业务消息
    * `from`: **(long)** 发送方 id
    * `to`: **(long)** 接收方uid
    * `mtype`: **(byte)** 业务消息类型（请使用51-127，禁止使用50及以下的值）
    * `msg`: **(string)** 业务消息内容
    * `attrs`: **(string)** 业务消息附加信息, 没有可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 业务消息id
      
* `sendMessages($from, $tos, $mtype, $msg, $attrs)`: 发送多人业务消息
    * `from`: **(long)** 发送方 id
    * `tos`: **(long[])** 接收方uids
    * `mtype`: **(byte)** 业务消息类型（请使用51-127，禁止使用50及以下的值）
    * `msg`: **(string)** 业务消息内容
    * `attrs`: **(string)** 业务消息附加信息, 没有可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 业务消息id  
      
* `sendGroupMessage($from, $gid, $mtype, $msg, $attrs)`: 发送组消业务息
    * `from`: **(long)** 发送方 id
    * `gid`: **(long)** group id
    * `mtype`: **(byte)** 业务消息类型（请使用51-127，禁止使用50及以下的值）
    * `msg`: **(string)** 业务消息内容
    * `attrs`: **(string)** 业务消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 业务消息id  

* `sendRoomMessage($from, $rid, $mtype, $msg, $attrs)`: 发送房间业务消息
    * `from`: **(long)** 发送方 id
    * `rid`: **(long)** room id
    * `mtype`: **(byte)** 业务消息类型（请使用51-127，禁止使用50及以下的值）
    * `msg`: **(string)** 业务消息内容
    * `attrs`: **(string)** 业务消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 业务消息id  
      
* `broadcastMessage($from, $mtype, $msg, $attrs)`: 广播业务消息
    * `from`: **(long)** admin id
    * `mtype`: **(byte)** 业务消息类型（请使用51-127，禁止使用50及以下的值）
    * `msg`: **(string)** 业务消息内容
    * `attrs`: **(string)** 业务消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 业务消息id
      
* `getP2PMessage($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: 获取P2P业务消息历史
    * `uid`: **(long)** 获取和两个用户之间的历史业务消息
    * `ouid`: **(long)** 获取和两个用户之间的历史业务消息
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条业务消息的id, 第一次默认传`0`, 条件：`> or <`
    * `mtypes`: **([int])** mtype列表`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<P2PMsg> }`

* `getGroupMessage($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: 获取组消息历史
    * `gid`: **(long)** 组id
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条业务消息的id, 第一次默认传`0`, 条件：`> or <`
    * `mtypes`: **([int])** mtype列表`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<GroupMsg> }`
      
* `getRoomMessage($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: 获取房间业务消息历史
    * `rid`: **(long)** 房间id
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条业务消息的id, 第一次默认传`0`, 条件：`> or <`
    * `mtypes`: **([int])** mtype列表`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<RoomMsg> }`
      
* `getBroadcastMessage($num, $desc, $begin = 0, $end = 0, $lastId = 0, $mtypes = array())`: 获取广播业务消息历史
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条业务消息的id, 第一次默认传`0`, 条件：`> or <`
    * `mtypes`: **([int])** mtype列表`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<BroadcastMsg> }`
      
* `deleteP2PMessage($mid, $from, $xid)`: 删除P2P消息历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id

* `deleteGroupMessage($mid, $from, $xid)`: 删除组消息历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
    
* `deleteRoomMessage($mid, $from, $xid)`: 删除房间消息历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
    
* `deleteBroadcastMessage($mid, $from, $xid)`: 删除广播消息历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
      
### 聊天相关
      
* `sendChat($from, $to, $msg, $attrs)`: 发送聊天消息
    * `from`: **(long)** 发送方 id
    * `to`: **(long)** 接收方uid
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendChats($from, $tos, $msg, $attrs)`: 发送多人聊天消息
    * `from`: **(long)** 发送方 id
    * `tos`: **(long)** 接收方uids
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
* `sendCmd($from, $to, $msg, $attrs)`: 发送聊天相关系统命令
    * `from`: **(long)** 发送方 id
    * `to`: **(long)** 接收方uid
    * `msg`: **(string)** 发送聊天中的各种系统命令，比如：申请入群,拒绝申请入群,邀请入群,拒绝邀请入群,加好友,删除好友,其他等和聊天相关的命令
    * `attrs`: **(string)** 系统命令附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendCmds($from, $tos, $msg, $attrs)`: 发送多人发送聊天相关系统命令
    * `from`: **(long)** 发送方 id
    * `tos`: **(long)** 接收方uids
    * `msg`: **(string)** 发送聊天中的各种系统命令，比如：申请入群,拒绝申请入群,邀请入群,拒绝邀请入群,加好友,删除好友,其他等和聊天相关的命令
    * `attrs`: **(string)** 系统命令附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendGroupChat($from, $gid, $msg, $attrs)`: 发送组聊天消息
    * `from`: **(long)** 发送方 id
    * `gid`: **(long)** group id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendRoomChat($from, $rid, $msg, $attrs)`: 发送房间聊天消息
    * `from`: **(long)** 发送方 id
    * `rid`: **(long)** room id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
 * `broadcastChat($from, $msg, $attrs)`: 广播聊天消息
    * `from`: **(long)** admin id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
* `sendGroupCmd($from, $gid, $msg, $attrs)`: 发送组聊天控制命令
    * `from`: **(long)** 发送方 id
    * `gid`: **(long)** group id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendRoomCmd($from, $rid, $msg, $attrs)`: 发送房间聊天控制命令
    * `from`: **(long)** 发送方 id
    * `rid`: **(long)** room id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
 * `broadcastCmd($from, $msg, $attrs)`: 广播聊天控制命令
    * `from`: **(long)** admin id
    * `msg`: **(string)** 聊天消息内容，附加修饰信息不要放这里，方便后继的操作，比如翻译，敏感词过滤等等
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

 * `getP2PChat($uid, $ouid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: 获取P2P聊天历史
    * `uid`: **(long)** 获取和两个用户之间的历史聊天消息
    * `ouid`: **(long)** 获取和两个用户之间的历史聊天消息
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条聊天消息的id, 第一次默认传`0`, 条件：`> or <`
    * 返回:
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<P2PMsg> }`
      
 * `getGroupChat($gid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: 获取组聊天消息历史
    * `gid`: **(long)** 组id
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条聊天消息的id, 第一次默认传`0`, 条件：`> or <`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<GroupMsg> }`
      
 * `getRoomChat($rid, $num, $desc, $begin = 0, $end = 0, $lastId = 0)`: 获取房间聊天消息历史
    * `rid`: **(long)** 房间id
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条聊天消息的id, 第一次默认传`0`, 条件：`> or <`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<RoomMsg> }`
      
 * `getBroadcastChat($num, $desc, $begin = 0, $end = 0, $lastId = 0)`: 获取广播聊天消息历史
    * `desc`: **(bool)** `true`: 则从`end`的时间戳开始倒序翻页, `false`: 则从`begin`的时间戳顺序翻页
    * `num`: **(int)** 获取数量, **一次最多获取20条, 建议10条**
    * `begin`: **(long)** 开始时间戳, 毫秒, 默认`0`, 条件：`>=`
    * `end`: **(long)** 结束时间戳, 毫秒, 默认`0`, 条件：`<=`
    * `lastId`: **(long)** 最后一条聊天消息的id, 第一次默认传`0`, 条件：`> or <`
    * 返回：
      * `{ num:int16, lastId:int64, begin:int64, end:int64, msgs:list<BroadcastMsg> }`

* `deleteP2PChat($mid, $from, $xid)`: 删除P2P聊天历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id

* `deleteGroupChat($mid, $from, $xid)`: 删除组聊天历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
    
* `deleteRoomChat($mid, $from, $xid)`: 删除房间聊天历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
    
* `deleteBroadcastChat($mid, $from, $xid)`: 删除广播聊天历史
    * `mid`: **(long)**: 业务消息id
    * `from`: **(long)** 发布者id
    * `xid`: **(long)** rid/gid/to id
    
* `sendAudio($from, $to, $msg, $attrs)`: 发送语音聊天消息
    * `from`: **(long)** 发送方 id
    * `to`: **(long)** 接收方uid
    * `msg`: **(string)** 语音聊天内容
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendAudios($from, $tos, $msg, $attrs)`: 发送多人语音聊天消息
    * `from`: **(long)** 发送方 id
    * `tos`: **(long)** 接收方uids
    * `msg`: **(string)** 语音聊天内容
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendGroupAudio($from, $gid, $msg, $attrs)`: 发送组语音聊天消息
    * `from`: **(long)** 发送方 id
    * `gid`: **(long)** group id
    * `msg`: **(string)** 语音聊天内容
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id

* `sendRoomAudio($from, $rid, $msg, $attrs)`: 发送房间语音聊天消息
    * `from`: **(long)** 发送方 id
    * `rid`: **(long)** room id
    * `msg`: **(string)** 语音聊天内容
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
 * `broadcastAudio($from, $msg, $attrs)`: 广播语音聊天消息
    * `from`: **(long)** admin id
    * `msg`: **(string)** 语音聊天内容
    * `attrs`: **(string)** 聊天消息附加信息, 可传`""`
    * 返回：
      * `mtime`: 响应时间戳
      * `mid` : 聊天消息id
      
* `translate($text, $dst, $src = '', $type = 'chat', $profanity = '')`: 翻译
    * `text`: **(string)**: 内容
    * `dst`: **(string)** 目标语言类型
    * `src`: **(string)** 原始语言类型
    * `type`: **(string)** 可选值为chat或mail。如未指定，则默认使用'chat'
    * `profanity`: **(string)** 敏感语过滤。设置为以下3项之一: off, stop, censor
    * 返回：
      * sourceText: 原始聊天消息
      * source：原始聊天消息语言类型（经过翻译系统检测的）
      * targetText：翻译后的聊天消息
      * target：翻译后的语言类型

* `profanity($text, $action = '')`: 敏感词过滤
    * `text`: **(string)**: 内容
    * `action`: **(string)** stop: 返回错误，censor: 用星号(*)替换敏感词
    * 返回：
      * text: 过滤后的聊天消息
      
* `transcribe($audio, $action = '')`: 语音识别
    * `audio`: **(string)**: 语音内容
    * `action`: **(string)** stop: 返回错误，censor: 用星号(*)替换敏感词
    * 返回：
      * text: 过滤后的聊天消息
      
### 文件相关操作

* `sendFile($from, $to, $mtype, $file)`: 发送文件
    * `from`: **(long)** 发送方 id
    * `to`: **(long)** 接收方 id
    * `mtype`: **(byte)** 业务文件类型
    * `file` : 文件地址
    
    
### 用户操作相关    

* `getOnlineUsers($uids)`: 获取在线用户
    * `uids`: **(long)** 用户 id列表
    * 返回：
      * array(uid)   

* `addProjectBlack($uid, $btime)`: 添加项目黑名单     
    * `uid`: **(long)** 用户 id     
    * `btime`: **(int)** 禁言时长，从当前时间开始，以秒计算       
 
* `removeProjectBlack($uid)`: 取消项目黑名单   
    * `uid`: **(long)** 用户 id    
      
* `isProjectBlack($uid)`: 判断是否为项目黑名单
    * `uid`: **(long)** 用户 id
    * 返回：
      * bool        

* `setUserInfo($uid, $oinfo = NULL, $pinfo = NULL)`: 设置用户的公开信息或者私有信息
    * `uid`: **(long)** 用户 id        
    * `oinfo`: **(string)** 公开信息 
    * `pinfo`: **(string)** 私有信息   
 
* `getUserInfo($uid) `: 获取用户的公开信息或者私有信息
    * `uid`: **(long)** 用户 id
    * 返回：
      * oinfo
      * pinfo 
 
* `getUserOpenInfo($uids)`: 获取用户的公开信息
    * `uid`: **(long)** 用户 id
    * 返回：
      * oinfo      
      
### 好友操作相关

* `addFriends($uid, $friends)`: 添加好友
    * `uid`: **(long)** 用户 id
    * `friends`: **(long[])** 多个好友 id

* `delFriends($uid, $friends)`: 删除好友
    * `uid`: **(long)** 用户 id
    * `friends`: **(long[])** 多个好友 id
    
* `getFriends($uid)`: 获取好友
    * `uid`: **(long)** 用户 id
    * 返回：
      * array(uid)
      
* `isFriend($uid, $fuid)`: 判断是否为好友
    * `uid`: **(long)** 用户 id
    * `fuid`: **(long)** 另一个用户 id
    * 返回：
      * bool 
    
* `isFriends($uid, $fuids)`: 判断是否为好友(批量)
    * `uid`: **(long)** 用户 id
    * 返回：
      * array(uid)

### 群组操作相关

* `addGroupMembers($gid, $uids)`: 添加组成员
    * `gid`: **(long)** 组 id
    * `uids`: **(long)** 成员用户id

* `deleteGroupMembers($gid, $uids)`: 删除组成员
    * `gid`: **(long)** 组 id
    * `uids`: **(long)** 成员用户id
    
* `deleteGroup($gid)`: 删除组
    * `gid`: **(long)** 组 id

* `getGroupMembers($gid)`: 获取组成员
    * `gid`: **(long)** 组 id
    * 返回：
      * array(uid)

* `isGroupMember($gid, $uid)`: 判断是否为组成员
    * `gid`: **(long)** 组 id
    * `uid`: **(long)** 用户 id
    * 返回：
      * bool 
      
* `getUserGroups($uid)`: 获取组列表
    * `uid`: **(long)** 用户 id
    * 返回：
      * array(gid)           
      
* `addGroupBan($gid, $uid, $btime)`: 添加group禁言
    * `gid`: **(long)** 组 id       
    * `uid`: **(long)** 用户 id     
    * `btime`: **(int)** 禁言时长，从当前时间开始，以秒计算     
      
* `removeGroupBan($gid, $uid)`: 取消group禁言
    * `gid`: **(long)** 组 id       
    * `uid`: **(long)** 用户 id      
    
* `isBanOfGroup($gid, $uid)`: 判断是否被组禁言
    * `gid`: **(long)** 组 id
    * `uid`: **(long)** 用户 id
    * 返回：
      * bool  
      
* `setGroupInfo($gid, $oinfo = NULL, $pinfo = NULL)`: 设置群组的公开信息或者私有信息
    * `gid`: **(long)** 组 id        
    * `oinfo`: **(string)** 公开信息 
    * `pinfo`: **(string)** 私有信息   
 
* `getGroupInfo($gid)`: 获取群组的公开信息或者私有信息
    * `gid`: **(long)** 组 id
    * 返回：
      * oinfo
      * pinfo      
  
### 房间操作相关  
  
* `addRoomBan($rid, $uid, $btime)`: 添加room禁言
    * `rid`: **(long)** 房间 id       
    * `uid`: **(long)** 用户 id     
    * `btime`: **(int)** 禁言时长，从当前时间开始，以秒计算      
  
* `removeRoomBan($rid, $uid)`: 取消room禁言
    * `rid`: **(long)** 房间 id       
    * `uid`: **(long)** 用户 id        
 
* `isBanOfRoom($rid, $uid)`: 判断是否被房间禁言
    * `rid`: **(long)** 房间 id
    * `uid`: **(long)** 用户 id
    * 返回：
      * bool  
 
* `setRoomInfo($rid, $oinfo = NULL, $pinfo = NULL)`: 设置房间的公开信息或者私有信息
    * `rid`: **(long)** 房间 id        
    * `oinfo`: **(string)** 公开信息 
    * `pinfo`: **(string)** 私有信息   
 
* `getRoomInfo($rid) `: 获取房间的公开信息或者私有信息
    * `rid`: **(long)** 房间 id
    * 返回：
      * oinfo
      * pinfo 

* `addRoomMember($rid, $uid)`: 添加房间成员
    * `rid`: **(long)** 房间 id
    * `uid`: **(long)** 成员用户id 
    
* `deleteRoomMember($rid, $uid)`: 删除房间成员
    * `rid`: **(long)** 房间 id
    * `uid`: **(long)** 成员用户id 


### 数据存取相关

* `dataGet($uid, $key)`: 获取存储的数据信息
    * `uid`: **(long)** 用户 id
    * `key`: **(string)** key
    * 返回：
      * val    
      
* `dataSet($uid, $key, $value)`: 设置存储的数据信息
    * `uid`: **(long)** 用户 id
    * `key`: **(string)** key    
    * `value`: **(string)** value  
    
* `dataDelete($uid, $key)`: 删除存储的数据信息
    * `uid`: **(long)** 用户 id
    * `key`: **(string)** key    



    
    
 
 
