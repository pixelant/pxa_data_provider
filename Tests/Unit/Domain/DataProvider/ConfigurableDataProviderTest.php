<?php

declare(strict_types=1);

namespace Unit\Domain\DataProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaDataProvider\Domain\DataProvider\ConfigurableDataProvider;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class ConfigurableDataProviderTest extends UnitTestCase
{

    /**
     * @test
     */
    public function configurableDataProviderCanBeInstantiatedWithEmptyArray(): void
    {
        $subject = new ConfigurableDataProvider([]);

        $this->assertEquals(
            true,
            is_object($subject)
        );
    }

    /**
     * @test
     */
    public function objectSupportedReturnsFalseWithNoConfiguration(): void
    {
        $subject = new ConfigurableDataProvider([]);

        $this->assertEquals(
            false,
            $subject->isObjectSupported(new \stdClass())
        );
    }

    /**
     * @test
     */
    public function objectSupportedReturnsTrueIfObjectIsConfiguredAsSupported(): void
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'stdClass.' => []
            ]
        ]);

        $this->assertEquals(
            true,
            $subject->isObjectSupported(new \stdClass())
        );
    }

    /**
     * @test
     */
    public function subclassInheritsConfigurationFromParent(): void
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'SplDoublyLinkedList.' => [
                    'key' => 'splobject'
                ],
                'SplQueue.' => []
            ]
        ]);

        $this->assertEquals(
            'splobject',
            $subject->getProviderSettingsForObject(new \SplQueue())['key']
        );
    }

    /**
     * @test
     */
    public function subclassInheritsConfigurationFromParentAndReplacesNonArrays()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'SplDoublyLinkedList.' => [
                    'key' => 'splobject'
                ],
                'SplQueue.' => [
                    'key' => 'splqueue'
                ]
            ]
        ]);

        $this->assertEquals(
            'splqueue',
            $subject->getProviderSettingsForObject(new \SplQueue())['key']
        );
    }

    /**
     * @test
     */
    public function currentIncludePropertiesOverrideParentExcludeProperties()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'SplDoublyLinkedList.' => [
                    'includeProperties' => 'property2',
                    'excludeProperties' => 'property1'
                ],
                'SplQueue.' => [
                    'includeProperties' => 'property1'
                ]
            ]
        ]);

        $this->assertEquals(
            [
                'property2',
                'property1'
            ],
            $subject->getProviderSettingsForObject(new \SplQueue())['includeProperties']
        );
    }

    /**
     * @test
     */
    public function dataForObjectReturnsCorrectData()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'TYPO3\CMS\Extbase\Domain\Model\FrontendUser.' => [
                    'key' => 'frontenduser',
                    'includeProperties' => 'username'
                ]
            ]
        ]);

        $theUsername = 'theUsername';

        $object = new FrontendUser($theUsername);

        $this->assertEquals(
            $theUsername,
            $subject->dataForObject($object)['username']
        );
    }

    /**
     * @test
     */
    public function dataForObjectsReturnsCorrectData()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'TYPO3\CMS\Extbase\Domain\Model\FrontendUser.' => [
                    'key' => 'frontenduser',
                    'includeProperties' => 'username'
                ]
            ]
        ]);

        $theUsername = 'theUsername';

        $object = new FrontendUser($theUsername);

        $this->assertEquals(
            $theUsername,
            $subject->dataForObjects([$object])['frontenduser'][0]['username']
        );
    }

    /**
     * @test
     */
    public function remapPropertiesRemapsProperties()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'TYPO3\CMS\Extbase\Domain\Model\FrontendUser.' => [
                    'key' => 'frontenduser',
                    'includeProperties' => 'username',
                    'remapProperties.' => [
                        'username' => 'remappedproperty'
                    ]
                ]
            ]
        ]);

        $theUsername = 'theUsername';

        $object = new FrontendUser($theUsername);

        $this->assertEquals(
            $theUsername,
            $subject->dataForObject($object)['remappedproperty']
        );
    }

    /**
     * @test
     */
    public function oldPropertyIsUnsetWhenPropertyIsRemapped()
    {
        $subject = new ConfigurableDataProvider([
            'objectConfig.' => [
                'TYPO3\CMS\Extbase\Domain\Model\FrontendUser.' => [
                    'key' => 'frontenduser',
                    'includeProperties' => 'username',
                    'remapProperties.' => [
                        'username' => 'remappedproperty'
                    ]
                ]
            ]
        ]);

        $theUsername = 'theUsername';

        $object = new FrontendUser($theUsername);

        $this->assertEquals(
            null,
            $subject->dataForObject($object)['username']
        );
    }
}
