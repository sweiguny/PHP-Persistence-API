<?php

namespace PPA\tests\bootstrap;

use Iterator;
use PDOException;
use PPA\core\exceptions\io\IOException;
use PPA\core\exceptions\runtime\db\DatabaseException;
use PPA\core\util\FileReader;
use PPA\dbal\Connection;
use PPA\dbal\query\builder\AST\catalogObjects\_Field;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\dbal\TransactionManager;
use PPA\orm\Analysis;
use PPA\orm\EntityAnalyser;
use PPA\orm\EntityProperty;
use const PPA_TEST_BOOTSTRAP_PATH;

class FixtureSetup
{
    /**
     *
     * @var Connection
     */
    protected $connection;
    
    /**
     *
     * @var TransactionManager
     */
    protected $transactionManager;
    
    /**
     *
     * @var EntityAnalyser
     */
    protected $entityAnalyser;

    /**
     *
     * @var array
     */
    private $tables = [];

    public function __construct(Connection $connection, EntityAnalyser $entityAnalyser, TransactionManager $transactionManager)
    {
        $this->connection         = $connection;
        $this->entityAnalyser     = $entityAnalyser;
        $this->transactionManager = $transactionManager;
    }
    
    public function setUpFixtures(string $classname): void
    {
        $analysis      = $this->entityAnalyser->analyse($classname);
        $addrStatePath = self::generateFilePathToFixtures($analysis->getTableName());
        
        $filereader = new FileReader();
        $iterator   = $filereader->getLineIterator($addrStatePath);
        
        $this->insertData($iterator, $analysis);
    }
    
    private function addTable(string $tablename): void
    {
        if (!in_array($tablename, $this->tables))
        {
            $this->tables[] = $tablename;
        }
    }
    
    private function generateFilePathToFixtures(string $tablename): string
    {
        if (!file_exists($filepath = PPA_TEST_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR . "{$tablename}.csv"))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        return $filepath;
    }
    
    private function insertData(Iterator $iterator, Analysis $analysis)
    {
        $header     = explode(";", $iterator->current());
        $properties = $analysis->getPropertiesByColumn();
        $tablename  = $analysis->getTableName();
        
        $this->addTable($tablename);
        $this->transactionManager->begin();
        
        $query     = $this->generateInsertQuery($tablename, $properties);
//        print_r($query);
        $statement = $this->connection->getPdo()->prepare($query);
        
        for ($iterator->next(); $iterator->valid(); $iterator->next())
        {
            $line   = $iterator->current();
            $values = explode(";", $line);
            
            try
            {
                $statement->execute($values);
            }
            catch (PDOException $exc)
            {
                throw new DatabaseException("Failed on query '{$query}' with values (" . implode(",", $values) . ").", 0, $exc);
            }
        }
        
        $this->transactionManager->commit();
    }
    
    private function generateInsertQuery(string $tablename, array $properties): string
    {
        $queryBuilder = new QueryBuilder($this->connection->getDriver());
        $columns = $values = [];
//        [];
        
        foreach ($properties as $columnname => $property)
        {
            /* @var $property EntityProperty */
            
            $columns[] = new _Field($columnname);
            $values[]  = new UnnamedParameter();
        }
        
        $queryBuilder->insert()->intoTable($tablename)->fields(...$columns)->values(...$values);
        
        return $queryBuilder->sql();
    }
    
    public function tearDownFixtures(string ...$classnames)
    {
        if (empty($classnames))
        {
            $tables = $this->tables;
        }
        else
        {
            $tables = [];
            
            foreach ($classnames as $classname)
            {
                $analysis = $this->entityAnalyser->analyse($classname);
                $tables[] = $analysis->getTableName();
            }
        }
        
        $tables = array_reverse($tables); // to respect foreign keys
        
        foreach ($tables as $table)
        {
            $this->emptyTable($table);
            unset($this->tables[array_search($table, $this->tables)]);
        }
    }
    
    private function emptyTable(string $tablename)
    {
        $this->transactionManager->begin();
        
        $query     = $this->generateDeleteQuery($tablename);
//        print_r($query);
        $statement = $this->connection->getPdo()->query($query);
        
        $this->transactionManager->commit();
    }
    
    private function generateDeleteQuery(string $tablename): string
    {
        $queryBuilder = new QueryBuilder($this->connection->getDriver());
        $queryBuilder->delete()->fromTable($tablename);
        
        return $queryBuilder->sql();
    }
    
}

?>
