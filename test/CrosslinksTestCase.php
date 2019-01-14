<?php
/**
 * @package crosslinks
 * @subpackage test
 */

/**
 * Extends the basic PHPUnit TestCase class to provide Crosslinks specific methods
 *
 * @package crosslinks
 * @subpackage test
 */
class CrosslinksTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var modX $modx
     */
    protected $modx = null;
    /**
     * @var Crosslinks $crosslinks
     */
    protected $crosslinks = null;

    /**
     * Ensure all tests have a reference to the MODX and Quip objects
     */
    public function setUp()
    {
        $this->modx = CrosslinksTestHarness::_getConnection();

        $corePath = $this->modx->getOption('crosslinks.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/crosslinks/');
        require_once $corePath . 'model/crosslinks/crosslinks.class.php';

        $this->crosslinks = new Crosslinks($this->modx);
        $this->crosslinks->options['debug'] = true;

        $this->modx->placeholders = array();
        $this->modx->crosslinks = &$this->crosslinks;

        error_reporting(E_ALL);
    }

    /**
     * Remove reference at end of test case
     */
    public function tearDown()
    {
        $this->modx = null;
        $this->crosslinks = null;
    }
}
