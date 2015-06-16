<?php

/**
* Add by Leon
* Define the cache functions.
*/

/**
* Usage
*
        $cacheLife = 3600; // Define the cache life time.
        $cacheDir = JPATH_CACHE . "/leon/";
        $queryStringEncode = base64_encode($this->_db->replacePrefix((string) $query));
        $queryMd5 = $queryStringEncode; 
        $cacheFile = $cacheDir . "query_" . md5($queryMd5) . ".cache";
        //echo $queryMd5 . "<br>";
        //echo $queryString . "<br>";
        
        
        $rowsCount = getCache($cacheFile, $cacheLife);
        
        if($rowsCount === false){
            $this->_db->setQuery($query);
            $this->_db->execute();

            $rowsCount = $this->_db->getNumRows();
            saveCache($cacheFile,$rowsCount);
        }
        return $rowsCount;
*/
function getCache($cacheFile,$cacheLife){
    // Get the cache
    if(file_exists($cacheFile) && ((time() - fileatime($cacheFile)) < $cacheLife)){
        // Cache exist and we will load the cache
        // ini_set('memory_limit','1024M');
        $content = include $cacheFile;
        $content = unserialize($content);
        return $content;
    }else{
        return false;
    }
}

function saveCache($cacheFile, $content){
    // Let's serialize the vars first
    $content = serialize($content);
    // Delete the old cache
    if(file_exists($cacheFile))unlink($cacheFile);
    // Save the cache
    $content = var_export($content, true);
    $content = '<?php return '.$content.'; ?>';
    $cacheSave = file_put_contents($cacheFile, $content);
}

function saveSqlLog($time,$sql, $link){
    // Save the sql logs
    
    // We don't have to save the sql query that runs more than 0.1 seconds
    if($time < 0.1) return true;
    
    $query = "INSERT INTO `log_sql` 
            (`id` ,`time` ,`runtime` ,`qs`) VALUES (
            NULL , NOW(), '$time', \"" . $sql . "\")";
    //$query = addslashes($query);
    //echo $query . "<hr>";
    $r = mysqli_query($link, $query);
    return $r;    
}

?>