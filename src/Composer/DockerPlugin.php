<?php

namespace Phpguild\Docker\Composer;

use Composer\Composer;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
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
     */
    public function runScheduledTasks(): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $appDir = $vendorDir . '/..';
        $pluginDir = realpath(__DIR__) . '/../..';
        $dataDir = $pluginDir . '/data';

        $this->io->write('');
        if (file_exists($appDir . '/docker-compose.yml')) {
            $this->io->write('<fg=blue>[✓] Already exists docker-compose</fg=blue>');
            $this->io->write('');
            return;
        }

        (new Filesystem())->mirror($dataDir, $appDir);

        $this->updateGitFile($appDir . '/.gitignore');
        $this->updateEnvFile($appDir . '/.env');

        $this->io->write('<fg=green>[✓] Install docker-compose</fg=green>');
        $this->io->write('');
    }

    /**
     * updateEnvFile
     *
     * @param string $file
     */
    private function updateEnvFile(string $file): void
    {
        $data = file_exists($file) ? file_get_contents($file) : '';
        if (!preg_match('/###> phpguild\/docker-web-standard ###/', $data)) {
            $data .=
                PHP_EOL .
                '###> phpguild/docker-web-standard ###' . PHP_EOL .
                'APP_PORT=8000' . PHP_EOL .
                'APP_INSTANCE=live' . PHP_EOL .
                'COMPOSE_PROJECT_NAME=myapp_live' . PHP_EOL .
                'COMPOSE_FILE=docker-compose.yml' . PHP_EOL .
                'MYSQL_ROOT_PASSWORD=password' . PHP_EOL .
                'MYSQL_DATABASE=myapp' . PHP_EOL .
                '###< phpguild/docker-web-standard ###' . PHP_EOL
            ;
            file_put_contents($file, $data);
        }
    }

    /**
     * updateGitFile
     *
     * @param string $file
     */
    private function updateGitFile(string $file): void
    {
        $data = file_exists($file) ? file_get_contents($file) : '';
        if (!preg_match('/###> phpguild\/docker-web-standard ###/', $data)) {
            $data .=
                PHP_EOL .
                '###> phpguild/docker-web-standard ###' . PHP_EOL .
                '/data/' . PHP_EOL .
                '###< phpguild/docker-web-standard ###' . PHP_EOL
            ;
            file_put_contents($file, $data);
        }
    }
}
