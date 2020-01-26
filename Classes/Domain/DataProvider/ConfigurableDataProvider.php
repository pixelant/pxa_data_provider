<?php

namespace Pixelant\PxaDataProvider\Domain\DataProvider;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Pixelant
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class ConfigurableDataProvider
 * @package PxaDataProvider\Domain\DataProvider
 */
class ConfigurableDataProvider implements SingletonInterface
{
    /**
     * Extension settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Settings from plugin.tx_pxaprodutmanager.settings.dataProvider
     *
     * @var array
     */
    protected $providerSettings;

    /**
     * Supported full class namespaces
     *
     * @var array
     */
    protected $supportedClasses = [];

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * ConfigurableDataProvider constructor.
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $this->configurationManager = $objectManager->get(ConfigurationManager::class);
        $this->configurationManager->setContentObject($objectManager->get(ContentObjectRenderer::class));

        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        )['plugin.']['tx_pxadataprovider.']['settings.'];
        $this->providerSettings = $this->settings['objectConfig.'];
        $this->supportedClasses = array_keys($this->providerSettings);
        array_walk(
            $this->supportedClasses,
            function (&$item) {
                if (substr($item, -1) === '.')
                $item = substr($item, 0, strlen($item) - 1);
            }
        );

        //Convert comma separated lists once and for all
        foreach ($this->providerSettings as &$providerSetting) {
            $providerSetting['includeProperties'] = GeneralUtility::trimExplode(
                ',',
                $providerSetting['includeProperties'],
                true
            );
            $providerSetting['excludeProperties'] = GeneralUtility::trimExplode(
                ',',
                $providerSetting['excludeProperties'],
                true
            );
        }
    }

    /**
     * @param array $objects
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function dataForObjects(array $objects): array
    {
        $data = [];

        foreach ($objects as $index=>$object) {
            if($object === null) {
                continue;
            }

            if (!$this->isObjectSupported($object)) {
                throw new \InvalidArgumentException(
                    'Object at index "' . $index . '" is not an instance of a supported class '
                    . '. Supplied value was "' . get_class($object) . '". Supported classes are: '
                    . implode(', ', array_keys($this->providerSettings)),
                    1579781212.
                );
            }

            $objectProviderSettings = $this->getProviderSettingsForObject($object);

            if (!isset($objectProviderSettings['key'])) {
                $objectProviderSettings['key'] = 'object';
            }

            $data[$objectProviderSettings['key']][] = $this->dataForObject($object);
        }

        return $data;
    }

    /**
     * @param object $object
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function dataForObject(object $object): array
    {
        if (!$this->isObjectSupported($object)) {
            throw new \InvalidArgumentException(
                'Object supplied is not an instance of a supported class '
                . '. Supplied object class was "' . get_class($object) . '". Supported classes are: '
                . implode(', ', array_keys($this->providerSettings)),
                1579781615.
            );
        }

        $objectProviderSettings = $this->getProviderSettingsForObject($object);

        $properties = array_diff($objectProviderSettings['includeProperties'], $objectProviderSettings['excludeProperties']);

        $data = [];

        foreach ($properties as $property) {
            if (method_exists($object, 'get' . $property)) {
                $data[$property] = call_user_func([$object, 'get' . $property]);
            } elseif (method_exists($object, 'is' . $property)) {
                $data[$property] = call_user_func([$object, 'is' . $property]);
            }
        }

        foreach ($objectProviderSettings['remapProperties.'] as $originalFieldName=>$newFieldName) {
            $data[$newFieldName] = $data[$originalFieldName];
            unset($data[$originalFieldName]);
        }

        foreach ($objectProviderSettings['processProperties.'] as $property=>$stdWrap) {
            $contentObject = $this->configurationManager->getContentObject();

            $data[$property] = $contentObject->stdWrap($data[$property], $stdWrap);
        }

        return $data;
    }

    /**
     * Returns true if the supplied object's class or parent class has a configuration
     *
     * @param object $object
     * @return bool
     */
    protected function isObjectSupported(object $object)
    {
        $fullObjectClassName = get_class($object);

        if (in_array($fullObjectClassName, $this->supportedClasses)) {
            return true;
        }

        $classParentsFullObjectClassNames = array_values(class_parents($object));

        if (count(array_intersect($classParentsFullObjectClassNames, $this->supportedClasses)) > 0) {
            $this->supportedClasses = array_unique(array_merge($classParentsFullObjectClassNames, $this->supportedClasses));
            return true;
        }

        return false;
    }

    /**
     * Returns the provider settings for the supplied object
     *
     * @param object $object
     * @return array
     */
    protected function getProviderSettingsForObject(object $object): array
    {
        $objectAndParentClassNames = array_merge([get_class($object)], array_values(class_parents($object)));

        return $this->getProviderSettingsForClassAncestors($objectAndParentClassNames);
    }

    /**
     * Compiles provider settings for the array of class ancestors
     *
     * if $providerSettings[<className>][ignoreParents] is set, recursing will end and the setting returned.
     *
     * @param array $ancestors
     * @return array
     */
    protected function getProviderSettingsForClassAncestors(array $ancestors): array
    {
        $mostRecentAncestor = array_shift($ancestors);
        $mostRecentAncestorSettings = $this->providerSettings[$mostRecentAncestor . '.'];

        if (!is_array($mostRecentAncestorSettings)) {
            $mostRecentAncestorSettings = [];
        }

        $ancestorSettings = [];
        if ($mostRecentAncestorSettings['ignoreParents']) {
            return $this->providerSettings[$mostRecentAncestor . '.'];
        }

        if (count($ancestors) > 0) {
            $ancestorSettings = $this->getProviderSettingsForClassAncestors($ancestors);

            // Remove now included properties from the previously excluded properties to ensure that new includes
            // override old excludes.
            $mostRecentAncestorSettings['excludeProperties'] = array_diff(
                // Extract previously exluded properties that are now in the included list
                array_intersect(
                    $mostRecentAncestorSettings['excludeProperties'],
                    $ancestorSettings['includeProperties']
                ),
                $mostRecentAncestorSettings['excludeProperties']
            );

            ArrayUtility::mergeRecursiveWithOverrule(
                $ancestorSettings,
                $mostRecentAncestorSettings
            );
        }

        $ancestorSettings['includeProperties'] = array_unique($ancestorSettings['includeProperties']);
        $ancestorSettings['excludeProperties'] = array_unique($ancestorSettings['excludeProperties']);

        $this->providerSettings[$mostRecentAncestor . '.'] = $ancestorSettings;
        //Avoid recursing next time
        $this->providerSettings[$mostRecentAncestor . '.']['ignoreParents'] = true;

        return $ancestorSettings;
    }
}
