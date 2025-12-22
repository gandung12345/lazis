<?php

declare(strict_types=1);

namespace Lazis\Api\Entity\Report\Components;

use RuntimeException;
use Schnell\Attribute\Schema\Json;
use Schnell\Entity\AbstractEntity;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class AmilFundingUtilization extends AbstractEntity
{
    /**
     * @var int
     */
    #[Json(name: 'socializationAndEducationCost')]
    private int $socializationAndEducationCost;

    /**
     * @var int
     */
    #[Json(name: 'employeeExpenseCost')]
    private int $employeeExpenseCost;

    /**
     * @var int
     */
    #[Json(name: 'employeeSalary')]
    private int $employeeSalary;

    /**
     * @var int
     */
    #[Json(name: 'officeUtilityCost')]
    private int $officeUtilityCost;

    /**
     * @var int
     */
    #[Json(name: 'officeStationeryCost')]
    private int $officeStationeryCost;

    /**
     * @var int
     */
    #[Json(name: 'internetCost')]
    private int $internetCost;

    /**
     * @var int
     */
    #[Json(name: 'telephoneCost')]
    private int $telephoneCost;

    /**
     * @var int
     */
    #[Json(name: 'electricityCost')]
    private int $electricityCost;

    /**
     * @var int
     */
    #[Json(name: 'transportationCost')]
    private int $transportationCost;

    /**
     * @var int
     */
    #[Json(name: 'communicationCost')]
    private int $communicationCost;

    /**
     * @var int
     */
    #[Json(name: 'officeAssetMaintenanceCost')]
    private int $officeAssetMaintenanceCost;

    /**
     * @var int
     */
    #[Json(name: 'consumptionCost')]
    private int $consumptionCost;

    /**
     * @var int
     */
    #[Json(name: 'insuranceCost')]
    private int $insuranceCost;

    /**
     * @var int
     */
    #[Json(name: 'adminAndCommonCost')]
    private int $adminAndCommonCost;

    /**
     * @var int
     */
    #[Json(name: 'deprecationExpense')]
    private int $deprecationExpense;

    public function __construct()
    {
        $this->setSocializationAndEducationCost(0);
        $this->setEmployeeExpenseCost(0);
        $this->setEmployeeSalary(0);
        $this->setOfficeUtilityCost(0);
        $this->setOfficeStationeryCost(0);
        $this->setInternetCost(0);
        $this->setTelephoneCost(0);
        $this->setElectricityCost(0);
        $this->setTransportationCost(0);
        $this->setCommunicationCost(0);
        $this->setOfficeAssetMaintenanceCost(0);
        $this->setConsumptionCost(0);
        $this->setInsuranceCost(0);
        $this->setAdminAndCommonCost(0);
        $this->setDeprecationExpense(0);
    }

    /**
     * @return int
     */
    public function getSocializationAndEducationCost(): int
    {
        return $this->socializationAndEducationCost;
    }

    /**
     * @param int $socializationAndEducationCost
     * @return void
     */
    public function setSocializationAndEducationCost(int $socializationAndEducationCost): void
    {
        $this->socializationAndEducationCost = $socializationAndEducationCost;
    }

    /**
     * @param int $socializationAndEducationCost
     * @return void
     */
    public function addSocializationAndEducationCost(int $socializationAndEducationCost): void
    {
        $this->socializationAndEducationCost += $socializationAndEducationCost;
    }

    /**
     * @return int
     */
    public function getEmployeeExpenseCost(): int
    {
        return $this->employeeExpenseCost;
    }

    /**
     * @param int $employeeExpenseCost
     * @return void
     */
    public function setEmployeeExpenseCost(int $employeeExpenseCost): void
    {
        $this->employeeExpenseCost = $employeeExpenseCost;
    }

    /**
     * @param int $employeeExpenseCost
     * @return void
     */
    public function addEmployeeExpenseCost(int $employeeExpenseCost): void
    {
        $this->employeeExpenseCost += $employeeExpenseCost;
    }

    /**
     * @return int
     */
    public function getEmployeeSalary(): int
    {
        return $this->employeeSalary;
    }

    /**
     * @param int $employeeSalary
     * @return void
     */
    public function setEmployeeSalary(int $employeeSalary): void
    {
        $this->employeeSalary = $employeeSalary;
    }

    /**
     * @param int $employeeSalary
     * @return void
     */
    public function addEmployeeSalary(int $employeeSalary): void
    {
        $this->employeeSalary += $employeeSalary;
    }

    /**
     * @return int
     */
    public function getOfficeUtilityCost(): int
    {
        return $this->officeUtilityCost;
    }

    /**
     * @param int $officeUtilityCost
     * @return void
     */
    public function setOfficeUtilityCost(int $officeUtilityCost): void
    {
        $this->officeUtilityCost = $officeUtilityCost;
    }

    /**
     * @param int $officeUtilityCost
     * @return void
     */
    public function addOfficeUtilityCost(int $officeUtilityCost): void
    {
        $this->officeUtilityCost += $officeUtilityCost;
    }

    /**
     * @return int
     */
    public function getOfficeStationeryCost(): int
    {
        return $this->officeStationeryCost;
    }

    /**
     * @param int $officeStationeryCost
     * @return void
     */
    public function setOfficeStationeryCost(int $officeStationeryCost): void
    {
        $this->officeStationeryCost = $officeStationeryCost;
    }

    /**
     * @param int $officeStationeryCost
     * @return void
     */
    public function addOfficeStationeryCost(int $officeStationeryCost): void
    {
        $this->officeStationeryCost += $officeStationeryCost;
    }

    /**
     * @return int
     */
    public function getInternetCost(): int
    {
        return $this->internetCost;
    }

    /**
     * @param int $internetCost
     * @return void
     */
    public function setInternetCost(int $internetCost): void
    {
        $this->internetCost = $internetCost;
    }

    /**
     * @param int $internetCost
     * @return void
     */
    public function addInternetCost(int $internetCost): void
    {
        $this->internetCost += $internetCost;
    }

    /**
     * @return int
     */
    public function getTelephoneCost(): int
    {
        return $this->telephoneCost;
    }

    /**
     * @param int $telephoneCost
     * @return void
     */
    public function setTelephoneCost(int $telephoneCost): void
    {
        $this->telephoneCost = $telephoneCost;
    }

    /**
     * @param int $telephoneCost
     * @return void
     */
    public function addTelephoneCost(int $telephoneCost): void
    {
        $this->telephoneCost += $telephoneCost;
    }

    /**
     * @return int
     */
    public function getElectricityCost(): int
    {
        return $this->electricityCost;
    }

    /**
     * @param int $electricityCost
     * @return void
     */
    public function setElectricityCost(int $electricityCost): void
    {
        $this->electricityCost = $electricityCost;
    }

    /**
     * @param int $electricityCost
     * @return void
     */
    public function addElectricityCost(int $electricityCost): void
    {
        $this->electricityCost += $electricityCost;
    }

    /**
     * @return int
     */
    public function getTransportationCost(): int
    {
        return $this->transportationCost;
    }

    /**
     * @param int $transportationCost
     * @return void
     */
    public function setTransportationCost(int $transportationCost): void
    {
        $this->transportationCost = $transportationCost;
    }

    /**
     * @param int $transportationCost
     * @return void
     */
    public function addTransportationCost(int $transportationCost): void
    {
        $this->transportationCost += $transportationCost;
    }

    /**
     * @return int
     */
    public function getCommunicationCost(): int
    {
        return $this->communicationCost;
    }

    /**
     * @param int $communicationCost
     * @return void
     */
    public function setCommunicationCost(int $communicationCost): void
    {
        $this->communicationCost = $communicationCost;
    }

    /**
     * @param int $communicationCost
     * @return void
     */
    public function addCommunicationCost(int $communicationCost): void
    {
        $this->communicationCost += $communicationCost;
    }

    /**
     * @return int
     */
    public function getOfficeAssetMaintenanceCost(): int
    {
        return $this->officeAssetMaintenanceCost;
    }

    /**
     * @param int $officeAssetMaintenanceCost
     * @return void
     */
    public function setOfficeAssetMaintenanceCost(int $officeAssetMaintenanceCost): void
    {
        $this->officeAssetMaintenanceCost = $officeAssetMaintenanceCost;
    }

    /**
     * @param int $officeAssetMaintenanceCost
     * @return void
     */
    public function addOfficeAssetMaintenanceCost(int $officeAssetMaintenanceCost): void
    {
        $this->officeAssetMaintenanceCost += $officeAssetMaintenanceCost;
    }

    /**
     * @return int
     */
    public function getConsumptionCost(): int
    {
        return $this->consumptionCost;
    }

    /**
     * @param int $consumptionCost
     * @return void
     */
    public function setConsumptionCost(int $consumptionCost): void
    {
        $this->consumptionCost = $consumptionCost;
    }

    /**
     * @param int $consumptionCost
     * @return void
     */
    public function addConsumptionCost(int $consumptionCost): void
    {
        $this->consumptionCost += $consumptionCost;
    }
    /**
     * @return int
     */
    public function getInsuranceCost(): int
    {
        return $this->insuranceCost;
    }

    /**
     * @param int $insuranceCost
     * @return void
     */
    public function setInsuranceCost(int $insuranceCost): void
    {
        $this->insuranceCost = $insuranceCost;
    }

    /**
     * @param int $insuranceCost
     * @return void
     */
    public function addInsuranceCost(int $insuranceCost): void
    {
        $this->insuranceCost += $insuranceCost;
    }

    /**
     * @return int
     */
    public function getAdminAndCommonCost(): int
    {
        return $this->adminAndCommonCost;
    }

    /**
     * @param int $adminAndCommonCost
     * @return void
     */
    public function setAdminAndCommonCost(int $adminAndCommonCost): void
    {
        $this->adminAndCommonCost = $adminAndCommonCost;
    }

    /**
     * @param int $adminAndCommonCost
     * @return void
     */
    public function addAdminAndCommonCost(int $adminAndCommonCost): void
    {
        $this->adminAndCommonCost += $adminAndCommonCost;
    }

    /**
     * @return int
     */
    public function getDeprecationExpense(): int
    {
        return $this->deprecationExpense;
    }

    /**
     * @param int $deprecationExpense
     * @return void
     */
    public function setDeprecationExpense(int $deprecationExpense): void
    {
        $this->deprecationExpense = $deprecationExpense;
    }

    /**
     * @param int $deprecationExpense
     * @return void
     */
    public function addDeprecationExpense(int $deprecationExpense): void
    {
        $this->deprecationExpense += $deprecationExpense;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueryBuilderAlias(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getCanonicalTableName(): string
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * {@inheritDoc}
     */
    public function getDqlName(): string
    {
        throw new RuntimeException("Not implemented.");
    }
}
