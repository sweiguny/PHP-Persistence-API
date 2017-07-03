<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/**
 * @table(name = "orderpos")
 */
class OrderPosition extends Entity
{
    /**
     * @id
     * @column(name="id")
     */
    private $id;

    /**
     * @column(name = "order_id")
     */
    private $orderId;

    /**
     * @column
     */
    private $article;

    /**
     * @column
     */
    private $price;

    public function __construct($article, $price)
    {
        $this->article = $article;
        $this->price   = $price;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getArticle()
    {
        return $this->article;
    }

    public function setArticle($article)
    {
        $this->article = $article;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

}

?>
