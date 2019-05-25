<?php
/**
* obApi测试代码
*/

if (!function_exists('getmicrotime')) {
    function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
}
if (!function_exists('get_use_time')) {
    function get_use_time($min = false, $reset = false)
    {
        global $time_start;
        static $time_start2;
        if (!$time_start2) {
            $time_start2 = $time_start;
        }

        $time_end = getmicrotime();
        $times    = $time_end - ($reset ? $time_start2 : $time_start);
        $times    = sprintf('%.5f', $times);
        if ($min == false) {
            $use_time = "用时:" . $times . "秒";
        } else {
            $use_time = $times;
        }
        $time_start2 = $time_end;

        return $use_time;
    }

}


$time_start = getmicrotime();
define('DIR_RUNTIME','.');//缓存目录
// define('SECACHE_SIZE','0');//缓存大小


$lang = !empty($_GET['lang'])?$_GET['lang']:'';
$q = !empty($_GET['q'])?$_GET['q']:'';
$api_server = !empty($_GET['api_server'])?$_GET['api_server']:'api-1.onebound.cn';
$remote_translate = !empty($_GET['remote_translate'])?$_GET['remote_translate']:'0';




$cfg_taobao_api_key= 'seventao.com';//API KEY
$cfg_taobao_api_secret='';
$cfg_obapi_cache_db='';
$cfg_taobao_api_url ='http://'.$api_server.'/';
$cfg_secache_size ='0';
$cfg_taobao_secache_time ='0';

include 'startup.php';

 $otao_translate_config=array(
 	'otao_translate_channel'=>'baidu',//使用google翻译
 	'otao_translate_account'=>array(
			'google'=>array(
				'domain'=>'cn',
				'api_key'=>'',
				'referer'=>'',
				),
			// 'baidu'=>array(
			// 	'app_id'=>'',
			// 	'sec_key'=>'',
			// 	'client_id'=>'',
			// 	),
			// 'microsoft'=>array(
			// 	'client_id'=>'',
			// 	'client_secret'=>'',
			// 	),
			// 'youdao'=>array(
			// 	'api_key'=>'',
			// 	'key_from'=>'',
			// 	),
		)
 	);
$obapi->translate()->set_account_config($otao_translate_config);//本地翻译参数
$obapi->translateRemote = $remote_translate; 	//不请求api翻译（使用本地翻译引擎代替）
$obapi->log_file = null;

?>
   <form action="?" method="get" id="form-contact" accept-charset="utf-8">
		api server:<input type="text" name="api_server" value="<?=$api_server?>" placeholder="api_server">
		keyword:<input type="text" name="q" value="<?=$q?>" placeholder="keyword">
		<br>
		lang code:<input type="text" name="lang" value="<?=$lang?>" placeholder="lang" size=4>
		use api server translate:<input type="text" name="remote_translate" value="<?=$remote_translate?>" placeholder="0 or 1" >
		
		<button type="submit">search</button>
  </form>
 
 <?php
// $q = '女装';
//获取列表
$data = $obapi->exec(array('api_type' => 'taobao', 'api_name' => 'item_search', 'api_params' => array('q' => $q, 'page' => 1,'page_size'=>10)));
// var_dump($obapi->m_curl);
echo '<h5>'.$obapi->m_curl->m_options['10002'].''.$obapi->m_curl->m_options['10015'].'</h5>';
if(!empty($data['items']['item'])){
	echo '<ol>';
	foreach($data['items']['item'] as $k=>$v){
		echo '<li>#'.$v['num_iid'].' '.$v['title'].' ￥'.$v['price'].'</lil>';
	}

	echo '</ol>';

}
echo '<hr>';
//获取详情
// $data = $obapi->exec(array('api_type'=>'taobao','api_name'=>'item_get','api_params' => array('num_iid'=>'566817428203')));
// var_dump($data['item']['title']);

echo get_use_time();