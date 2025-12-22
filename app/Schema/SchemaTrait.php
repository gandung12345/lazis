<?php

declare(strict_types=1);

namespace Lazis\Api\Schema;

use Lazis\Api\Type\ActiveStatus;
use Lazis\Api\Type\AmilFunding;
use Lazis\Api\Type\AmilFundingDistribution;
use Lazis\Api\Type\AssetRecording;
use Lazis\Api\Type\Asnaf;
use Lazis\Api\Type\BranchPosition;
use Lazis\Api\Type\BranchRepresentativePosition;
use Lazis\Api\Type\Donee;
use Lazis\Api\Type\Dskl;
use Lazis\Api\Type\Education;
use Lazis\Api\Type\InfaqProgram;
use Lazis\Api\Type\Jpzis;
use Lazis\Api\Type\Muzakki;
use Lazis\Api\Type\NonHalalDistribution;
use Lazis\Api\Type\NonHalalFunding;
use Lazis\Api\Type\NuCoinCrossTransfer;
use Lazis\Api\Type\NuCoinCrossTransferQueue;
use Lazis\Api\Type\OffBalanceSheet;
use Lazis\Api\Type\OffBalanceSheetAggregation;
use Lazis\Api\Type\Role;
use Lazis\Api\Type\Scope;
use Lazis\Api\Type\Sex;
use Lazis\Api\Type\Transaction;
use Lazis\Api\Type\TwigPosition;
use Lazis\Api\Type\Volunteer;
use Lazis\Api\Type\VolunteerGroup;
use Lazis\Api\Type\Wallet;
use Lazis\Api\Type\Zakat;
use Lazis\Api\Type\ZakatDistribution;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
trait SchemaTrait
{
    /**
     * @var string
     */
    private const string ISO8601_DATE_PATTERN = <<<REGEX
    /^(?:\d{4})\-(?:\d{2})\-(?:\d{2})$/
    REGEX;

    /**
     * @var string
     */
    private const string ISO8601_YEAR_PATTERN = <<<REGEX
    /^(?:\d{4})$/
    REGEX;

    /**
     * @var string
     */
    private const string PHONE_NUMBER_PATTERN = '/^(\+62|0)([0-9]{10,13})$/';

    /**
     * @var string
     */
    private const string EMAIL_PATTERN = <<<REGEX
    /^(?:[a-z]{1})(?:[a-zA-Z0-9\.\-\_]*)(?:\@)(?:[a-zA-Z0-9\-\_]+)(?:\.(?:[a-z]+))+/
    REGEX;

    /**
     * @var array
     */
    private const array ACTIVE_STATUS_LIST = [
        ActiveStatus::ACTIVE,
        ActiveStatus::INACTIVE
    ];

    /**
     * @var array
     */
    private const array AMIL_FUNDING_LIST = [
        AmilFunding::GRANT_FUNDS,
        AmilFunding::OTHER_AMIL
    ];

    /**
     * @var array
     */
    private const array AMIL_FUNDING_DISTRIBUTION_LIST = [
        AmilFundingDistribution::SOCIAL_AND_EDUCATION_FUNDING,
        AmilFundingDistribution::EMPLOYEE_EXPENSES,
        AmilFundingDistribution::SALARY,
        AmilFundingDistribution::OFFICE_EQUIPMENT_COST,
        AmilFundingDistribution::OFFICE_STATIONERY_COST,
        AmilFundingDistribution::INTERNET_COST,
        AmilFundingDistribution::PHONE_BILL_COST,
        AmilFundingDistribution::ELECTRIC_BILL_COST,
        AmilFundingDistribution::TRANSPORTATION_COST,
        AmilFundingDistribution::COMMUNICATION_COST,
        AmilFundingDistribution::OFFICE_ASSET_MAINTENANCE_COST,
        AmilFundingDistribution::FOOD_AND_BEVERAGE_COST,
        AmilFundingDistribution::INSURANCE_COST,
        AmilFundingDistribution::ADMIN_AND_COMMON_COST,
        AmilFundingDistribution::DEPRECIATION_EXPENSE
    ];

    /**
     * @var array
     */
    private const array ASSET_RECORDING_LIST = [
        AssetRecording::CURRENT_ASSET,
        AssetRecording::NON_CURRENT_ASSET
    ];

    /**
     * @var array
     */
    private const array ASNAF_LIST = [
        Asnaf::FAKIR,
        Asnaf::POOR,
        Asnaf::AMIL,
        Asnaf::MUALAF,
        Asnaf::RIQAB,
        Asnaf::GHARIM,
        Asnaf::FISABILILLAH,
        Asnaf::IBNU_SABIL
    ];

