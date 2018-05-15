<?php

namespace PPA\dbal\query\builder;

use Latitude\QueryBuilder\Conditions;
use Latitude\QueryBuilder\QueryFactory;
use Latitude\QueryBuilder\Statement;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\expressions\properties\AsteriskWildcard;
use PPA\dbal\query\builder\AST\expressions\properties\Field;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\sub\SelectBuilder;
use PPA\dbal\statements\DQL\SelectStatement;
use PPA\orm\Analysis;
use PPA\orm\entity\Change;
use PPA\orm\entity\ChangeSet;
use PPA\orm\entity\Serializable;

class QueryBuilder
{
    const STATE_DIRTY   = -1;
    const STATE_INITIAL = 0;
    const STATE_CLEAN   = 1;
    
    const TYPE_SELECT = "SELECT";
    const TYPE_INSERT = "INSERT";
    const TYPE_UPDATE = "UPDATE";
    const TYPE_DELETE = "DELETE";
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    /**
     *
     * @var QueryFactory
     */
    private $factory;

    private $state;

    private $type;

    private $ASTCollection = [];


    /**
     *
     * @var SelectBuilder
     */
    private $builder;

    public function __construct(DriverInterface $driver)
    {
        $this->driver  = $driver;
        $this->factory = new QueryFactory($this->driver->getDriverName());
        $this->state   = self::STATE_INITIAL;
        
//        $this->ASTCollection = [
//            "select" => [],
//            "from"   => [],
//            "join"   => [],
//            "where"  => []
//        ];
    }
    
//    public function buildSelect(string $fromTable, parts\SelectList $selectList = null, string $alias = null): self
//    {
//        if (!$this->stateIsInitial())
//        {
//            throw new Exception("Wrong state");
//        }
//        
//        $this->state = self::STATE_DIRTY;
//        
//        $this->builder = new SelectBuilder($fromTable, $selectList);
//        
//        return $this;
//    }
    
//    public function createNativeQuery()
//    {
//        
//    }
//    public function createPreparedNativeQuery()
//    {
//        
//    }
//    public function createTypedQuery()
//    {
//        
//    }
//    public function createPreparedTypedQuery()
//    {
//        
//    }


    public function createStatementsForChangeSet(Serializable $entity, Analysis $analysis, ChangeSet $changeSet): Statement
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
    
    protected function setStateInitial(): void
    {
        $this->state = self::STATE_INITIAL;
    }
    
    protected function setStateDirty(): void
    {
        $this->state = self::STATE_DIRTY;
    }
    
    protected function setStateClean(): void
    {
        $this->state = self::STATE_CLEAN;
    }
    
    public function stateIsDirty(): bool
    {
        return $this->state == self::STATE_DIRTY;
    }
    
    public function stateIsClean(): bool
    {
        return $this->state == self::STATE_CLEAN;
    }
    
    public function stateIsInitial(): bool
    {
        return $this->state == self::STATE_INITIAL;
    }
    
    protected function setTypeSelet(): void
    {
        $this->type = self::TYPE_SELECT;
    }
    
    protected function setTypeUpdate(): void
    {
        $this->type = self::TYPE_UPDATE;
    }
    
    protected function setTypeInsert(): void
    {
        $this->type = self::TYPE_INSERT;
    }
    
    protected function setTypeDelete(): void
    {
        $this->type = self::TYPE_DELETE;
    }
    
    public function typeIsSelect(): bool
    {
        return $this->type == self::TYPE_SELECT;
    }
    
    public function typeIsInsert(): bool
    {
        return $this->type == self::TYPE_INSERT;
    }
    
    public function typeIsUpdate(): bool
    {
        return $this->type == self::TYPE_UPDATE;
    }
    
    public function typeIsDelete(): bool
    {
        return $this->type == self::TYPE_DELETE;
    }
    
    public function select(Property ...$properties): SelectStatement
    {
        if (!$this->stateIsInitial())
        {
            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_INITIAL, $this->state);
        }
        
        if (empty($properties))
        {
            $properties[] = new AsteriskWildcard();
        }
        
        
        $selectStatement = new SelectStatement($this->driver, ...$properties);
        
        $this->ASTCollection[] = $selectStatement;
        
        $this->setTypeSelet();
        $this->setStateDirty();
//        $this->ASTCollection["select"] = $sources;
        
        return $selectStatement;
    }
    
//    
//    public function end(): self
//    {
//        if (!$this->stateIsDirty())
//        {
//            throw ExceptionFactory::CollectionState("QueryBuilder is not in a dirty state.");
//        }
//        
//        $this->setStateClean();
//        
//        return $this;
//    }
    
    public function sql(): string
    {
//        if (!$this->stateIsClean())
//        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_CLEAN, $this->state);
//        }
        
        if ($this->typeIsSelect())
        {
//            $from   = $this->ASTCollection["from"];
//            $select = $this->ASTCollection["select"];
//            $where  = $this->ASTCollection["where"];
//            $string = "SELECT ";
            
//            print_r($from);
//            foreach ($this->ASTCollection["select"] as $element)
//            {
//                if (is_string($element))
//                {
//                    die($element);
//                }
//                $string .= $element->toString();
//            }
            
            
            array_walk($this->ASTCollection, function(&$element) {
                $element = $element->toString();
            });
//            var_dump($this->ASTCollection);
            $string = implode(" ", $this->ASTCollection);
//            var_dump($string);
//            $string .= " FROM `{$from[0]}`" . ($from[1] == null ? : "" . " AS '{$from[1]}'");
            
            
//            foreach ($this->ASTCollection["join"] as $element)
//            {
//                $string .= " JOIN `{$element[0]}`" . ($element[1] == null ? "" : " AS '{$element[1]}'");
//                
//                if (isset($element[2]))
//                {
//                    $string .= " ON(" . $element[2]->toString() . ")";
//                }
//            }
//            
//            if (!empty($where))
//            {
//                $string .= " WHERE " . $where->toString();
//            }
        }
        
        return $string;
    }
    
    public static function processExpression($expression): Expression
    {
        if ($expression == "?")
        {
            $expression = new UnnamedParameter();
        }
        // Check if string is quoted
        else if (is_string($expression) && preg_match('/^(["\']).*\1$/m', $expression) || is_integer($expression))
        {
            $expression = new Literal(trim($expression, "\"'"), gettype($expression));
        }
        else
        {
            $expression = new Field($expression);
        }
        
        return $expression;
    }
    
}

?>
