# Data Api

* `dataGet($uid, $key)`: Get stored data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key
    * return:
      * val    
      
* `dataSet($uid, $key, $value)`: Store data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key    
    * `value`: **(string)** value  
    
* `dataDelete($uid, $key)`: Delete stored data
    * `uid`: **(long)** User ID
    * `key`: **(string)** key    