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
    public $version = '1.1.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    /**
     * Crosslinks constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    function __construct(modX &$modx, $options = array())
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
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
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        // Add default options
        $this->options = array_merge($this->options, array(
            'is_admin' => ($this->modx->user) ? $this->modx->user->isMember('Administrator') || $this->modx->user->isMember('Agenda Administrator') : false,
            'debug' => (bool)$this->getOption('debug', $options, false)
        ));

        $this->modx->addPackage($this->namespace, $this->getOption('modelPath'));
        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');
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
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
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
     * Create links in the text
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
            $splitEx = '#((?:' . $this->getOption('sectionsStart') . ').*?(?:' . $this->getOption('sectionsEnd') . '))#isu';
            $sections = preg_split($splitEx, $text, null, PREG_SPLIT_DELIM_CAPTURE);
        } else {
            $sections = array($text);
        }

        // Mask all links first
        $links = $this->getLinks();
        $maskStart = '<_^_>';
        $maskEnd = '<_$_>';
        $fullwords = $this->getOption('fullwords', null, true);
        $disabledAttributes = array_map('trim', explode(',', $this->getOption('disabledAttributes', null, 'title,alt')));
        $splitEx = '#((?:' . implode('|', $disabledAttributes) . ')\s*=\s*".*?")#isu';
        foreach ($links as $link) {
            if ($fullwords) {
                foreach ($sections as &$section) {
                    if (($enableSections && strpos($section, $this->getOption('sectionsStart')) === 0 && preg_match('/\b' . preg_quote($link['text']) . '\b/u', $section)) ||
                        (!$enableSections && preg_match('/\b' . preg_quote($link['text']) . '\b/u', $section))
                    ) {
                        $subSections = preg_split($splitEx, $section, null, PREG_SPLIT_DELIM_CAPTURE);
                        foreach ($subSections as &$subSection) {
                            if (!preg_match($splitEx, $subSection)) {
                                $subSection = preg_replace('/\b' . preg_quote($link['text']) . '\b/u', $maskStart . $link['text'] . $maskEnd, $subSection);
                            }
                        }
                        $section = implode('', $subSections);
                    }
                }
            } else {
                foreach ($sections as &$section) {
                    if (($enableSections && strpos($section, $this->getOption('sectionsStart')) === 0 && strpos($section, $link['text']) !== false) ||
                        (!$enableSections && strpos($section, $link['text']) !== false)
                    ) {
                        $subSections = preg_split($splitEx, $section, null, PREG_SPLIT_DELIM_CAPTURE);
                        foreach ($subSections as &$subSection) {
                            if (!preg_match($splitEx, $subSection)) {
                                $subSection = str_replace($link['text'], $maskStart . $link['text'] . $maskEnd, $subSection);
                            }
                        }
                        $section = implode('', $subSections);
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
            $this->getOption('sectionsStart'), $this->getOption('sectionsEnd')
        ), '', $text) : $text;
        return $text;
    }
}
