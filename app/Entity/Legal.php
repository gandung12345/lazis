<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Entity\GeneratedError;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Mapper\Query\Error as QueryError;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'legal')]
#[GeneratedError(
    targetColumn: 'organization',
    sqlStatePrefix: QueryError::SQLSTATE_PREF27,
)]
#[OpenApi\Schema]
class Legal extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Legal ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'certificateNumberExpiredAt')]
    #[OpenApi\Property(
        property: 'certificateNumberExpiredAt',
        type: 'string',
        format: 'date',
        description: 'Certificate number expiration date'
    )]
    private DateTimeDecorator $certNumberExpired;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'certificateLicensingNumber')]
    #[OpenApi\Property(
        property: 'certificateLicensingNumber',
        type: 'string',
        description: 'Certificate licensing number'
    )]
    private string $certLicensingNumber;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'operationCertificateNumberExpiredAt')]
    #[OpenApi\Property(
        property: 'operationCertificateNumberExpiredAt',
        type: 'string',
        format: 'date',
        description: 'Operation certificate number expiration date'
    )]
    private DateTimeDecorator $opCertNumberExpired;

    #[Column(type: 'string', nullable: false)]
    #[Json(name: 'operationCertificateLicensingNumber')]
    #[OpenApi\Property(
        property: 'operationCertificateLicensingNumber',
        type: 'string',
        description: 'Operation certificate licensing number'
    )]
    private string $opCertLicensingNumber;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'legalityDocument')]
    #[OpenApi\Property(
        property: 'legalityDocument',
        type: 'string',
        description: 'Legality document'
    )]
    private string $legalityDoc;

    #[OneToOne(targetEntity: Organization::class, inversedBy: 'legal')]
    #[JoinColumn(name: 'organizationRefId', referencedColumnName: 'id')]
    private Organization $organization;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getCertNumberExpired(): DateTimeDecorator
    {
        return $this->certNumberExpired;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator $certNumberExpired
     * @return void
     */
    public function setCertNumberExpired(DateTimeDecorator $certNumberExpired): void
    {
        $this->certNumberExpired = $certNumberExpired;
    }

    /**
     * @return string
     */
    public function getCertLicensingNumber(): string
    {
        return $this->certLicensingNumber;
    }

    /**
     * @param string $certLicensingNumber
     * @return void
     */
    public function setCertLicensingNumber(string $certLicensingNumber): void
    {
        $this->certLicensingNumber = $certLicensingNumber;
    }

    /**
     * @return Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getOpCertNumberExpired(): DateTimeDecorator
    {
        return $this->opCertNumberExpired;
    }

    /**
     * @param Schnell\Decorator\Stringified\DateTimeDecorator $opCertNumberExpired
     * @return void
     */
    public function setOpCertNumberExpired(DateTimeDecorator $opCertNumberExpired): void
    {
        $this->opCertNumberExpired = $opCertNumberExpired;
    }

    /**
     * @return string
     */
    public function getOpCertLicensingNumber(): string
    {
        return $this->opCertLicensingNumber;
    }

    /**
     * @param string $opCertLicensingNumber
     * @return void
     */
    public function setOpCertLicensingNumber(string $opCertLicensingNumber): void
    {
        $this->opCertLicensingNumber = $opCertLicensingNumber;
    }

    /**
     * @return string
     */
    public function getLegalityDoc(): string
    {
        return $this->legalityDoc;
    }

    /**
     * @param string $legalityDoc
     * @return void
     */
    public function setLegalityDoc(string $legalityDoc): void
    {
        $this->legalityDoc = $legalityDoc;
    }

    /**
     * @return Lazis\Api\Entity\Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @param Lazis\Api\Entity\Organization $organization
     * @return void
     */
    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__legal__';
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'legal';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Legal::class;
    }
}
