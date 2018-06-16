<?php

namespace VideoBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Film
 *
 * @ORM\Table(name="films")
 * @ORM\Entity(repositoryClass="VideoBundle\Repository\FilmRepository")
 * @UniqueEntity(fields={"titre"},errorPath="titre",message="Le titre du film existe déjà")
 */
class Film {

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
     * @Assert\NotBlank(message="Un titre doit être renseigné")
     * @Assert\Length(min = 10, minMessage = "Le titre du film doit faire au minimum {{ limit }} caractères")
     * @Assert\Regex(pattern = "/[a-zA-Z0-9]+$/i", message = "Un titre ne peut pas contenir des caractères speciaux")
     * @ORM\Column(name="titre", type="string", length=255, unique=true)
     */
    private $titre;

    /**
     * @var string
     * @Assert\Length(min = 5, minMessage = "Donner au moins {{ limit }} caractères pour la description")
     * @Assert\NotBlank(message="Une description doit être renseignée")
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @Assert\NotNull(message = "Vous devez selectionner une catégorie")
     * @ManyToOne(targetEntity="Category", inversedBy="films")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable = true)
     */
    private $photo;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_ajout", type="datetime")
     */
    private $dateAjout;

    /**
     * Constructor
     */
    public function __construct() {
        $this->dateAjout = new DateTime;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Film
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Film
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Film
     */
    public function setPhoto($photo) {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * Set dateAjout
     *
     * @param DateTime $dateAjout
     *
     * @return Film
     */
    public function setDateAjout($dateAjout) {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return DateTime
     */
    public function getDateAjout() {
        return $this->dateAjout;
    }

    /**
     * Set categorie
     *
     * @param Category $categorie
     *
     * @return Film
     */
    public function setCategorie(Category $categorie = null) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return Category
     */
    public function getCategorie() {
        return $this->categorie;
    }

}
