<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class LegalSchema extends AbstractSchema
{
    use SchemaTrait;

    #[Json(name: 'certificateNumberExpiredAt')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $certNumberExpired;

    #[Json(name: 'certificateLicensingNumber')]
    #[Rule(required: true)]
    private ?string $certLicensingNumber;

    #[Json(name: 'operationCertificateNumberExpiredAt')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[Rule(required: true)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $opCertNumberExpired;

    #[Json(name: 'operationCertificateLicensingNumber')]
    #[Rule(required: true)]
    private ?string $opCertLicensingNumber;

    #[Json(name: 'legalityDocument')]
    #[Rule(required: true)]
    private ?string $legalityDoc;

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $certNumberExpired
     * @param string|null $certLicensingNumber
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $opCertNumberExpired
     * @param string|null $opCertLicensingNumber
     * @param string|null $legalityDoc
     * @return static
     */
    public function __construct(
        ?DateTimeDecorator $certNumberExpired = null,
        ?string $certLicensingNumber = null,
        ?DateTimeDecorator $opCertNumberExpired = null,
        ?string $opCertLicensingNumber = null,
        ?string $legalityDoc = null
    ) {
        $this->setCertNumberExpired($certNumberExpired);
        $this->setCertLicensingNumber($certLicensingNumber);
        $this->setOpCertNumberExpired($opCertNumberExpired);
        $this->setOpCertLicensingNumber($opCertLicensingNumber);
        $this->setLegalityDoc($legalityDoc);
    }

    /**
     * @return Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getCertNumberExpired(): ?DateTimeDecorator
    {
        return $this->certNumberExpired;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $certNumberExpired
     * @return void
     */
    public function setCertNumberExpired(?DateTimeDecorator $certNumberExpired): void
    {
        $this->certNumberExpired = $certNumberExpired;
    }

    /**
     * @return string|null
     */
    public function getCertLicensingNumber(): ?string
    {
        return $this->certLicensingNumber;
    }

    /**
     * @param string|null $certLicensingNumber
     * @return void
     */
    public function setCertLicensingNumber(?string $certLicensingNumber): void
    {
        $this->certLicensingNumber = $certLicensingNumber;
    }

    /**
     * @return Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getOpCertNumberExpired(): ?DateTimeDecorator
    {
        return $this->opCertNumberExpired;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator|null $opCertNumberExpired
     * @return void
     */
    public function setOpCertNumberExpired(?DateTimeDecorator $opCertNumberExpired): void
    {
        $this->opCertNumberExpired = $opCertNumberExpired;
    }

    /**
     * @return string|null
     */
    public function getOpCertLicensingNumber(): ?string
    {
        return $this->opCertLicensingNumber;
    }

    /**
     * @param string|null $opCertLicensingNumber
     * @return void
     */
    public function setOpCertLicensingNumber(?string $opCertLicensingNumber): void
    {
        $this->opCertLicensingNumber = $opCertLicensingNumber;
    }

    /**
     * @return string|null
     */
    public function getLegalityDoc(): ?string
    {
        return $this->legalityDoc;
    }

    /**
     * @param string|null $legalityDoc
     * @return void
     */
    public function setLegalityDoc(?string $legalityDoc): void
    {
        $this->legalityDoc = $legalityDoc;
    }
}
