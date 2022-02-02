<?php
/**
 * @package crosslinks
 * @subpackage plugin
 */

namespace TreehillStudio\Crosslinks\Plugins\Events;

use TreehillStudio\Crosslinks\Plugins\Plugin;

class OnLoadWebDocument extends Plugin
{
    public function process()
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
            if (!in_array($this->modx->resource->get('template'), $templates)) {
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
