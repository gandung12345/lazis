<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Rule(required: true)]
    #[Json(name: 'licenseOutdatedAt')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $licenseOutdatedAt;

    #[Rule(required: true)]
    #[Json(name: 'licenseDocument')]
    private ?string $licenseDoc;

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $licenseOutdatedAt
     * @param string|null $licenseDoc
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $licenseOutdatedAt = null,
        ?string $licenseDoc = null
    ) {
        $this->setLicenseOutdatedAt($licenseOutdatedAt);
        $this->setLicenseDoc($licenseDoc);
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getLicenseOutdatedAt(): ?DateTimeDecorator
    {
        return $this->licenseOutdatedAt;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $licenseOutdatedAt
     * @return void
     */
    public function setLicenseOutdatedAt(?DateTimeDecorator $licenseOutdatedAt): void
    {
        $this->licenseOutdatedAt = $licenseOutdatedAt;
    }

    /**
     * @return string|null
     */
    public function getLicenseDoc(): ?string
    {
        return $this->licenseDoc;
    }

    /**
     * @param string|null $licenseDoc
     * @return void
     */
    public function setLicenseDoc(?string $licenseDoc): void
    {
        $this->licenseDoc = $licenseDoc;
    }
}
