<?php
/**
 * Properties file for testing
 *
 * @package crosslinks
 * @subpackage test
 */

// The global xPDO class alias is not available before MODX 3 is initialized
$xpdo = class_exists('xPDO\xPDO') ? 'xPDO\xPDO' : 'xPDO';

$properties = [
    constant($xpdo . '::OPT_CACHE_PATH') => MODX_CORE_PATH . 'cache/',
    constant($xpdo . '::OPT_HYDRATE_FIELDS') => true,
    constant($xpdo . '::OPT_HYDRATE_RELATED_OBJECTS') => true,
    constant($xpdo . '::OPT_HYDRATE_ADHOC_FIELDS') => true,
];

/* PHPUnit test config */
$properties['testPath'] = dirname(__FILE__) . '/';
$properties['logLevel'] = constant($xpdo . '::LOG_LEVEL_INFO');
