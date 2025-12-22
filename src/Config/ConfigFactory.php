<?php

declare(strict_types=1);

namespace Schnell\Config;

use Schnell\Exception\ConfigException;

use function file_get_contents;
use function in_array;
use function sprintf;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ConfigFactory implements ConfigFactoryInterface
{
    /**
     * @var array
     */
    private $files;

    /**
     * @psalm-api
     *
     * @return static
     */
    public function __construct()
    {
        $this->files = [];
    }

    /**
     * {@inheritdoc}
     */
    public function import(string $filename): void
    {
        if (true === in_array($filename, $this->files, true)) {
            throw new ConfigException(
                sprintf("Config file with name '%s' exists.", $filename)
            );
        }

        $this->files[] = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function importBulk(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $this->import($filename);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        $lexer = new Lexer($this->collectBuffer());
        $lexer->lex();

        $parser = new Parser($lexer->getTokens());
        $parser->parse();

        return new Config($parser->ast());
    }

    /**
     * @return string
     */
    private function collectBuffer(): string
    {
        $buf = '';

        foreach ($this->files as $file) {
            if (($tmp = file_get_contents($file)) === false) {
                throw new ConfigException(
                    sprintf("Read buffer from file '%s' failed.", $file)
                );
            }

            $buf .= $tmp;
        }

        return $buf;
    }
}
