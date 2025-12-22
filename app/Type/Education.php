<?php

declare(strict_types=1);

namespace Lazis\Api\Type;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
final class Education
{
    use TypeTrait;

    /**
     * @var int
     */
    public const int NONE = 1;

    /**
     * @var int
     */
    public const int UNGRADUATE_ELEMENTARY_SCHOOL = 1;

    /**
     * @var int
     */
    public const int GRADUATE_ELEMENTARY_SCHOOL = 2;

    /**
     * @var int
     */
    public const int JUNIOR_HIGH_SCHOOL = 3;

    /**
     * @var int
     */
    public const int SENIOR_HIGH_SCHOOL = 4;

    /**
     * @var int
     */
    public const int DIPLOMA_I_II = 5;

    /**
     * @var int
     */
    public const int DIPLOMA_III = 6;

    /**
     * @var int
     */
    public const int DIPLOMA_IV = 7;

    /**
     * @var int
     */
    public const int MASTER = 8;
}
