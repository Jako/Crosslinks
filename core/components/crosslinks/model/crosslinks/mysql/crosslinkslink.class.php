<?php
/**
 * @package crosslinks
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/crosslinkslink.class.php');
class CrosslinksLink_mysql extends CrosslinksLink {}
?>