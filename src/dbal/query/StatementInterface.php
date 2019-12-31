<?php

namespace PPA\dbal\query;

/**
 *
 * @author siwe
 */
interface StatementInterface
{
    public function execute(): ResultSet;
}

?>
