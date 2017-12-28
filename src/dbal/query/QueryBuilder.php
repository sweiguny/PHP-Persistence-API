<?php

namespace PPA\dbal\query;

use Latitude\QueryBuilder\Conditions;
use Latitude\QueryBuilder\QueryFactory;
use PPA\dbal\drivers\DriverInterface;
use PPA\orm\Analysis;
use PPA\orm\entity\Change;
use PPA\orm\entity\ChangeSet;
use PPA\orm\entity\Serializable;

class QueryBuilder
{
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    private $factory;


    public function __construct(DriverInterface $driver)
    {
        $this->driver  = $driver;
        $this->factory = new QueryFactory($this->driver->getDriverName());
    }
    
    public function createStatementsForChangeSet(Serializable $entity, Analysis $analysis, ChangeSet $changeSet): string
    {
        $columnList = [];
        $primProp   = $analysis->getPrimaryProperty();
        $primValue  = $primProp->getColumn()->getDatatype()->quoteValueForQuery($primProp->getValue($entity));
        
        foreach ($changeSet as $change)
        {
            /* @var $change Change */

            $column   = $change->getProperty()->getColumn();
            $dataType = $column->getDatatype();
            $value    = $dataType->quoteValueForQuery($change->getToValue());
            
            $columnList[$column->getName()] = $value;
        }
        
        $statement = $this->factory->update($analysis->getTableName(), $columnList);
        $statement->where(Conditions::make("{$primProp->getName()} = ?", $primValue));

        return $statement;
    }
    
}

?>
