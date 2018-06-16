<?php

namespace VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="VideoBundle\Repository\CategoryRepository")
 * @UniqueEntity(fields={"nom"},errorPath="titre",message="Cette catégorie existe déjà")
 */
class Category {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max=100, minMessage = "Le nom doit faire au minimum {{ limit }} caractères",
     *      maxMessage = "Le nom doit faire au maximum {{ limit }} caractères")
     * @ORM\Column(name="nom", type="string", length=100, unique=true)
     */
    private $nom;

    /**
     * @OneToMany(targetEntity="Film", mappedBy="categorie", cascade={"remove"})
     */
    private $films;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->films = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Category
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * Add film
     *
     * @param Film $film
     *
     * @return Category
     */
    public function addFilm(Film $film) {
        $this->films[] = $film;

        return $this;
    }

    /**
     * Remove film
     *
     * @param Film $film
     */
    public function removeFilm(Film $film) {
        $this->films->removeElement($film);
    }

    /**
     * Get films
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilms() {
        return $this->films;
    }

}
