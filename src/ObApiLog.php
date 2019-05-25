<?php 
class ObApiLog{
	var $client;	
	var $cache_db_config     = array('host'=>'localhost','dbname'=>'obapi','username'=>'','password'=>'','prefix'); //本地数据库参数
	var $keys_config = array(
			'id'=>array('name'=>'id','type'=>'INTEGER PRIMARY KEY'),
			'log_date'=>array('name'=>'log_date','type'=>'date'),
			'log_time'=>array('name'=>'log_time','type'=>'time'),
			'request_id'=>array('name'=>'request_id','type'=>'char(15)'),
			'api_server'=>array('name'=>'api_server','type'=>'char(50)'),
			'api_type'=>array('name'=>'api_type','type'=>'char(20)'),
			'api_key'=>array('name'=>'api_key','type'=>'char(32)'),
			'api_secret'=>array('name'=>'api_secret','type'=>'char(32)'),
			'api_lang'=>array('name'=>'api_lang','type'=>'char(10)'),
			'api_name'=>array('name'=>'api_name','type'=>'char(32)'),
			'api_item'=>array('name'=>'api_item','type'=>'char(32)'),
			'api_request'=>array('name'=>'api_request','type'=>'char(255)'),
			'server_ip'=>array('name'=>'服务器IP','type'=>'char(15)'),
			'guest_ip'=>array('name'=>'访客ID','type'=>'char(15)'),
			'guest_host'=>array('name'=>'guest_host','type'=>'char(100)'),
			'guest_url'=>array('name'=>'guest_url','type'=>'char(255)'),
			'guest_post'=>array('name'=>'guest_post','type'=>'char(100)'),
			'guest_agent'=>array('name'=>'guest_agent','type'=>'char(255)'),
			'api_result'=>array('name'=>'返回结果','type'=>'char(255)'),
			'use_time'=>array('name'=>'耗时','type'=>'float'),
			'error'=>array('name'=>'错误','type'=>'char(100)'),
			'cache'=>array('name'=>'缓存','type'=>'BOOLEAN'),
		 );


	public function __construct($client) {
		$this->client = &$client;
	}

