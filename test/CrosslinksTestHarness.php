<?php
/**
 * Crosslinks Test Harness
 *
 * Run the tests in the test folder with `phpunit`. The tests use the MODX
 * installation in the config.core.php of the extra. Set the MODX_CONFIG_CORE
 * environment variable to the config.core.php of another MODX 2 or MODX 3
 * installation to run the tests there:
 *
 * MODX_CONFIG_CORE=/path/to/modx3/config.core.php phpunit
 *
 * @package crosslinks
 * @subpackage test
 */
require_once strtr(realpath(dirname(__FILE__)) . '/CrosslinksTestCase.php', '\\', '/');

class CrosslinksTestHarness
{
    /**
     * @var modX Static reference to modX instance.
     */
    public static $modx = null;
    /**
     * @var array Static reference to configuration array.
     */
    public static $properties = [];

    /**
     * Load all Test Suites for xPDO Test Harness.
     *
     * @return CrosslinksTestHarness
     */
    public static function suite()
    {
        $suite = new CrosslinksTestHarness();
        return $suite;
    }

    /**
     * Grab a persistent instance of the xPDO class to share connection data
     * across multiple tests and test suites.
     *
     * @param array $options An array of configuration parameters.
     * @return xPDO An xPDO object instance.
     */
    public static function _getConnection($options = [])
    {
        $modx = CrosslinksTestHarness::$modx;
        if (is_object($modx)) {
            if (!$modx->request) {
                $modx->getRequest();
            }
            if (!$modx->error) {
                $modx->request->loadErrorHandler();
            }
            $modx->error->reset();
            CrosslinksTestHarness::$modx = $modx;
            return CrosslinksTestHarness::$modx;
        }

        /* include config.core.php, the MODX_CONFIG_CORE environment variable can point to the config.core.php of another installation */
        $properties = [];
        $configCore = getenv('MODX_CONFIG_CORE') ?: dirname(__FILE__, 2) . '/config.core.php';
        if (!file_exists($configCore)) {
            throw new RuntimeException('The config.core.php "' . $configCore . '" does not exist.');
        }
        include_once $configCore;
        require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';

        /* MODX 3 is autoloaded, MODX 2 has a class file */
        if (file_exists(MODX_CORE_PATH . 'src/Revolution/modX.php')) {
            require_once MODX_CORE_PATH . 'vendor/autoload.php';
            $modxClass = \MODX\Revolution\modX::class;
            $userClass = \MODX\Revolution\modUser::class;
        } else {
            require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
            $modxClass = 'modX';
            $userClass = 'modUser';
        }
        include_once strtr(realpath(dirname(__FILE__)) . '/properties.inc.php', '\\', '/');

        if (!defined('MODX_REQP')) {
            define('MODX_REQP', false);
        }
        /** @var modX $modx */
        $modx = new $modxClass(null, $properties);
        if (!class_exists('modX')) {
            throw new RuntimeException('The extra needs the global MODX class aliases. Enable the load_deprecated_global_class_aliases system setting.');
        }
        $ctx = !empty($options['ctx']) ? $options['ctx'] : 'web';
        $modx->initialize($ctx);

        $debug = !empty($options['debug']);
        $modx->setDebug($debug);
        if (!empty($properties['logTarget'])) $modx->setLogTarget($properties['logTarget']);
        if (!empty($properties['logLevel'])) $modx->setLogLevel($properties['logLevel']);
        $modx->user = $modx->newObject($userClass);
        $modx->user->set('id', $modx->getOption('modx.test.user.id', null, 1));
        $modx->user->set('username', $modx->getOption('modx.test.user.username', null, 'test'));

        $modx->getRequest();
        $modx->getParser();
        $modx->request->loadErrorHandler();

        CrosslinksTestHarness::$modx = $modx;
        CrosslinksTestHarness::$properties = $properties;
        return $modx;
    }
}
