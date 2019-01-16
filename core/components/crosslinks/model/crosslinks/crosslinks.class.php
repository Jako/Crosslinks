<?php
/**
 * Crosslinks Classfile
 *
 * Copyright 2018-2019 by Thomas Jakobi <thomas.jakobi@partout.info>
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
    public $version = '1.2.0';

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
            'debug' => (bool)$this->getOption('debug', $options, false),
            'tpl' => $this->getOption('tpl', $options, 'Crosslinks.linkTp'),
            'fullwords' => (bool)$this->getOption('fullwords', $options, true),
            'sectionsStart' => $this->getOption('sectionsStart', $options, '<!-- CrosslinksStart -->'),
            'sectionsEnd' => $this->getOption('sectionsEnd', $options, '<!-- CrosslinksEnd -->'),
            'disabledAttributes' => $this->getOption('disabledAttributes', $options, 'title,alt,value'),
            'disabledTags' => $this->getOption('disabledTags', $options, 'a,form,select'),
            'sections' => (bool)$this->getOption('sections', $options, false)
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
    public function getLinks($chunkName)
    {
        /** @var CrosslinksLink[] $links */
        $links = $this->modx->getCollection('CrosslinksLink');
        $result = array();
        foreach ($links as $link) {
            $linkArray = $link->toArray();
            $result[$linkArray['text']] = $this->modx->getChunk($chunkName, array(
                'text' => $linkArray['text'],
                'link' => $this->modx->makeUrl($linkArray['resource'], '', json_decode($linkArray['parameter'])),
                'resource' => $linkArray['resource'],
                'parameter' => $linkArray['parameter']
            ));
        };
        return $result;
    }

    /**
     * Create links in the text
     *
     * @param string $text
     * @param string $chunkName
     * @param array $links
     * @return string
     */
    public function addCrosslinks($text, $links)
    {
        // Enable section markers
        $enableSections = $this->getOption('sections', null, false);
        if ($enableSections) {
            $splitEx = '~((?:' . $this->getOption('sectionsStart') . ').*?(?:' . $this->getOption('sectionsEnd') . '))~isu';
            $sections = preg_split($splitEx, $text, null, PREG_SPLIT_DELIM_CAPTURE);
        } else {
            $sections = array($text);
        }

        // Mask all links first
        $maskStart = '<_^_>';
        $maskEnd = '<_$_>';
        $fullwords = $this->getOption('fullwords', null, true);
        $disabledTags = array_map('trim', explode(',', $this->getOption('disabledTags')));
        $splitExTags = array();
        foreach ($disabledTags as $disabledTag) {
            $splitExTags[] = '<' . $disabledTag . '.*?</' . $disabledTag . '>';
        }
        $splitExDisabled = '~([a-z0-9-]+\s*=\s*".*?"|' . implode('|', $splitExTags) . ')~isu';

        //'~((?:title|alt)\s*=\s*".*?"|<(a|form|select).*?</\2>)~isu'

        foreach ($links as $linkText => $linkValue) {
            if ($fullwords) {
                foreach ($sections as &$section) {
                    if (($enableSections && strpos($section, $this->getOption('sectionsStart')) === 0 && preg_match('/\b' . preg_quote($linkText) . '\b/u', $section)) ||
                        (!$enableSections && preg_match('/\b' . preg_quote($linkText) . '\b/u', $section))
                    ) {
                        $subSections = preg_split($splitExDisabled, $section, null, PREG_SPLIT_DELIM_CAPTURE);
                        foreach ($subSections as &$subSection) {
                            if (!preg_match($splitExDisabled, $subSection)) {
                                $subSection = preg_replace('/\b' . preg_quote($linkText) . '\b/u', $maskStart . $linkText . $maskEnd, $subSection);
                            }
                        }
                        $section = implode('', $subSections);
                    }
                }
            } else {
                foreach ($sections as &$section) {
                    if (($enableSections && strpos($section, $this->getOption('sectionsStart')) === 0 && strpos($section, $linkText) !== false) ||
                        (!$enableSections && strpos($section, $linkText) !== false)
                    ) {
                        $subSections = preg_split($splitExDisabled, $section, null, PREG_SPLIT_DELIM_CAPTURE);
                        foreach ($subSections as &$subSection) {
                            if (!preg_match($splitExDisabled, $subSection)) {
                                $subSection = str_replace($linkText, $maskStart . $linkText . $maskEnd, $subSection);
                            }
                        }
                        $section = implode('', $subSections);
                    }
                }
            }
        }
        $text = implode('', $sections);

        // And replace the links after to avoid nested replacement
        foreach ($links as $linkText => $linkValue) {
            $text = str_replace($maskStart . $linkText . $maskEnd, $linkValue, $text);
        }

        // Remove remaining section markers
        $text = ($enableSections) ? str_replace(array(
            $this->getOption('sectionsStart'), $this->getOption('sectionsEnd')
        ), '', $text) : $text;
        return $text;
    }
}