	//显示日志
	public function view(){
		$this->init();

		$keys_name = array();
		foreach($this->keys_config as $k=>$v){
			$keys_name[$k]=$v['name'];
		}
		$keys = array_keys($keys_name);
		if(isset($_GET['key'])){
			// var_dump($_GET['key']);
			setcookie('custom_keys', implode(',',$_GET['key']));
			header('Location: ?debug=view_log');
			exit();
		}

		$custom_keys= isset($_COOKIE['custom_keys']) ? $_COOKIE['custom_keys'] : implode(',',$keys);
		$where = 'WHERE 1';
		$params=array();
		foreach ($keys as $v) {
			$vv = isset($_GET[$v])?$_GET[$v]:'';
			if($vv){
				$where .=' AND '.$v.' LIKE :'.$v.'';
				$params[$v] = '%'.$vv.'%'; 
			}
		}

		$logs = $this->log_db_dbh->getRows("SELECT ".implode(',',$keys)." FROM api_item_log $where order by id desc  limit 100",$params);
	

		//$log_key  = array_keys($logs[0]);
		

		
		foreach($logs as $k=>$v){
			foreach($v as $kk=>$vv){
				if($kk=='api_key')$logs[$k][$kk]=substr($vv,0,1).str_pad('*',strlen($vv),'*').substr($vv,-1);
				if($kk=='api_secret')$logs[$k][$kk]=substr($vv,0,1).str_pad('*',strlen($vv),'*').substr($vv,-1);
				if($kk=='api_request'){
				
			
					$logs[$k][$kk]= str_replace($v['api_key'],'**API_KEY**',$logs[$k][$kk]);
					$logs[$k][$kk]= str_replace($v['api_secret'],'**API_SECRET**',$logs[$k][$kk]);
					$logs[$k][$kk]= preg_replace('@&sign=[\w\d]+&@isU','**SIGN**&',$logs[$k][$kk]);

					//&sign=765F709E5AC761B4DDC1054E6A6DF99D

				}

				
				if(!in_array($kk,explode(',',$custom_keys))){
					
					unset($logs[$k][$kk]);
				}
			}
		}

		$log_key_search=array();
		foreach ($keys as $v) {
			if(!in_array($v,explode(',',$custom_keys))) continue;
			$vv = isset($_GET[$v])?$_GET[$v]:'';
			$log_key_search[$v]='<input name="'.$v.'" value="'.$vv.'" />';
		}
		array_unshift($logs, $log_key_search);

		echo '
		<!DOCTYPE html>
<html lang="en">
	<head>
	<style>
#grid_custom_ul li{
	width:30%;
	float:left
}
.item-list {
    margin: 0;
    padding: 0;
    list-style: none;
}
li.item-default {
    border-left-color: #abbac3;
    list-style: none;
    padding: 9px;
    margin-top: -1px;
    line-height:1em;
}
li[class*="item-"] {
    border: 1px solid #DDD;
        border-left-color: rgb(221, 221, 221);
        border-left-width: 1px;
    border-left-width: 3px;
}
.clearfix{overflow:hidden;_zoom:1;}
table.table0 th{font-bold: 700;color:blue; padding:10px 5px;background:#ccc} table.table0 td{padding:10px 5px}
.data-error{color: red}
</style>

<script>
function grid_custom_save(){
	// var post = \$(\'#grid_custom form\').serialize();
	// \$.post(\'?go=\'+objgo+\'&do=grid_custom\',post,function(d,s){
	// 	if(d==\'OK\'){
	// 		\$(\'#filter\').click();
	// 	}else{
			
	// 	}
	// });
}

function grid_custom(){
	
	dn = '.json_encode($keys_name).';
	cd = ",'.$custom_keys.',";
	var html = \'<form action="?"><input type="reset"><input type="submit"><input type="hidden" name="debug" value="view_log"><ul class="item-list ui-sortable" id="grid_custom_ul">	\';
	  
		// html += \'<tbody>\';
		for(i in dn){
			
				html += \'<li class="item-default clearfix">\';
				html += \'<label class="inline"><input type="checkbox" name="key[]" value="\'+i+\'" class="ace" \'+(cd.indexOf(","+i+",")!=-1?\'checked\':\'\')+\'><span class="lbl"></span>\'+i+\' ( \'+dn[i]+\')</label>\';
				
				html += \'</li>\';
			
		}
		
		
		html += \'</ul></form>\';	
	 document.querySelector("#grid_custom .panel-body .content").innerHTML = html
	
	 document.querySelector("#grid_custom").style.display="";
	}

</script>

</head>
<body>
<div class="panel panel-default clearfix" id="grid_custom" style="display:none">
  <div class="panel-heading">定制显示列</div>
  <div class="panel-body">
    <div class="content"></div>
    <div><button class="btn btn-info" onclick="grid_custom_save()" type="button"><i class="icon-cog"></i><span class="iconbutton">保存</span></button></div>
  </div>
</div>
<script>
// grid_custom();
</script>
<hr class="clearfix" />
';
		echo '<form action="?"><button class="btn btn-xs"  title="定制显示列" onclick="grid_custom()" type="button">定制显示列</button><input type="reset"><input type="submit" value="搜索"><input type="hidden" name="debug" value="view_log">';
		
		echo $this->client->helper_obj->outtable($logs);
		echo '</form>';
	
		exit();

	}
	//写入日志
	public function write($log){
		$this->init();
		$ret = $this->log_db_dbh->insertRow('api_item_log',$log);

	}
	public function set_db_config($config) {
		$this->log_db_config = $config;

	}
	public function init(){


		static $init_db = 0;
		if($init_db)return ;


		// $config = array(
		// 	'host'=>'localhost',
		// 	'dbname'=>'svn_otao_hitao',
		// 	'username'=>'root',
		// 	'password'=>'',
		// );
		// $this->log_db_config = 	$config;


		$dbh = "mysql:host=".$this->log_db_config['host'].";dbname=".$this->log_db_config['dbname']."";

		// $dbh = "sqlite:".$this->client->log_dir."/sqlite3_api_log.db";
		$this->log_db_dbh = new ObApiDB($dbh,$this->log_db_config['username'],$this->log_db_config['password']);
 		$this->log_db_dbh->setCharset('UTF8');
 		

 		$tables = $this->log_db_dbh->getRows("show tables like 'api_item_log';");
 		
 		//$tables = $this->log_db_dbh->getRows("SELECT * FROM sqlite_master WHERE sql NOTNULL AND name='api_item_log';");//sqlite
 		$keys_type=array();
 		foreach($this->keys_config as $k=>$v){
 			$keys_type[] = $k.' '.$v['type'];
 		}
 		if(!$tables){

			$d = $this->log_db_dbh->exec('CREATE TABLE api_item_log ( 
			'.implode(',',$keys_type).'
			);');
			
			$this->log_db_dbh->exec("ALTER TABLE `api_item_log`  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;;");
			$this->log_db_dbh->exec("CREATE index idxKEYTIME on api_item_log(log_date,api_key);");

		}else{
			//表字段定义不一致时，修复
			//ALTER TABLE `api_item_log` ADD `aa` INT NOT NULL AFTER `cache`; 
			$tables = $this->log_db_dbh->getTablesInfo('api_item_log');
			foreach($this->keys_config as $k=>$v){
				if(!isset($tables[$k])){
					$this->log_db_dbh->exec('ALTER TABLE `api_item_log`  ADD `'.$k.'` '.$v['type'].';');

				}
			}
			
		}

 		 $init_db = 1;

	}

}