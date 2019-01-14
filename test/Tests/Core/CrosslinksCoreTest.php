<?php
/**
 * Crosslinks
 *
 * Copyright 2010 by Jason Coward <jason@modx.com> and Shaun McCormick <shaun+crosslinks@modx.com>
 *
 * Crosslinks is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * Crosslinks is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Crosslinks; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package crosslinks
 * @subpackage test
 */

/**
 * Tests related to Profile snippet
 *
 * @package crosslinks
 * @subpackage test
 * @group Core
 * @group Profile
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
        $source = $this->crosslinks->addCrosslinks($source, $links);
        $result = file_get_contents($this->modx->config['testPath'] . 'Data/Page/result_fullwords.page.tpl');

        $this->assertEquals($source, $result);
    }
}
