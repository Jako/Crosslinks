<?php
/**
 * Crosslinks Core Tests
 *
 * @package crosslinks
 * @subpackage test
 */

class CrosslinksCoreTest extends CrosslinksTestCase
{
    public function testAddCrosslinks()
    {
        $source = file_get_contents($this->modx->config['testPath'] . 'Data/Page/source.page.tpl');
        $links = array(
            'Template' => '<a class="crosslink" href="https://linkurl" title="Template">Template</a>'
        );
        $this->crosslinks->options['sections'] = true;
        $this->crosslinks->options['fullwords'] = false;
        $this->crosslinks->options['limit'] = 0;
        $source = $this->crosslinks->addCrosslinks($source, $links);
        $result = file_get_contents($this->modx->config['testPath'] . 'Data/Page/result.page.tpl');

        $this->assertEquals($source, $result);
    }

    public function testAddCrosslinksFullwords()
    {
        $source = file_get_contents($this->modx->config['testPath'] . 'Data/Page/source.page.tpl');
        $links = array(
            'Template' => '<a class="crosslink" href="https://linkurl" title="Template">Template</a>'
        );
        $this->crosslinks->options['sections'] = true;
        $this->crosslinks->options['fullwords'] = true;
        $this->crosslinks->options['limit'] = 0;
        $source = $this->crosslinks->addCrosslinks($source, $links);
        $result = file_get_contents($this->modx->config['testPath'] . 'Data/Page/result_fullwords.page.tpl');

        $this->assertEquals($source, $result);
    }
}
