<?php
if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
    require(dirname(__FILE__). '/secache_new.php');
}
else {
    require(dirname(__FILE__). '/secache_old.php');
}