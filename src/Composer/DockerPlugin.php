<?php

namespace Phpguild\Docker\Composer;

use Composer\Composer;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Composer\IO\IOInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DockerPlugin
 * @package Phpguild\Docker\Composer
 */
class DockerPlugin implements PluginInterface, EventSubscriberInterface
{
    public const PACKAGE_NAME = 'phpguild/docker';

    /** @var Composer */
    protected $composer;

    /** @var IOInterface */
    protected $io;

    /**
     * activate
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_INSTALL_CMD => 'runScheduledTasks',
            ScriptEvents::POST_UPDATE_CMD => 'runScheduledTasks',
        ];
    }

    /**
     * runScheduledTasks
     *
     * @param Event $event
     */
    public function runScheduledTasks(Event $event): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $appDir = $vendorDir . '/..';
        $pluginDir = realpath(__DIR__) . '/../..';
        $dataDir = $pluginDir . '/data';

        $this->io->write('');

        if (!file_exists($appDir . '/docker-compose.yml')) {
            (new Filesystem())->mirror($dataDir, $appDir);
            $this->io->write('<fg=green>[✓] Install docker-compose</fg=green>');
        } else {
            $this->io->write('<fg=blue>[✓] Already exists docker-compose</fg=blue>');
        }

        $this->io->write('');
    }
}
