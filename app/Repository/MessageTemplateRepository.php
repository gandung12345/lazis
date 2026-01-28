<?php

declare(strict_types=1);

namespace Lazis\Api\Repository;

use Lazis\Api\Type\MessageTemplate as MessageTemplateType;
use Schnell\Repository\AbstractRepository;
use Schnell\Schema\SchemaInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class MessageTemplateRepository extends AbstractRepository
{
    private array $messageTemplateKeyMap = [
        MessageTemplateType::ZAKAT_MAAL => 'zakatMaal',
        MessageTemplateType::ZAKAT_FITRAH => 'zakatFitrah',
        MessageTemplateType::NU_COIN => 'nuCoin',
        MessageTemplateType::INFAQ => 'infaq'
    ];

    /**
     * @return array
     */
    public function getAll(): array
    {
        return json_decode(file_get_contents('notification-message/notification.json'), true);
    }

    /**
     * @param \Schnell\Schema\SchemaInterface $schema
     * @return array
     */
    public function createOrUpdate(SchemaInterface $schema): array
    {
        $data = json_decode(file_get_contents('notification-message/notification.json'), true);
        $key  = $this->messageTemplateKeyMap[$schema->getType()];

        $data[$key] = $schema->getMessage();

        file_put_contents('notification-message/notification.json', json_encode($data));

        return [$key => $schema->getMessage()];
    }
}
