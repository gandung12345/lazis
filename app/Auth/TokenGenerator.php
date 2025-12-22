<?php

declare(strict_types=1);

namespace Lazis\Api\Auth;

use DateTimeImmutable;
use Doctrine\ORM\Query\Expr;
use Lazis\Api\Entity\Organization;
use Lazis\Api\Entity\Organizer;
use Lazis\Api\Entity\Users;
use Lazis\Api\Schema\TokenSchema;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Schnell\ContainerInterface;
use Schnell\Config\ConfigInterface;
use Schnell\Entity\EntityInterface;

use function password_verify;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @var \Schnell\ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var \Schnell\Config\ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @param \Schnell\ContainerInterface $container
     * @param \Schnell\Config\ConfigInterface $config
     * @return static
     */
    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->setContainer($container);
        $this->setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(TokenSchema $token): ?TokenObject
    {
        $isValid = $this->checkCredential(
            new Organizer(),
            new Users(),
            $token
        );

        if (false === $isValid) {
            return null;
        }

        $authData = $this->getOrganizerAndUsersFromTokenSchema(
            new Organizer(),
            new Users(),
            $token
        );

        if (null === $authData) {
            return null;
        }

        $organization = $this->getOrganizationFromOrganizerObject(
            $authData['organizer']
        );

        if (null === $organization) {
            return null;
        }

        $now = new DateTimeImmutable();
        $tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        $token = $tokenBuilder
            ->identifiedBy(sprintf('%s.%s', uniqid(), uniqid()))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('data', [
                'email' => $token->getEmail(),
                'id' => $authData['organizer']->getId(),
                'role' => $authData['organizer']->getRole(),
                'scope' => $authData['organizer']->getScope(),
                'name' => $authData['organizer']->getName(),
                'userId' => $authData['users']->getId(),
                'userScope' => $authData['users']->getRole(),
                'organization' => [
                    'id' => $organization->getId(),
                    'name' => $organization->getName(),
                    'address' => $organization->getAddress(),
                    'phoneNumber' => $organization->getPhoneNumber(),
                    'email' => $organization->getEmail(),
                    'scope' => $organization->getScope(),
                    'district' => $organization->getDistrict(),
                    'village' => $organization->getVillage()
                ]
            ])
            ->getToken(
                new Sha256(),
                InMemory::plainText($this->getConfig()->get('app.secret'))
            );

        $tokenObject = new TokenObject();
        $tokenObject->setToken($token->toString());
        $tokenObject->setLifetime($this->getConfig()->get('cache.defaultLifetime'));

        return $tokenObject;
    }

    /**
     * @param \Schnell\Entity\EntityInterface $parent
     * @param \Schnell\Entity\EntityInterface $child
     * @param \Lazis\Api\Schema\TokenSchema $token
     * @return bool
     */
    private function checkCredential(
        EntityInterface $parent,
        EntityInterface $child,
        TokenSchema $token
    ): bool {
        $entityManager = $this->getContainer()
            ->get('mapper')
            ->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($child->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organizer',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.email', $parent->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $token->getEmail())
            ->getQuery()
            ->getResult();

        if (sizeof($result) !== 1) {
            return false;
        }

        return password_verify($token->getPassword(), $result[0]->getPassword());
    }

    /**
     * @param \Schnell\Entity\EntityInterface $parent
     * @param \Schnell\Entity\EntityInterface $child
     * @param \Lazis\Api\Schema\TokenSchema $token
     * @return array|null
     */
    private function getOrganizerAndUsersFromTokenSchema(
        EntityInterface $parent,
        EntityInterface $child,
        TokenSchema $token
    ): ?array {
        $entityManager = $this->getContainer()
            ->get('mapper')
            ->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select([
                $parent->getQueryBuilderAlias(),
                $child->getQueryBuilderAlias()
            ])
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $child->getDqlName(),
                $child->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organizer',
                    $parent->getQueryBuilderAlias(),
                    $child->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.email', $parent->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $token->getEmail())
            ->getQuery()
            ->getResult();

        if (sizeof($result) !== 2) {
            return null;
        }

        return ['organizer' => $result[0], 'users' => $result[1]];
    }

    /**
     * @param \Schnell\Entity\EntityInterface $entity
     * @return \Schnell\Entity\EntityInterface|null
     */
    public function getOrganizationFromOrganizerObject(
        EntityInterface $entity
    ): ?EntityInterface {
        $parent = new Organization();
        $entityManager = $this->getContainer()
            ->get('mapper')
            ->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder
            ->select($parent->getQueryBuilderAlias())
            ->from($parent->getDqlName(), $parent->getQueryBuilderAlias())
            ->join(
                $entity->getDqlName(),
                $entity->getQueryBuilderAlias(),
                Expr\Join::WITH,
                sprintf(
                    '%s.id = %s.organization',
                    $parent->getQueryBuilderAlias(),
                    $entity->getQueryBuilderAlias()
                )
            )
            ->where($queryBuilder->expr()->eq(
                sprintf('%s.id', $entity->getQueryBuilderAlias()),
                '?1'
            ))
            ->setParameter(1, $entity->getId())
            ->getQuery()
            ->getResult();

        if (sizeof($result) !== 1) {
            return null;
        }

        return $result[0];
    }
}
