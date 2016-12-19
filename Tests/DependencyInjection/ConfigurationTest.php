<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\DependencyInjection;

use DavidKmenta\CommandSupervisorBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider exceptionDataProvider
     * @param array $configuration
     * @param string $expectedExceptionMessage
     */
    public function testShouldThrowException(array $configuration, $expectedExceptionMessage)
    {
        $processor = new Processor();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $processor->processConfiguration(new Configuration(), [$configuration]);
    }

    /**
     * @return array
     */
    public function exceptionDataProvider()
    {
        return [
            'no command is defined' => [
                [
                    'commands' => [],
                ],
                'The path "command_supervisor.commands" should have at least 1 element(s) defined.',
            ],
            'cache path is not defined' => [
                [
                    'commands' => [
                        ['name' => 'command:name', 'threshold' => 10]
                    ],
                ],
                'The child node "cache_path" at path "command_supervisor" must be configured.',
            ],
            'threshold is too small' => [
                [
                    'commands' => [
                        ['name' => 'command:name', 'threshold' => 0]
                    ],
                    'cache_path' => '/var/test',
                ],
                'The value 0 is too small for path "command_supervisor.commands.command:name.threshold". ' .
                'Should be greater than or equal to 1'
            ],
        ];
    }

    public function testShouldAssembleConfigurationAndGetDefaultValues()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(
            new Configuration(),
            [
                [
                    'commands' => [
                        ['name' => 'command:name', 'threshold' => 10]
                    ],
                    'cache_path' => '/var/test',
                ]
            ]
        );

        $this->assertSame(['command:name' => ['threshold' => 10, 'handler' => 'default']], $config['commands']);
        $this->assertSame('/var/test', $config['cache_path']);
        $this->assertSame('command_supervisor.handler.swift_mailer_handler', $config['default_handler']);
        $this->assertEmpty($config['handlers']);
    }

    public function testShouldAssembleConfiguration()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(
            new Configuration(),
            [
                [
                    'commands' => [
                        ['name' => 'command:name', 'threshold' => 10],
                        ['name' => 'doctrine:migration:diff', 'threshold' => 3600, 'handler' => 'awesome_handler'],
                    ],
                    'cache_path' => '/var/cache/supervisor',
                    'default_handler' => 'my_custom_handler.service_id',
                    'handlers' => [
                        'awesome_handler' => 'awesome_handler.service_id',
                    ],
                ]
            ]
        );

        $this->assertSame(
            [
                'command:name' => ['threshold' => 10, 'handler' => 'default'],
                'doctrine:migration:diff' => ['threshold' => 3600, 'handler' => 'awesome_handler'],
            ],
            $config['commands']
        );
        $this->assertSame('/var/cache/supervisor', $config['cache_path']);
        $this->assertSame('my_custom_handler.service_id', $config['default_handler']);
        $this->assertSame(['awesome_handler' => 'awesome_handler.service_id'], $config['handlers']);
    }
}
