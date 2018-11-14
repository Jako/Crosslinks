<?php
/**
 * Crosslinks connector
 *
 * @package crosslinks
 * @subpackage connector
 *
 * @var modX $modx
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('crosslinks.core_path', null, $modx->getOption('core_path') . 'components/crosslinks/');
$crosslinks = $modx->getService('crosslinks', 'Crosslinks', $corePath . 'model/crosslinks/', array(
    'core_path' => $corePath
));

// Handle request
$modx->request->handleRequest(array(
    'processors_path' => $crosslinks->getOption('processorsPath'),
    'location' => ''
));