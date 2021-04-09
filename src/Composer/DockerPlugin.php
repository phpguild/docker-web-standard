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

        if (file_exists($appDir . '/docker-compose.yml')) {
            return;
        }

        $this->config = [
            'APP_PORT_LOCAL' => random_int(8000, 8999),
            'MYSQL_PORT_LOCAL' => random_int(33000, 33999),
        ];

        (new Filesystem())->mirror($installDir, $appDir);

        $this->updateEnvFile($appDir . '/.env');
        $this->updateEnvLocalFile($appDir . '/.env.local');

        $this->searchAndReplace(
            $appDir . '/docker-compose.local.yml',
            '33306:3306',
            $this->config['MYSQL_PORT_LOCAL'] . ':3306'
        );

        $this->searchAndReplace(
            $appDir . '/config/nginx/proxies/local.conf',
            '127.0.0.1:8000',
            '127.0.0.1:' . $this->config['APP_PORT_LOCAL']
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
        file_put_contents($file, str_replace($search, $replace, file_get_contents($file)));
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
                'APP_NAME=__dwsmyapp__' . PHP_EOL .
                'APP_ENV=prod' . PHP_EOL .
                'APP_DEBUG=0' . PHP_EOL .
                'APP_SECRET=<insecure>' . PHP_EOL .
                'APP_PORT=8000' . PHP_EOL .
                'APP_INSTANCE=live' . PHP_EOL .
                'APP_TZ=Europe/Paris' . PHP_EOL .
                'APP_UID=1000' . PHP_EOL .
                'APP_DUMP_HOURLY=0' . PHP_EOL .
                'COMPOSE_FILE=docker-compose.yml' . PHP_EOL .
                'MYSQL_ROOT_PASSWORD=<insecure>' . PHP_EOL .
                'MYSQL_USER=__dwsmyapp__' . PHP_EOL .
                'MYSQL_PASSWORD=<insecure>' . PHP_EOL .
                'MYSQL_DATABASE=__dwsmyapp__' . PHP_EOL .
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
                'APP_DEBUG=1' . PHP_EOL .
                'APP_PORT=' . $this->config['APP_PORT_LOCAL'] . PHP_EOL .
                'APP_INSTANCE=local' . PHP_EOL .
                'COMPOSE_FILE=docker-compose.local.yml' . PHP_EOL .
                '###< phpguild/docker-web-standard ###' . PHP_EOL
            ;
            file_put_contents($file, $data);
        }
    }
}