    /**
     * @var array
     */
    private const array AUTH_ROLE_LIST = [
        Role::ROOT,
        Role::ADMIN,
        Role::ADMIN_MASTER_DATA,
        Role::AGGREGATOR_ADMIN,
        Role::TASHARUF_ADMIN
    ];

    /**
     * @var array
     */
    private const array BRANCH_POSITION_LIST = [
        BranchPosition::RAIS,
        BranchPosition::LEADER,
        BranchPosition::REPRESENTATIVE_LEADER,
        BranchPosition::SECRETARY,
        BranchPosition::REPRESENTATIVE_SECRETARY,
        BranchPosition::MEMBER_I,
        BranchPosition::MEMBER_II,
        BranchPosition::BRANCH_HEAD,
        BranchPosition::FINANCING_BRANCH_MANAGER_I,
        BranchPosition::FINANCING_BRANCH_SENIOR_STAFF_MANAGER_I,
        BranchPosition::FINANCING_BRANCH_STAFF_MANAGER_I,
        BranchPosition::IT_AND_COLLECTION_BRANCH_MANAGER_II,
        BranchPosition::BRANCH_SENIOR_STAFF_MANAGER_II,
        BranchPosition::BRANCH_STAFF_MANAGER_II,
        BranchPosition::BRANCH_DISTRIBUTION_MANAGER_III,
        BranchPosition::BRANCH_SENIOR_STAFF_MANAGER_III,
        BranchPosition::BRANCH_HR_AND_GENERAL_ADMIN_MANAGER_IV,
        BranchPosition::BRANCH_SENIOR_STAFF_MANAGER_IV,
        BranchPosition::BRANCH_STAFF_MANAGER_IV
    ];

    /**
     * @var array
     */
    private const array BRANCH_REPRESENTATIVE_POSITION_LIST = [
        BranchRepresentativePosition::RAIS,
        BranchRepresentativePosition::LEADER,
        BranchRepresentativePosition::AREA_MANAGER,
        BranchRepresentativePosition::ADMIN_AND_FINANCING_STAFF,
        BranchRepresentativePosition::COLLECTION_STAFF,
        BranchRepresentativePosition::DISTRIBUTION_STAFF
    ];

    /**
     * @var array
     */
    private const array COLLECTOR_KIND_LIST = [
        OffBalanceSheetAggregation::MOSQUE,
        OffBalanceSheetAggregation::UPZIS,
        OffBalanceSheetAggregation::JPZIS
    ];

    /**
     * @var array
     */
    private const array DONEE_LIST = [
        Donee::POOR,
        Donee::ORPHAN,
        Donee::QURAN_TEACHER,
        Donee::DISABILITY,
        Donee::OTHER
    ];

    /**
     * @var array
     */
    private const array DSKL_LIST = [
        Dskl::BPKH,
        Dskl::QURBAN,
        Dskl::FIDYAH
    ];

    /**
     * @var array
     */
    private const array EDUCATION_LIST = [
        Education::NONE,
        Education::UNGRADUATE_ELEMENTARY_SCHOOL,
        Education::GRADUATE_ELEMENTARY_SCHOOL,
        Education::JUNIOR_HIGH_SCHOOL,
        Education::SENIOR_HIGH_SCHOOL,
        Education::DIPLOMA_I_II,
        Education::DIPLOMA_III,
        Education::DIPLOMA_IV,
        Education::MASTER
    ];

    /**
     * @var array
     */
    private const array INFAQ_PROGRAM_LIST = [
        InfaqProgram::NU_CARE_SMART,
        InfaqProgram::NU_CARE_EMPOWERED,
        InfaqProgram::NU_CARE_HEALTHY,
        InfaqProgram::NU_CARE_GREEN,
        InfaqProgram::NU_CARE_PEACE,
        InfaqProgram::CAMPAIGN_PROGRAM,
        InfaqProgram::DONATION,
        InfaqProgram::UNBOUNDED
    ];

    /**
     * @var array
     */
    private const array JPZIS_TYPE_LIST = [
        Jpzis::MOSQUE,
        Jpzis::OTHER
    ];

    /**
     * @var array
     */
    private const array MUZAKKI_LIST = [
        Muzakki::PERSONAL,
        Muzakki::COLLECTIVE
    ];

    /**
     * @var array
     */
    private const array NON_HALAL_FUNDING_DISTRIBUTION_LIST = [
        NonHalalDistribution::BANK_ADMIN,
        NonHalalDistribution::NON_HALAL_FUNDING_USAGE
    ];

    /**
     * @var array
     */
    private const array NON_HALAL_FUNDING_RECEIVE_LIST = [
        NonHalalFunding::BANK_INTEREST,
        NonHalalFunding::CURRENT_ACCOUNT_SERVICE,
        NonHalalFunding::OTHER
    ];

