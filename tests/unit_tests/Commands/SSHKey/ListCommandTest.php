<?php

namespace Pantheon\Terminus\UnitTests\Commands\SSHKey;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Collections\SSHKeys;
use Pantheon\Terminus\Commands\SSHKey\ListCommand;
use Pantheon\Terminus\Models\SSHKey;

/**
 * Class ListCommandTest
 * Testing class for Pantheon\Terminus\Commands\SSHKey\ListCommand
 * @package Pantheon\Terminus\UnitTests\Commands\SSHKey
 */
class ListCommandTest extends SSHKeyCommandTest
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->command = new ListCommand($this->getConfig());
        $this->command->setSession($this->session);
        $this->command->setLogger($this->logger);
    }

    /**
     * Tests the ssh-key:list command when there are no tokens
     */
    public function testSSHKeyListEmpty()
    {
        $this->ssh_keys->method('all')
            ->willReturn([]);

        $this->logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo('warning'), $this->equalTo('You have no ssh keys.'));

        $out = $this->command->listSSHKeys();
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals([], $out->getArrayCopy());
    }

    /**
     * Tests the ssh-key:list command
     */
    public function testSSHKeysList()
    {
        $keys = [
            [
                'id' => '79e7e210bdf335bb8651a46b9a8417ab',
                'key' => 'ssh-rsa xxxxxxx dev@foo.bar',
            ],
            [
                'id' => '27a7a11ab9d2acbf91063410546ef980',
                'key' => 'ssh-rsa yyyyyyy dev@baz.bar',
            ]
        ];
        $output = [
            [
                'id' => '79e7e210bdf335bb8651a46b9a8417ab',
                'hex' => '79:e7:e2:10:bd:f3:35:bb:86:51:a4:6b:9a:84:17:ab',
                'comment' => 'dev@foo.bar'
            ],
            [
                'id' => '27a7a11ab9d2acbf91063410546ef980',
                'hex' => '27:a7:a1:1a:b9:d2:ac:bf:91:06:34:10:54:6e:f9:80',
                'comment' => 'dev@baz.bar'
            ]
        ];
        $collection = new SSHKeys(['user' => $this->user]);
        $this->ssh_keys->method('all')
            ->willReturn([
                new SSHKey((object)$keys[0], ['collection' => $collection]),
                new SSHKey((object)$keys[1], ['collection' => $collection])
            ]);

        $this->logger->expects($this->never())
            ->method($this->anything());

        $out = $this->command->listSSHKeys();
        $this->assertInstanceOf(RowsOfFields::class, $out);
        $this->assertEquals($output, $out->getArrayCopy());
    }
}
