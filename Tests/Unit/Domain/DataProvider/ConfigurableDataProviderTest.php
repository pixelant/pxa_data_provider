<?php

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

        self::assertIsObject($subject);
    }

    /**
     * @test
     */
    public function objectSupportedReturnsFalseWithNoConfiguration(): void
    {
        $subject = new ConfigurableDataProvider([]);

        self::assertEquals(
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

        self::assertEquals(
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

        self::assertEquals(
            'splobject',
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

        self::assertEquals(
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

        self::assertEquals(
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

        self::assertEquals(
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

        self::assertEquals(
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

        self::assertEquals(
            null,
            $subject->dataForObject($object)['username']
        );
    }
}
