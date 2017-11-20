<?php

namespace Model;

use Dibi\Connection;

/**
 * @author Hána František <sinacek@gmail.com>
 */
class BaseTable
{

    const TABLE_CHIT = 'ac_chits';
    const TABLE_CATEGORY = 'ac_chitsCategory';
    const TABLE_CATEGORY_OBJECT = 'ac_chitsCategory_object';
    const TABLE_CHIT_VIEW = 'ac_chitsView';
    const TABLE_CAMP_PARTICIPANT = 'ac_camp_participants';
    const TABLE_OBJECT = 'ac_object';
    const TABLE_OBJECT_TYPE = 'ac_object_type';
    const TABLE_UNIT_BUDGET_CATEGORY = 'ac_unit_budget_category';
    const TABLE_TC_CONTRACTS = 'tc_contracts';
    const TABLE_TC_COMMANDS = 'tc_commands';
    const TABLE_TC_COMMAND_TYPES = 'tc_command_types';
    const TABLE_TC_TRAVEL_TYPES = 'tc_travelTypes';
    const TABLE_TC_VEHICLE = 'tc_vehicle';

    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * vyhleda akci|jednotku a pokud tam není, tak založí její záznam
     */
    public function getLocalId(int $skautisEventId, string $type): int
    {
        if (!($ret = $this->connection->fetchSingle("SELECT id FROM [" . self::TABLE_OBJECT . "] WHERE skautisId=%i AND type=%s LIMIT 1", $skautisEventId, $type))) {
            $ret = $this->connection->insert(self::TABLE_OBJECT, ["skautisId" => $skautisEventId, "type" => $type])->execute(\dibi::IDENTIFIER);
        }
        return $ret;
    }

    public function getSkautisId($localId)
    {
        return $this->connection->fetchSingle("SELECT skautisId FROM [" . self::TABLE_OBJECT . "] WHERE id=%i LIMIT 1", $localId);
    }

    public function getByEventId($skautisEventId, $type)
    {
        $ret = $this->connection->fetch("SELECT id as localId, prefix FROM  [" . self::TABLE_OBJECT . "] WHERE skautisId=%i AND type=%s LIMIT 1", $skautisEventId, $type);
        if (!$ret) {
            $this->getLocalId($skautisEventId, $type);
            $ret = $this->{__FUNCTION__}($skautisEventId, $type);
        }
        return $ret;
    }

}
