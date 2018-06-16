<?php

namespace VideoBundle\Repository;

/**
 * FilmRepository
 */
class FilmRepository extends \Doctrine\ORM\EntityRepository {

    /**
     * Cette méthode cherche les films correspondants à la $category
     */
    public function findFilmsByCategory($category) {
        $query = $this->createQueryBuilder('f')
                ->where('f.categorie = :category')
                ->setParameter("category", $category)
                ->orderBy('f.dateAjout', 'DESC')
                ->getQuery()
                ->execute();
        return $query;
    }

    /**
     * Cette méthode permet de chercher des films selon les critères titre, category et date d'ajout
     */
    public function findFilms($titre, $category, $date) {
        if ($titre == '' && $date == '') {
            $query = $this->createQueryBuilder('f')->where('f.categorie = :category')
                    ->setParameter("category", $category)
                    ->getQuery();
        }
        if ($titre != '' && $date == '') {
            $query = $this->createQueryBuilder('f')
                    ->where('f.categorie = :category AND f.titre LIKE :titre')
                    ->setParameters(["category" => $category, "titre" => '%' . $titre . '%'])
                    ->getQuery();
        }
        if ($titre != '' && $date != '') {
            $query = $this->createQueryBuilder('f')
                    ->where('f.categorie = :category AND f.titre LIKE :titre AND f.dateAjout LIKE :date')
                    ->setParameters(["category" => $category, "titre" => '%' . $titre . '%', "date" => '%' . $date . '%'])
                    ->getQuery();
        }
        if ($titre == '' && $date != '') {
            $dateArray = explode('-', $date);
            //Transformation de la date en format Y-m-d comme elle est enregistrée dans la base de donnée
            $date = $dateArray[2] . "-" . $dateArray[1] . "-" . $dateArray[0];
            $query = $this->createQueryBuilder('f')
                    ->where('f.categorie = :category AND f.dateAjout LIKE :date')
                    ->setParameters(["category" => $category, "date" => '%' . $date . '%'])
                    ->getQuery();
        }
        return $query->execute();
    }

}
