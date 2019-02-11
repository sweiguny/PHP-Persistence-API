<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DummyDriver;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderExceptionTest extends TestCase
{
    
    
    public function provideQueryBuilder(): array
    {
        return [
            [new QueryBuilder(new DummyDriver())]
        ];
    }
    
    

}

?>
