<?php

declare(strict_types=1);

namespace Model\Payment\ReadModel\Queries\MailCredentials;

/**
 * @see MailCredentialsAccessibleByGroupsQueryHandler
 */
final class MailCredentialsAccessibleByGroupsQuery
{
    /** @var int[] */
    private $unitIds;

    /**
     * @param int[] $unitIds
     */
    public function __construct(array $unitIds)
    {
        $this->unitIds = $unitIds;
    }

    /**
     * @return int[]
     */
    public function getUnitIds() : array
    {
        return $this->unitIds;
    }
}
