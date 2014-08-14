<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/**
 * @table = 'right'
 */
class Right extends Entity {
    
    /**
     * @id = generatedValue
     * @column(name="id")
     */
    private $id;
    
    /**
     * @column(name="name")
     */
    private $desc;
    
    public function __construct($desc) {
        $this->desc = $desc;
    }

}

?>
