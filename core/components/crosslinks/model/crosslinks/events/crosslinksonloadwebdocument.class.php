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
        $links = $this->crosslinks->getLinks($chunkName);
        $newContent = $this->crosslinks->addCrosslinks($content, $links);
        $this->modx->resource->set('content', $newContent);
    }
}
