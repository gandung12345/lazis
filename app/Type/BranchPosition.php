<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class BranchPosition
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int RAIS = 1;

    /**
     * @var int
     */
    public const int PCNU_LEADER = 2;

    /**
     * @var int
     */
    public const int LEADER = 3;

    /**
     * @var int
     */
    public const int REPRESENTATIVE_LEADER = 4;

    /**
     * @var int
     */
    public const int SECRETARY = 5;

    /**
     * @var int
     */
    public const int REPRESENTATIVE_SECRETARY = 6;

    /**
     * @var int
     */
    public const int MEMBER_I = 7;

    /**
     * @var int
     */
    public const int MEMBER_II = 8;

    /**
     * @var int
     */
    public const int BRANCH_HEAD = 9;

    /**
     * @var int
     */
    public const int FINANCING_BRANCH_MANAGER_I = 10;

    /**
     * @var int
     */
    public const int FINANCING_BRANCH_SENIOR_STAFF_MANAGER_I = 11;

    /**
     * @var int
     */
    public const int FINANCING_BRANCH_STAFF_MANAGER_I = 12;

    /**
     * @var int
     */
    public const int IT_AND_COLLECTION_BRANCH_MANAGER_II = 13;

    /**
     * @var int
     */
    public const int BRANCH_SENIOR_STAFF_MANAGER_II = 14;

    /**
     * @var int
     */
    public const int BRANCH_STAFF_MANAGER_II = 15;

    /**
     * @var int
     */
    public const int BRANCH_DISTRIBUTION_MANAGER_III = 16;

    /**
     * @var int
     */
    public const int BRANCH_SENIOR_STAFF_MANAGER_III = 17;

    /**
     * @var int
     */
    public const int BRANCH_STAFF_MANAGER_III = 18;

    /**
     * @var int
     */
    public const int BRANCH_HR_AND_GENERAL_ADMIN_MANAGER_IV = 19;

    /**
     * @var int
     */
    public const int BRANCH_SENIOR_STAFF_MANAGER_IV = 20;

    /**
     * @var int
     */
    public const int BRANCH_STAFF_MANAGER_IV = 21;
}
