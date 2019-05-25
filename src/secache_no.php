<?php
class secache_no{
	 function workat($file){ 
	 }
	 function fetch($key,&$return){
	 }
  function store($key,$value){
  }
   function status(&$curBytes,&$totalBytes){
        $totalBytes = $curBytes = 0;
        $hits = $miss = 0;
        $return[] = array('name'=>'缓存命中','value'=>$hits);
        $return[] = array('name'=>'缓存未命中','value'=>$miss);
        return $return;
    }
	
}
?>