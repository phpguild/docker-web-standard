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
            ScriptEvents::POST_INSTALL_CMD => [ 'runScheduledTasks', 50 ],
            ScriptEvents::POST_UPDATE_CMD => [ 'runScheduledTasks', 50 ],
        ];
    }

    /**
     * runScheduledTasks
     *
     * @throws \Exception
     */
    public function runScheduledTasks(): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        $appDir = $vendorDir . '/..';
        $pluginDir = realpath(__DIR__) . '/../..';
        $installDir = $pluginDir . '/install';

        if (file_exists($appDir . '/docker-compose.yml')) {
            return;
        }

        (new Filesystem())->mirror($installDir, $appDir);

        $this->updateEnvFile($appDir . '/.env');
        $this->updateEnvLocalFile($appDir . '/.env.local');

        $dockerComposeLocal = file_get_contents($appDir . '/docker-compose.local.yml');
        file_put_contents(
            $appDir . '/docker-compose.local.yml',
            str_replace('3306:3306', random_int(33000, 33999) . ':3306', $dockerComposeLocal)
        );

        $this->io->write('  - Installing recipe <fg=green>docker-compose</fg=green> ');
    }

    /**
     * updateEnvFile
     *
     * @param string $file
     *
     * @throws \Exception
     */
    private function updateEnvFile(string $file): void
    {
        $data = file_exists($file) ? file_get_contents($file) : '';
        if (!preg_match('/###> phpguild\/docker-web-standard ###/', $data)) {
            $data .=
                PHP_EOL .
                '###> phpguild/docker-web-standard ###' . PHP_EOL .
                'APP_ENV=prod' . PHP_EOL .
                'APP_PORT=' . random_int(8000, 8999) . PHP_EOL .
                'APP_INSTANCE=live' . PHP_EOL .
                'APP_TZ=Europe/Paris' . PHP_EOL .
                'APP_UID=1000' . PHP_EOL .
                'COMPOSE_PROJECT_NAME=myapp_live' . PHP_EOL .
                'COMPOSE_FILE=docker-compose.yml' . PHP_EOL .
                'MYSQL_ROOT_PASSWORD=<insecure>' . PHP_EOL .
                'MYSQL_DATABASE=myapp' . PHP_EOL .
                '###< phpguild/docker-web-standard ###' . PHP_EOL
            ;
            file_put_contents($file, $data);
        }
    }

    /**
     * updateEnvLocalFile
     *
     * @param string $file
     *
     * @throws \Exception
     */
    private function updateEnvLocalFile(string $file): void
    {
        $data = file_exists($file) ? file_get_contents($file) : '';
        if (!preg_match('/###> phpguild\/docker-web-standard ###/', $data)) {
            $data .=
                PHP_EOL .
                '###> phpguild/docker-web-standard ###' . PHP_EOL .
                'APP_ENV=dev' . PHP_EOL .
                'APP_PORT=' . random_int(8000, 8999) . PHP_EOL .
                'APP_INSTANCE=local' . PHP_EOL .
                'COMPOSE_PROJECT_NAME=myapp_local' . PHP_EOL .
                'COMPOSE_FILE=docker-compose.local.yml' . PHP_EOL .
                '###< phpguild/docker-web-standard ###' . PHP_EOL
            ;
            file_put_contents($file, $data);
        }
    }
}
