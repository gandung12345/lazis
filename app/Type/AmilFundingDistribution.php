<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class AmilFundingDistribution
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int SOCIAL_AND_EDUCATION_FUNDING = 1;

    /**
     * @var int
     */
    public const int EMPLOYEE_EXPENSES = 2;

    /**
     * @var int
     */
    public const int SALARY = 3;

    /**
     * @var int
     */
    public const int OFFICE_EQUIPMENT_COST = 4;

    /**
     * @var int
     */
    public const int OFFICE_STATIONERY_COST = 5;

    /**
     * @var int
     */
    public const int INTERNET_COST = 6;

    /**
     * @var int
     */
    public const int PHONE_BILL_COST = 7;

    /**
     * @var int
     */
    public const int ELECTRIC_BILL_COST = 8;

    /**
     * @var int
     */
    public const int TRANSPORTATION_COST = 9;

    /**
     * @var int
     */
    public const int COMMUNICATION_COST = 10;

    /**
     * @var int
     */
    public const int OFFICE_ASSET_MAINTENANCE_COST = 11;

    /**
     * @var int
     */
    public const int FOOD_AND_BEVERAGE_COST = 12;

    /**
     * @var int
     */
    public const int INSURANCE_COST = 13;

    /**
     * @var int
     */
    public const int ADMIN_AND_COMMON_COST = 14;

    /**
     * @var int
     */
    public const int DEPRECIATION_EXPENSE = 15;
}
