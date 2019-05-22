<?php
/**
 * @package crosslinks
 * @subpackage plugin
 */

class CrosslinksOnLoadWebDocument extends CrosslinksPlugin
{
    public function run()
    {
        $contexts = $this->crosslinks->getOption('enabledContexts');
        if ($contexts) {
            $contexts = explode(',', $contexts);
            if (!in_array($this->modx->context->key, $contexts)) {
                return;
            }
        }
        $templates = $this->crosslinks->getOption('enabledTemplates');
        if ($templates) {
            $templates = explode(',', $templates);
            if (!in_array($this->modx->context->key, $templates)) {
                return;
            }
        }

        $chunkName = $this->crosslinks->getOption('tpl');

        $content = $this->modx->resource->get('content');
        $links = $this->crosslinks->getLinks($chunkName);
        $newContent = $this->crosslinks->addCrosslinks($content, $links);
        $this->modx->resource->set('content', $newContent);
    }
}
