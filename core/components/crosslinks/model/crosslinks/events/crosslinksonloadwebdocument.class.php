<?php
/**
 * @package crosslinks
 * @subpackage plugin
 */

class CrosslinksOnLoadWebDocument extends CrosslinksPlugin
{
    public function run()
    {
        $chunkName = $this->crosslinks->getOption('tpl');

        $content = $this->modx->resource->get('content');
        $newContent = $this->crosslinks->addCrosslinks($content, $chunkName);
        $this->modx->resource->set('content', $newContent);
    }
}
