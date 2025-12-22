<?php

declare(strict_types=1);

namespace Lazis\Api\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\PersistentCollection;
use OpenApi\Attributes as OpenApi;
use Schnell\Attribute\Entity\GeneratedError;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;
use Schnell\Decorator\Stringified\DateTimeDecorator;
use Schnell\Mapper\Query\Error as QueryError;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
#[Entity, Table(name: 'amil')]
#[GeneratedError(
    targetColumn: 'organizer',
    sqlStatePrefix: QueryError::SQLSTATE_PREF27
)]
#[OpenApi\Schema]
class Amil extends AbstractEntity
{
    #[Id]
    #[Column(type: 'guid', nullable: false, unique: true)]
    #[Json(name: 'id')]
    #[OpenApi\Property(
        property: 'id',
        type: 'string',
        description: 'Amil ID',
        readOnly: true
    )]
    private string $id;

    #[Column(type: 'date', nullable: false)]
    #[Json(name: 'licenseOutdatedAt')]
    #[OpenApi\Property(
        property: 'licenseOutdatedAt',
        type: 'string',
        format: 'date',
        description: 'Amil date of birth'
    )]
    private DateTimeDecorator $licenseOutdatedAt;

    #[Column(type: 'text', nullable: false)]
    #[Json(name: 'licenseDocument')]
    #[OpenApi\Property(
        property: 'licenseDocument',
        type: 'string',
        description: 'Amil license document'
    )]
    private string $licenseDoc;

    #[OneToOne(
        targetEntity: Organizer::class,
        inversedBy: 'amil',
        cascade: ['persist']
    )]
    #[JoinColumn(name: 'organizerRefId', referencedColumnName: 'id')]
    private Organizer $organizer;

    #[OneToMany(
        targetEntity: AmilFunding::class,
        mappedBy: 'amil',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $amilFundings;

    #[OneToMany(
        targetEntity: Dskl::class,
        mappedBy: 'amil',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $dskls;

    #[OneToMany(
        targetEntity: Infaq::class,
        mappedBy: 'amil',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $infaqs;

    #[OneToMany(
        targetEntity: Zakat::class,
        mappedBy: 'amil',
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private PersistentCollection $zakats;

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
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator
     */
    public function getLicenseOutdatedAt(): DateTimeDecorator
    {
        return $this->licenseOutdatedAt;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator $licenseOutdatedAt
     * @return void
     */
    public function setLicenseOutdatedAt(DateTimeDecorator $licenseOutdatedAt): void
    {
        $this->licenseOutdatedAt = $licenseOutdatedAt;
    }

    /**
     * @return string
     */
    public function getLicenseDoc(): string
    {
        return $this->licenseDoc;
    }

    /**
     * @param string $licenseDoc
     * @return void
     */
    public function setLicenseDoc(string $licenseDoc): void
    {
        $this->licenseDoc = $licenseDoc;
    }

    /**
     * @return Lazis\Api\Entity\Organizer
     */
    public function getOrganizer(): Organizer
    {
        return $this->organizer;
    }

    /**
     * @param Lazis\Api\Entity\Organizer $organizer
     * @return void
     */
    public function setOrganizer(Organizer $organizer): void
    {
        $this->organizer = $organizer;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getAmilFundings(): PersistentCollection
    {
        return $this->amilFundings;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $amilFundings
     * @return void
     */
    public function setAmilFundings(PersistentCollection $amilFundings): void
    {
        $this->amilFundings = $amilFundings;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getDskls(): PersistentCollection
    {
        return $this->dskls;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $dskls
     * @return void
     */
    public function setDskls(PersistentCollection $dskls): void
    {
        $this->dskls = $dskls;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getInfaqs(): PersistentCollection
    {
        return $this->infaqs;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $infaqs
     * @return void
     */
    public function setInfaqs(PersistentCollection $infaqs): void
    {
        $this->infaqs = $infaqs;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getZakats(): PersistentCollection
    {
        return $this->zakats;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $zakats
     * @return void
     */
    public function setZakats(PersistentCollection $zakats): void
    {
        $this->zakats = $zakats;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        return '__amil__';
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        return 'amil';
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        return Amil::class;
    }
}
