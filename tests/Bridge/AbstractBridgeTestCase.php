<?php

declare(strict_types=1);

namespace Schnell\Tests\Bridge;

use Schnell\Container;
use Schnell\ContainerInterface;
use Schnell\Config\Lexer;
use Schnell\Config\Parser;
use Schnell\Config\Config;
use Schnell\Config\ConfigInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
abstract class AbstractBridgeTestCase extends TestCase
{
    /**
     * @var Schnell\Config\ConfigInterface|null
     */
    private $config;

    /**
     * @var Schnell\Config\ConfigInterface|null
     */
    private $anotherConfig;

    /**
     * @var Schnell\Container\ContainerInterface|null
     */
    private $container;

    /**
     * @var string
     */
    private $configBuffer = <<<CONF
    [database]
    driver = 'pdo_mysql'
    host = '127.0.0.1'
    port = 3306
    schema = 'foobar'
    user = 'foobar'
    password = 'foobar'
    charset = 'utf8'

    [doctrine]
    dev_mode = false
    cache_dir = 'var/Doctrine'
    metadata_dirs = ['app/Entity']
    CONF;

    private $anotherConfigBuffer = <<<CONF
    [database]
    driver = 'pdo_mysql'
    host = '127.0.0.1'
    port = 3306
    schema = 'foobar'
    user = 'foobar'
    password = 'foobar'
    charset = 'utf8'

    [doctrine]
    dev_mode = true
    cache_dir = 'var/Doctrine'
    metadata_dirs = ['app/Entity']
    CONF;

    /**
     * @return Schnell\Config\ConfigInterface
     */
    private function buildConfig(): ConfigInterface
    {
        $lexer = new Lexer($this->configBuffer);
        $lexer->lex();

        $parser = new Parser($lexer->getTokens());
        $parser->parse();

        return new Config($parser->ast());
    }

    /**
     * @return Schnell\Config\ConfigInterface
     */
    private function buildAnotherConfig(): ConfigInterface
    {
        $lexer = new Lexer($this->anotherConfigBuffer);
        $lexer->lex();

        $parser = new Parser($lexer->getTokens());
        $parser->parse();

        return new Config($parser->ast());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->config = $this->buildConfig();
        $this->anotherConfig = $this->buildAnotherConfig();
        $this->container = new Container();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->config = null;
        $this->anotherConfig = null;
        $this->container = null;
    }

    /**
     * @return Schnell\Config\ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @return Schnell\Config\ConfigInterface
     */
    public function getAnotherConfig(): ConfigInterface
    {
        return $this->anotherConfig;
    }

    /**
     * @return Schnell\ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
