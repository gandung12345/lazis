<?php

declare(strict_types=1);

namespace Schnell\Mapper\Query;

use function substr;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Error
{
    /**
     * @var string
     */
    public const SQLSTATE_PREF0 = '00';

    /**
     * @var string
     */
    public const SQLSTATE_PREF1 = '01';

    /**
     * @var string
     */
    public const SQLSTATE_PREF2 = '02';

    /**
     * @var string
     */
    public const SQLSTATE_PREF3 = '07';

    /**
     * @var string
     */
    public const SQLSTATE_PREF4 = '08';

    /**
     * @var string
     */
    public const SQLSTATE_PREF5 = '09';

    /**
     * @var string
     */
    public const SQLSTATE_PREF6 = '0A';

    /**
     * @var string
     */
    public const SQLSTATE_PREF7 = '0D';

    /**
     * @var string
     */
    public const SQLSTATE_PREF8 = '0E';

    /**
     * @var string
     */
    public const SQLSTATE_PREF9 = '0F';

    /**
     * @var string
     */
    public const SQLSTATE_PREF10 = '0K';

    /**
     * @var string
     */
    public const SQLSTATE_PREF11 = '0L';

    /**
     * @var string
     */
    public const SQLSTATE_PREF12 = '0M';

    /**
     * @var string
     */
    public const SQLSTATE_PREF13 = '0N';

    /**
     * @var string
     */
    public const SQLSTATE_PREF14 = '0P';

    /**
     * @var string
     */
    public const SQLSTATE_PREF15 = '0S';

    /**
     * @var string
     */
    public const SQLSTATE_PREF16 = '0T';

    /**
     * @var string
     */
    public const SQLSTATE_PREF17 = '0U';

    /**
     * @var string
     */
    public const SQLSTATE_PREF18 = '0V';

    /**
     * @var string
     */
    public const SQLSTATE_PREF19 = '0W';

    /**
     * @var string
     */
    public const SQLSTATE_PREF20 = '0X';

    /**
     * @var string
     */
    public const SQLSTATE_PREF21 = '0Y';

    /**
     * @var string
     */
    public const SQLSTATE_PREF22 = '0Z';

    /**
     * @var string
     */
    public const SQLSTATE_PREF23 = '10';

    /**
     * @var string
     */
    public const SQLSTATE_PREF24 = '20';

    /**
     * @var string
     */
    public const SQLSTATE_PREF25 = '21';

    /**
     * @var string
     */
    public const SQLSTATE_PREF26 = '22';

    /**
     * @var string
     */
    public const SQLSTATE_PREF27 = '23';

    /**
     * @var string
     */
    public const SQLSTATE_PREF28 = '24';

    /**
     * @var string
     */
    public const SQLSTATE_PREF29 = '25';

    /**
     * @var string
     */
    public const SQLSTATE_PREF30 = '26';

    /**
     * @var string
     */
    public const SQLSTATE_PREF31 = '27';

    /**
     * @var string
     */
    public const SQLSTATE_PREF32 = '28';

    /**
     * @var string
     */
    public const SQLSTATE_PREF33 = '2B';

    /**
     * @var string
     */
    public const SQLSTATE_PREF34 = '2C';

    /**
     * @var string
     */
    public const SQLSTATE_PREF35 = '2D';

    /**
     * @var string
     */
    public const SQLSTATE_PREF36 = '2E';

    /**
     * @var string
     */
    public const SQLSTATE_PREF37 = '2F';

    /**
     * @var string
     */
    public const SQLSTATE_PREF38 = '2H';

    /**
     * @var string
     */
    public const SQLSTATE_PREF39 = '30';

    /**
     * @var string
     */
    public const SQLSTATE_PREF40 = '33';

    /**
     * @var string
     */
    public const SQLSTATE_PREF41 = '34';

    /**
     * @var string
     */
    public const SQLSTATE_PREF42 = '35';

    /**
     * @var string
     */
    public const SQLSTATE_PREF43 = '36';

    /**
     * @var string
     */
    public const SQLSTATE_PREF44 = '38';

    /**
     * @var string
     */
    public const SQLSTATE_PREF45 = '39';

    /**
     * @var string
     */
    public const SQLSTATE_PREF46 = '3B';

    /**
     * @var string
     */
    public const SQLSTATE_PREF47 = '3C';

    /**
     * @var string
     */
    public const SQLSTATE_PREF48 = '3D';

    /**
     * @var string
     */
    public const SQLSTATE_PREF49 = '3F';

    /**
     * @var string
     */
    public const SQLSTATE_PREF50 = '40';

    /**
     * @var string
     */
    public const SQLSTATE_PREF51 = '42';

    /**
     * @var string
     */
    public const SQLSTATE_PREF52 = '44';

    /**
     * @var string
     */
    public const SQLSTATE_PREF53 = '45';

    /**
     * @var string
     */
    private string $sqlState;

    /**
     * @psalm-api
     *
     * @param string $sqlState
     * @return static
     */
    public function __construct(string $sqlState)
    {
        $this->setSqlState($sqlState);
    }

    /**
     * @return string
     */
    public function getSqlState(): string
    {
        return $this->sqlState;
    }

    /**
     * @param string $sqlState
     * @return void
     */
    public function setSqlState(string $sqlState): void
    {
        $this->sqlState = $sqlState;
    }

    /**
     * @return string
     */
    public function getErrorClassification(): string
    {
        $sqlStatePrefix = substr($this->getSqlState(), 0, 2);

        switch ($sqlStatePrefix) {
            case self::SQLSTATE_PREF0:
                return "successful completion";
            case self::SQLSTATE_PREF1:
                return "warning";
            case self::SQLSTATE_PREF2:
                return "no data";
            case self::SQLSTATE_PREF3:
                return "dynamic SQL error";
            case self::SQLSTATE_PREF4:
                return "connection exception";
            case self::SQLSTATE_PREF5:
                return "triggered action exception";
            case self::SQLSTATE_PREF6:
                return "feature not supported";
            case self::SQLSTATE_PREF7:
                return "invalid target type specification";
            case self::SQLSTATE_PREF8:
                return "invalid schema list specification";
            case self::SQLSTATE_PREF9:
                return "locator exception";
            case self::SQLSTATE_PREF10:
                return "resignal when handler not active";
            case self::SQLSTATE_PREF11:
                return "invalid grantor";
            case self::SQLSTATE_PREF12:
                return "invalid SQL-invoked procedure reference";
            case self::SQLSTATE_PREF13:
                return "SQL/XML mapping error";
            case self::SQLSTATE_PREF14:
                return "invalid role specification";
            case self::SQLSTATE_PREF15:
                return "invalid transform group name specification";
            case self::SQLSTATE_PREF16:
                return "target table disagrees with cursor specification";
            case self::SQLSTATE_PREF17:
                return "attempt to assign to non-updatable column";
            case self::SQLSTATE_PREF18:
                return "attempt to assign to ordering column";
            case self::SQLSTATE_PREF19:
                return "prohibited statement encountered during trigger execution";
            case self::SQLSTATE_PREF20:
                return "invalid foreign server specification";
            case self::SQLSTATE_PREF21:
                return "pass-through specific condition";
            case self::SQLSTATE_PREF22:
                return "diagnostics exception";
            case self::SQLSTATE_PREF23:
                return "XQuery error";
            case self::SQLSTATE_PREF24:
                return "case not found for case statement";
            case self::SQLSTATE_PREF25:
                return "cardinality violation";
            case self::SQLSTATE_PREF26:
                return "data exception";
            case self::SQLSTATE_PREF27:
                return "integrity constraint violation";
            case self::SQLSTATE_PREF28:
                return "invalid cursor state";
            case self::SQLSTATE_PREF29:
                return "invalid transaction state";
            case self::SQLSTATE_PREF30:
                return "invalid SQL statement name";
            case self::SQLSTATE_PREF31:
                return "triggered data change violation";
            case self::SQLSTATE_PREF32:
                return "invalid authorization specification";
            case self::SQLSTATE_PREF33:
                return "dependent privilege descriptors still exist";
            case self::SQLSTATE_PREF34:
                return "invalid character set name";
            case self::SQLSTATE_PREF35:
                return "invalid transaction termination";
            case self::SQLSTATE_PREF36:
                return "invalid connection name";
            case self::SQLSTATE_PREF37:
                return "SQL routine exception";
            case self::SQLSTATE_PREF38:
                return "invalid collation name";
            case self::SQLSTATE_PREF39:
                return "invalid SQL statement identifier";
            case self::SQLSTATE_PREF40:
                return "invalid SQL descriptor name";
            case self::SQLSTATE_PREF41:
                return "invalid cursor name";
            case self::SQLSTATE_PREF42:
                return "invalid condition number";
            case self::SQLSTATE_PREF43:
                return "cursor sensitivity exception";
            case self::SQLSTATE_PREF44:
                return "external routine exception";
            case self::SQLSTATE_PREF45:
                return "external routine invocation exception";
            case self::SQLSTATE_PREF46:
                return "savepoint exception";
            case self::SQLSTATE_PREF47:
                return "ambiguous cursor name";
            case self::SQLSTATE_PREF48:
                return "invalid catalog name";
            case self::SQLSTATE_PREF49:
                return "invalid schema name";
            case self::SQLSTATE_PREF50:
                return "transaction rollback";
            case self::SQLSTATE_PREF51:
                return "syntax error or access rule violation";
            case self::SQLSTATE_PREF52:
                return "with check option violation";
            case self::SQLSTATE_PREF53:
                return "unhandled user-defined exception";
        }

        return "unknown error classification";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getErrorClassification();
    }
}
