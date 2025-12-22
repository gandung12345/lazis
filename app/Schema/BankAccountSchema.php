<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Schnell\Attribute\Schema\Json;
use Schnell\Attribute\Schema\Rule;
use Schnell\Schema\AbstractSchema;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class BankAccountSchema extends AbstractSchema
{
    #[Rule(required: true)]
    #[Json(name: 'accountNumber')]
    private ?string $accountNumber;

    #[Rule(required: true)]
    #[Json(name: 'bankName')]
    private ?string $bankName;

    #[Rule(required: true)]
    #[Json(name: 'accountHolderName')]
    private ?string $accountHolderName;

    /**
     * @param string|null $accountNumber
     * @param string|null $bankName
     * @param string|null $accountHolderName
     * @return static
     */
    public function __construct(
        ?string $accountNumber = null,
        ?string $bankName = null,
        ?string $accountHolderName = null
    ) {
        $this->setAccountNumber($accountNumber);
        $this->setBankName($bankName);
        $this->setAccountHolderName($accountHolderName);
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    /**
     * @param string|null $accountNumber
     * @return void
     */
    public function setAccountNumber(?string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string|null
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * @param string|null $bankName
     * @return void
     */
    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string|null
     */
    public function getAccountHolderName(): ?string
    {
        return $this->accountHolderName;
    }

    /**
     * @param string|null $accountHolderName
     * @return void
     */
    public function setAccountHolderName(?string $accountHolderName): void
    {
        $this->accountHolderName = $accountHolderName;
    }
}
