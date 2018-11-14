<?php
/**
 * Crosslinks Classfile
 *
 * Copyright 2018 by Thomas Jakobi <thomas.jakobi@partout.info>
 *
 * @package crosslinks
 * @subpackage classfile
 */

/**
 * class Crosslinks
 */
class Crosslinks
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'crosslinks';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.0.0';

    /**
     * The class config
     * @var array $config
     */
    public $config = array();

    /**
     * Crosslinks constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $config An config array. Optional.
     */
    function __construct(modX &$modx, $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $config, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $config, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->config = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
        ), $config);

        // set default options
        $this->config = array_merge($this->config, array(
            'crosslinksStart' => $this->getOption('crosslinksStart', $config, '<!-- CrosslinksStart -->'),
            'crosslinksEnd' => $this->getOption('crosslinksEnd', $config, '<!-- CrosslinksEnd -->')
        ));

        $modx->getService('lexicon', 'modLexicon');
        $this->modx->lexicon->load($this->namespace . ':default');

        $this->modx->addPackage('crosslinks', $this->config['modelPath']);
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Get the Crosslinks links
     *
     * @return array
     */
    public function getLinks()
    {
        /** @var CrosslinksLink[] $links */
        $links = $this->modx->getCollection('CrosslinksLink');
        $retArray = array();
        foreach ($links as $link) {
            $retArray[] = $link->toArray();
        };
        return $retArray;
    }

    /**
     * Highlight links in the text
     *
     * @param string $text
     * @param string $chunkName
     * @return string
     */
    public function addCrosslinks($text, $chunkName)
    {
        // Enable section markers
        $enableSections = $this->getOption('sections', null, false);
        if ($enableSections) {
            $splitEx = '#((?:' . $this->getOption('crosslinksStart') . ').*?(?:' . $this->getOption('crosslinksEnd') . '))#isu';
            $sections = preg_split($splitEx, $text, null, PREG_SPLIT_DELIM_CAPTURE);
        } else {
            $sections = array($text);
        }

        // Mask all links first
        $links = $this->getLinks();
        $maskStart = '<_^_>';
        $maskEnd = '<_$_>';
        $fullwords = $this->getOption('fullwords', null, true);
        foreach ($links as $link) {
            if ($fullwords) {
                foreach ($sections as &$section) {
                    if (($enableSections && substr($section, 0, strlen($this->getOption('crosslinksStart'))) == $this->getOption('crosslinksStart') && preg_match('/\b' . preg_quote($link['text']) . '\b/u', $section)) ||
                        (!$enableSections && preg_match('/\b' . preg_quote($link['text']) . '\b/u', $section))
                    ) {
                        $section = preg_replace('/\b' . preg_quote($link['text']) . '\b/u', $maskStart . $link['text'] . $maskEnd, $section);
                    }
                }
            } else {
                foreach ($sections as &$section) {
                    if (($enableSections && substr($section, 0, strlen($this->getOption('crosslinksStart'))) == $this->getOption('crosslinksStart') && strpos($text, $link['text']) !== false) ||
                        (!$enableSections && strpos($text, $link['text']) !== false)
                    ) {
                        $section = str_replace($link['text'], $maskStart . $link['text'] . $maskEnd, $section);
                    }
                }
            }
        }
        $text = implode('', $sections);

        // And replace the links after to avoid nested replacement
        foreach ($links as $link) {
            $chunk = $this->modx->getChunk($chunkName, array(
                'text' => $link['text'],
                'link' => $this->modx->makeUrl($link['resource'], '', json_decode($link['parameter'])),
                'resource' => $link['resource'],
                'parameter' => $link['parameter']
            ));
            $text = str_replace($maskStart . $link['text'] . $maskEnd, $chunk, $text);
        }

        // Remove remaining section markers
        $text = ($enableSections) ? str_replace(array(
            $this->getOption('crosslinksStart'), $this->getOption('crosslinksEnd')
        ), '', $text) : $text;
        return $text;
    }
}
