<?php

/*
 * This file is part of the RouterOS project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Tests\RouterOS\Generator\Provider\Ansible\Build;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RouterOS\Generator\Concerns\EventSubscriberAssertions;
use RouterOS\Generator\Event\BuildEvent;
use RouterOS\Generator\Provider\Ansible\Build\CleanupTarget;
use RouterOS\Generator\Provider\Ansible\Contracts\ModuleManagerInterface;

class CleanupTargetTest extends TestCase
{
    use EventSubscriberAssertions;

    /**
     * @var MockObject|ModuleManagerInterface
     */
    private $moduleManager;

    /**
     * @var CleanupTarget
     */
    private $listener;

    /**
     * @var string
     */
    private $targetDir;

    private $output;

    private $event;

    protected function setUp(): void
    {
        $this->subscriberEventClass = CleanupTarget::class;
        $this->moduleManager = $this->createMock(ModuleManagerInterface::class);
        $this->targetDir = sys_get_temp_dir().'/routeros-generator/target';
        $this->event = $this->createMock(BuildEvent::class);

        filesystem()->mirror(__DIR__.'/fixtures/target', $this->targetDir);

        $this->listener = new CleanupTarget(
            $this->moduleManager,
            'ros_',
            $this->targetDir
        );
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSubscribedEvent(BuildEvent::PREPARE, 'onPrepare');
    }

    public function testOnPrepare()
    {
        $event = $this->event;
        $moduleManager = $this->moduleManager;
        $listener = $this->listener;
        $targetDir = $this->targetDir;

        $list = [
            'capsman_configuration' => [
                'package' => 'capsman',
            ],
            'bridge' => [
                'package' => 'interface.bridge',
            ],
            'interface' => [
                'package' => 'interface',
            ],
        ];

        $event
            ->expects($this->once())
            ->method('log')
            ->with($this->isType('string'));

        $moduleManager
            ->expects($this->once())
            ->method('getList')
            ->willReturn($list);

        $listener->onPrepare($event);

        $this->assertDirectoryDoesNotExist($targetDir.'/plugins/module_utils/resources/capsman');
        $this->assertDirectoryDoesNotExist($targetDir.'/plugins/module_utils/resources/interface');
        $this->assertFileDoesNotExist($targetDir.'/plugins/modules/ros_interface.py');
        $this->assertFileDoesNotExist($targetDir.'/plugins/modules/ros_bridge.py');

        $this->assertDirectoryDoesNotExist($targetDir.'/tests/integration/targets/ros_bridge');
        $this->assertDirectoryDoesNotExist($targetDir.'/tests/integration/targets/ros_interface');

        $this->assertDirectoryDoesNotExist($targetDir.'/tests/unit/modules/fixtures/facts');
        $this->assertDirectoryDoesNotExist($targetDir.'/tests/unit/modules/fixtures/units');

        $this->assertFileExists($targetDir.'/plugins/module_utils/resources/__init__.py');
        $this->assertFileExists($targetDir.'/plugins/modules/ros_facts.py');
    }
}