<?php

namespace Pantheon\Terminus\UnitTests\Commands\Auth;

use Pantheon\Terminus\Commands\MachineToken\ListCommand;
use Robo\Config;
use Pantheon\Terminus\Collections\MachineTokens;
use Pantheon\Terminus\Models\MachineToken;

/**
 * Class MachineTokensListCommandTest
 * Testing class for Pantheon\Terminus\Commands\Auth\LoginCommand
 * @package Pantheon\Terminus\UnitTests\Commands\Auth
 */
class MachineTokensListCommandTest extends MachineTokenCommandTest
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new ListCommand(new Config());
        $this->command->setSession($this->session);
        $this->command->setLogger($this->logger);
    }

    /**
     * Tests the machine-token:list command when there are no tokens
     */
    public function testMachineTokenListEmpty()
    {
        $this->machine_tokens->method('all')
            ->willReturn([]);

        $this->logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo('warning'), $this->equalTo('You have no machine tokens.'));

        $out = $this->command->listTokens();
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);
        $this->assertEquals([], $out->getArrayCopy());
    }

    /**
     * Tests the machine-token:list command when there are tokens
     */
    public function testMachineTokenListNotEmpty()
    {
        $tokens = [
            ['id' => '1', 'device_name' => 'Foo'],
            ['id' => '2', 'device_name' => 'Bar']
        ];
        $collection = new MachineTokens(['user' => $this->user]);
        $this->machine_tokens->method('all')
            ->willReturn([
                new MachineToken((object)$tokens[0], ['collection' => $collection]),
                new MachineToken((object)$tokens[1], ['collection' => $collection])
            ]);

        $this->logger->expects($this->never())
            ->method($this->anything());

        $out = $this->command->listTokens();
        $this->assertInstanceOf('Consolidation\OutputFormatters\StructuredData\RowsOfFields', $out);
        $this->assertEquals($tokens, $out->getArrayCopy());
    }
}