    /**
     * @var array
     */
    private const array NU_COIN_CROSS_TRANSFER_STATUS_LIST = [
        NuCoinCrossTransfer::QUEUED,
        NuCoinCrossTransfer::APPROVED,
        NuCoinCrossTransfer::REJECTED
    ];

    /**
     * @var array
     */
    private const array NU_COIN_CROSS_TRANSFER_QUEUE_STATUS_LIST = [
        NuCoinCrossTransferQueue::DEPOSIT,
        NuCoinCrossTransferQueue::REFUND,
    ];

    /**
     * @var array
     */
    private const array OFF_BALANCE_SHEET_KIND_LIST = [
        OffBalanceSheet::ZAKAT_MAL,
        OffBalanceSheet::ZAKAT_FITRAH,
        OffBalanceSheet::INFAQ,
        OffBalanceSheet::NATURA_INFAQ,
        OffBalanceSheet::QURBAN,
        OffBalanceSheet::FIDYAH,
        OffBalanceSheet::DSKL
    ];

    /**
     * @var array
     */
    private const array ROLE_LIST = [
        Scope::BRANCH => self::BRANCH_POSITION_LIST,
        Scope::BRANCH_REPRESENTATIVE => self::BRANCH_REPRESENTATIVE_POSITION_LIST,
        Scope::TWIG => self::TWIG_POSITION_LIST
    ];

    /**
     * @var array
     */
    private const array SEX_LIST = [
        Sex::MALE,
        Sex::FEMALE
    ];

    /**
     * @var array
     */
    private const array SCOPE_LIST = [
        Scope::BRANCH,
        Scope::BRANCH_REPRESENTATIVE,
        Scope::TWIG
    ];

    /**
     * @var array
     */
    private const array TRANSACTION_LIST = [
        Transaction::INCOMING,
        Transaction::OUTGOING
    ];

    /**
     * @var array
     */
    private const array TWIG_POSITION_LIST = [
        TwigPosition::RAIS,
        TwigPosition::LEADER,
        TwigPosition::SUB_AREA_MANAGER,
        TwigPosition::ADMIN_AND_FINANCING_STAFF,
        TwigPosition::COLLECTION_STAFF,
        TwigPosition::DISTRIBUTION_STAFF
    ];

    /**
     * @var array
     */
    private const array VOLUNTEER_LIST = [
        Volunteer::LEAD,
        Volunteer::MEMBER
    ];

    /**
     * @var array
     */
    private const array VOLUNTEER_GROUP_LIST = [
        VolunteerGroup::VOLGROUP_1,
        VolunteerGroup::VOLGROUP_2,
        VolunteerGroup::VOLGROUP_3,
        VolunteerGroup::VOLGROUP_4,
        VolunteerGroup::VOLGROUP_5,
        VolunteerGroup::VOLGROUP_6,
        VolunteerGroup::VOLGROUP_7,
        VolunteerGroup::VOLGROUP_8,
        VolunteerGroup::VOLGROUP_9,
        VolunteerGroup::VOLGROUP_10
    ];

    /**
     * @var array
     */
    private const array WALLET_TYPE_LIST = [
        Wallet::ALMSGIVING,
        Wallet::NUCARE_SMART_DISBURSEMENT,
        Wallet::NUCARE_EMPOWERED_DISBURSEMENT,
        Wallet::NUCARE_HEALTH_DISBURSEMENT,
        Wallet::NUCARE_GREEN_DISBURSEMENT,
        Wallet::NUCARE_PEACE_DISBURSEMENT,
        Wallet::UNBOUNDED_DISBURSEMENT,
        Wallet::DONATION,
        Wallet::CAMPAIGN_PROGRAM,
        Wallet::NU_COIN,
        Wallet::AMIL,
        Wallet::ORGANIZATION_SOCIAL_FUNDING,
        Wallet::QURBAN,
        Wallet::NON_HALAL
    ];

    /**
     * @var array
     */
    private const array ZAKAT_DISTRIBUTION_LIST = [
        ZakatDistribution::NU_CARE_SMART,
        ZakatDistribution::NU_CARE_EMPOWERED,
        ZakatDistribution::NU_CARE_HEALTHY,
        ZakatDistribution::NU_CARE_GREEN,
        ZakatDistribution::NU_CARE_PEACE,
        ZakatDistribution::NU_CARE_QURBAN
    ];

    /**
     * @var array
     */
    private const array ZAKAT_TYPE_LIST = [
        Zakat::FITRAH,
        Zakat::MAAL
    ];
}
