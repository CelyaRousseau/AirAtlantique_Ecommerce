<?php

namespace AirAtlantique\FlightBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AeroportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AirportRepository extends EntityRepository
{
  public function deleteAll()
    {
      $query = $this->createQueryBuilder('deleteAll');

      $query->delete();

      $query->getQuery()->execute();
    }
}
