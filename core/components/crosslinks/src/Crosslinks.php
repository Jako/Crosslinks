<?php
/**
 * Crosslinks
 *
 * Copyright 2018-2019 by Thomas Jakobi <office@treehillstudio.com>
 *
 * @package crosslinks
 * @subpackage classfile
 */

namespace TreehillStudio\Crosslinks;

use modX;

/**
 * Class Crosslinks
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
     * The package name
     * @var string $packageName
     */
    public $packageName = 'Crosslinks';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.2.2';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * Crosslinks constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
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
        ], $options);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('crosslinks');

        $this->modx->addPackage($this->namespace, $this->getOption('modelPath'));

        // Add default options
        $this->options = array_merge($this->options, [
            'debug' => (bool)$this->getOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
            'disabledTags' => $this->getOption('disabledTags', $options, 'a,form,select'),
            'fullwords' => (bool)$this->getOption('fullwords', $options, true),
            'is_admin' => $this->modx->user && $this->modx->context && ($modx->hasPermission('settings') || $modx->hasPermission($this->namespace . '_settings')),
            'limit' => (int)$this->getOption('limit', $options, 0),
            'sections' => (bool)$this->getOption('sections', $options, false),
            'sectionsEnd' => $this->getOption('sectionsEnd', $options, '<!-- CrosslinksEnd -->'),
            'sectionsStart' => $this->getOption('sectionsStart', $options, '<!-- CrosslinksStart -->'),
            'tpl' => $this->getOption('tpl', $options, 'Crosslinks.linkTp'),
            'enabledContexts' => $this->getOption('enabledContexts', $options, ''),
            'enabledTemplates' => $this->getOption('enabledTemplates', $options, ''),
        ]);
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
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
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
        $result = [];
        foreach ($links as $link) {
            if ($link->get('resource') !== $this->modx->resource->get('id')) {
                $result[$link->get('text')] = $this->modx->getChunk($chunkName, [
                    'text' => $link->get('text'),
                    'link' => $this->modx->makeUrl($link->get('resource'), '', json_decode($link->get('parameter'), true)),
                    'resource' => $link->get('resource'),
                    'parameter' => $link->get('parameter')
                ]);
            } else {
                $result[$link->get('text')] = $link->get('text');
            }
        }
        return $result;
    }

    /**
     * Create links in the text
     *
     * @param string $text
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
            $sections = [$text];
        }

        // Mask all links first
        $maskStart = '<_^_>';
        $maskEnd = '<_$_>';
        $fullwords = $this->getOption('fullwords', null, true);
        $disabledTags = array_map('trim', explode(',', $this->getOption('disabledTags')));
        $splitExTags = [];
        foreach ($disabledTags as $disabledTag) {
            $splitExTags[] = '<' . $disabledTag . '.*?</' . $disabledTag . '>';
        }
        // No replacements in html tag attributes and disabled tags
        $splitExDisabled = '~([a-z0-9-]+\s*=\s*".*?"|' . implode('|', $splitExTags) . ')~isu';
        foreach ($links as $linkText => $linkValue) {
            if ($fullwords) {
                foreach ($sections as &$section) {
                    if (($enableSections && strpos($section, $this->getOption('sectionsStart')) === 0 && preg_match('/\b' . preg_quote($linkText, '/') . '\b/u', $section)) ||
                        (!$enableSections && preg_match('/\b' . preg_quote($linkText, '/') . '\b/u', $section))
                    ) {
                        $subSections = preg_split($splitExDisabled, $section, null, PREG_SPLIT_DELIM_CAPTURE);
                        foreach ($subSections as &$subSection) {
                            if (!preg_match($splitExDisabled, $subSection)) {
                                $subSection = preg_replace('/\b' . preg_quote($linkText, '/') . '\b/u', $maskStart . $linkText . $maskEnd, $subSection);
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

        // Replace the links after to avoid nested replacement
        foreach ($links as $linkText => $linkValue) {
            $text = $this->str_replace_limit($maskStart . $linkText . $maskEnd, $linkValue, $text, $this->getOption('limit'));
            if ($this->getOption('limit')) {
                $text = $this->str_replace_limit($maskStart . $linkText . $maskEnd, $linkText, $text);
            }
        }

        // Remove remaining section markers
        $text = str_replace([
            $this->getOption('sectionsStart'), $this->getOption('sectionsEnd')
        ], '', $text);
        return $text;
    }

    /**
     * str_replace with a limit
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $limit
     * @return string|string[]|null
     */
    private function str_replace_limit($search, $replace, $subject, $limit = 0)
    {
        if ($limit == 0) {
            return str_replace($search, $replace, $subject);
        }
        $search = '/' . preg_quote($search, '/') . '/';
        return preg_replace($search, $replace, $subject, $limit);
    }
}
