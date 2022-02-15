<?php

declare(strict_types=1);

namespace Phpguild\Docker\Composer;

use Composer\Composer;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\IO\IOInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DockerPlugin
 */
class DockerPlugin implements PluginInterface, EventSubscriberInterface
{
    public const PACKAGE_NAME = 'phpguild/docker';

    /** @var Composer $composer */
    protected $composer;

    /** @var IOInterface $io */
    protected $io;

    /** @var array $config */
    protected $config;

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
     * uninstall
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * deactivate
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
        // TODO: Implement deactivate() method.
    }

    /**
     * getSubscribedEvents
     *
     * @return array[]
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
        $installDir = $pluginDir . '/.install';

        if (file_exists($appDir . '/docker-compose.yml') || file_exists($appDir . '/docker-compose.yaml')) {
            return;
        }

        $this->config = [
            'APP_PORT_LOCAL' => random_int(8000, 8999),
            'MYSQL_PORT_LOCAL' => random_int(33000, 33999),
        ];

        (new Filesystem())->mirror($installDir, $appDir);

        $this->updateFile($appDir . '/.env', [
            'APP_NAME=__dwsmyapp__',
            'APP_ENV=prod',
            'APP_INSTANCE=live',
            'APP_DEBUG=0',
            'APP_SECRET=<secret>',
            'APP_PORT=8000',
            'APP_UID=1000',
            'APP_GID=33',
            'APP_USER=www-data',
            'APP_DUMP_HOURLY=0',
            'TZ=Europe/Paris',
            'MYSQL_ROOT_PASSWORD=<secret>',
            'MYSQL_USER=__dwsmyapp__',
            'MYSQL_PASSWORD=<secret>',
            'MYSQL_DATABASE=__dwsmyapp__',
            '#POSTGRES_USER=__dwsmyapp__',
            '#POSTGRES_PASSWORD=<secret>',
            '#POSTGRES_DB=__dwsmyapp__',
            'BLACKFIRE_ENABLE=0',
            'BLACKFIRE_LOG_LEVEL=4',
            'BLACKFIRE_CLIENT_ID=',
            'BLACKFIRE_CLIENT_TOKEN=',
            'BLACKFIRE_SERVER_ID=',
            'BLACKFIRE_SERVER_TOKEN=',
        ]);

        $this->updateFile($appDir . '/.env.local', [
            'APP_ENV=dev',
            'APP_INSTANCE=local',
            'APP_DEBUG=1',
            'APP_PORT=' . $this->config['APP_PORT_LOCAL'],
        ]);

        $this->updateFile($appDir . '/.gitignore', [
            'docker-compose.override.yml',
            'docker-compose.override.yaml',
        ]);

        $this->searchAndReplace(
            $appDir . '/docker-compose.override.yaml',
            '33306:3306',
            $this->config['MYSQL_PORT_LOCAL'] . ':3306'
        );

        $this->searchAndReplace(
            $appDir . '/config/nginx/proxies/local.conf',
            '127.0.0.1:8000',
            '127.0.0.1:' . $this->config['APP_PORT_LOCAL']
        );

        $this->searchAndReplace(
            $appDir . '/README.md',
            '{custom_local_port}',
            $this->config['APP_PORT_LOCAL']
        );

        $this->io->write('  - Installing recipe <fg=green>docker-compose</fg=green> ');
    }

    /**
     * searchAndReplace
     *
     * @param string $file
     * @param string $search
     * @param string $replace
     */
    private function searchAndReplace(string $file, string $search, string $replace): void
    {
        if (!file_exists($file)) {
            return;
        }

        file_put_contents($file, str_replace($search, $replace, file_get_contents($file)));
    }

    /**
     * updateFile
     *
     * @param string $file
     * @param array  $items
     */
    private function updateFile(string $file, array $items): void
    {
        $data = file_exists($file) ? file_get_contents($file) : '';

        if (preg_match('/###> phpguild\/docker-web-standard ###/', $data)) {
            return;
        }

        $data .=
            PHP_EOL .
            '###> phpguild/docker-web-standard ###' . PHP_EOL .
            implode(PHP_EOL, $items) .
            '###< phpguild/docker-web-standard ###' . PHP_EOL
        ;

        file_put_contents($file, $data);
    }
}
