<?php

namespace VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compteur
 *
 * @ORM\Table(name="compteur")
 * @ORM\Entity(repositoryClass="VideoBundle\Repository\CompteurRepository")
 */
class Compteur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="integer", unique=true)
     */
    private $total;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Compteur
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
