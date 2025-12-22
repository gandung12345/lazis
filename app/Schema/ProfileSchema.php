<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Enum;
use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Regex;
use Schnell\Attribute\Schema\Rule;
use Schnell\Attribute\Schema\TransformedClassType;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class ProfileSchema extends AbstractSchema
{
    use SchemaTrait;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'name')]
    private ?string $name;

    /**
     * @var string|null
     */
    #[Regex(pattern: self::EMAIL_PATTERN)]
    #[Rule(required: true)]
    #[Json(name: 'email')]
    private ?string $email;

    /**
     * @var string|null
     */
    #[Rule(required: true)]
    #[Json(name: 'identityNumber')]
    private ?string $identityNumber;

    /**
     * @var \Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     */
    #[Rule(required: true)]
    #[Json(name: 'dateOfBirth')]
    #[Regex(pattern: self::ISO8601_DATE_PATTERN)]
    #[TransformedClassType(name: DateTimeDecorator::class)]
    private ?DateTimeDecorator $dateOfBirth;

    /**
     * @var string|null $phoneNumber
     */
    #[Rule(required: true)]
    #[Json(name: 'phoneNumber')]
    #[Regex(pattern: self::PHONE_NUMBER_PATTERN)]
    private ?string $phoneNumber;

    /**
     * @var string|null $sex
     */
    #[Rule(required: true)]
    #[Json(name: 'sex')]
    #[Enum(value: self::SEX_LIST)]
    private ?string $sex;

    /**
     * @var string|null $address
     */
    #[Rule(required: true)]
    #[Json(name: 'address')]
    private ?string $address;

    /**
     * @var string|null $education
     */
    #[Rule(required: true)]
    #[Json(name: 'education')]
    private ?string $education;

    /**
     * @var int|null $role
     */
    #[Rule(required: true)]
    #[Json(name: 'role')]
    private ?int $role;

    /**
     * @var string|null $photo
     */
    #[Rule(required: true)]
    #[Json(name: 'photo')]
    private ?string $photo;

    /**
     * @var string|null $identityPhoto
     */
    #[Rule(required: true)]
    #[Json(name: 'identityPhoto')]
    private ?string $identityPhoto;

    /**
     * @var string|null $qrCode
     */
    #[Rule(required: true)]
    #[Json(name: 'qrCode')]
    private ?string $qrCode;

    /**
     * @var \Lazis\Api\Schema\UsersSchema
     */
    #[Rule(required: true)]
    #[Json(name: '@user')]
    private ?UsersSchema $user;

    /**
     * @param string|null $name
     * @param string|null $email
     * @param string|null $identityNumber
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     * @param string|null $phoneNumber
     * @param string|null $sex
     * @param string|null $address
     * @param string|null $education
     * @param int|null $role
     * @param string|null $photo
     * @param string|null $identityPhoto
     * @param string|null $qrCode
     * @param \Lazis\Api\Schema\UsersSchema|null $user
     * @return static
     */
    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $identityNumber = null,
        ?DateTimeDecorator $dateOfBirth = null,
        ?string $phoneNumber = null,
        ?string $sex = null,
        ?string $address = null,
        ?string $education = null,
        ?int $role = null,
        ?string $photo = null,
        ?string $identityPhoto = null,
        ?string $qrCode = null,
        ?UsersSchema $user = null
    ) {
        $this->setName($name);
        $this->setEmail($email);
        $this->setIdentityNumber($identityNumber);
        $this->setDateOfBirth($dateOfBirth);
        $this->setPhoneNumber($phoneNumber);
        $this->setSex($sex);
        $this->setAddress($address);
        $this->setEducation($education);
        $this->setRole($role);
        $this->setPhoto($photo);
        $this->setIdentityPhoto($identityPhoto);
        $this->setQrCode($qrCode);
        $this->setUser($user);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getIdentityNumber(): ?string
    {
        return $this->identityNumber;
    }

    /**
     * @param string|null $identityNumber
     * @return void
     */
    public function setIdentityNumber(?string $identityNumber): void
    {
        $this->identityNumber = $identityNumber;
    }

    /**
     * @return \Schnell\Decorator\Stringified\DateTimeDecorator|null
     */
    public function getDateOfBirth(): ?DateTimeDecorator
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \Schnell\Decorator\Stringified\DateTimeDecorator|null $dateOfBirth
     * @return void
     */
    public function setDateOfBirth(?DateTimeDecorator $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return void
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getSex(): ?string
    {
        return $this->sex;
    }

    /**
     * @param string|null $sex
     * @return void
     */
    public function setSex(?string $sex): void
    {
        $this->sex = $sex;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return void
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getEducation(): ?string
    {
        return $this->education;
    }

    /**
     * @param string|null $education
     * @return void
     */
    public function setEducation(?string $education): void
    {
        $this->education = $education;
    }

    /**
     * @return int|null
     */
    public function getRole(): ?int
    {
        return $this->role;
    }

    /**
     * @param int|null $role
     * @return void
     */
    public function setRole(?int $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return void
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return string|null
     */
    public function getIdentityPhoto(): ?string
    {
        return $this->identityPhoto;
    }

    /**
     * @param string|null $identityPhoto
     * @return void
     */
    public function setIdentityPhoto(?string $identityPhoto): void
    {
        $this->identityPhoto = $identityPhoto;
    }

    /**
     * @return string|null
     */
    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    /**
     * @param string|null $qrCode
     * @return void
     */
    public function setQrCode(?string $qrCode): void
    {
        $this->qrCode = $qrCode;
    }

    /**
     * @return \Lazis\Api\Schema\UsersSchema|null
     */
    public function getUser(): ?UsersSchema
    {
        return $this->user;
    }

    /**
     * @param \Lazis\Api\Schema\UsersSchema|null $user
     * @return void
     */
    public function setUser(?UsersSchema $user): void
    {
        $this->user = $user;
    }
}
