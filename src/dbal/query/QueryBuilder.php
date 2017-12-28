<?php

namespace PPA\dbal\query;

use Latitude\QueryBuilder\Conditions;
use Latitude\QueryBuilder\QueryFactory;
use PPA\dbal\drivers\DriverInterface;
use PPA\orm\Analysis;
use PPA\orm\entity\Change;
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
    
    public function createStatementsForChangeSet(Serializable $entity, Analysis $analysis): array
    {
        $map = [];
        
//        $analysis  = $this->analyser->getMetaData($entity);
        $changeSet = $this->getChangeSet($entity);
        
        foreach ($changeSet as $change)
        {
            /* @var $change Change */

            $primProp = $analysis->getPrimaryProperty();
            $column   = $change->getProperty()->getColumn();
            $dataType = $column->getDatatype();
            $value    = $dataType->quoteValueForQuery($change->getToValue());

//            $query .= "`{$column->getName()}` = {$value}";
            
            
            $map[$column->getName()] = $value;
        }
        
        $statement = $this->factory->update($analysis->getTableName(), $map);
        $statement->where(Conditions::make("{$primProp->getName()} = ?", $primProp->getValue($entity)));

        var_dump($statement->sql());
    }
    
}

?>
