<?php

namespace VideoBundle\Repository;

/**
 * CompteurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompteurRepository extends \Doctrine\ORM\EntityRepository {

  public function countFilms() {
    $query = $this->createQueryBuilder('c')
    ->getQuery()
    ->getOneOrNullResult();
    return $query;
  }

}
