<?php

namespace App\Repository;

use App\Entity\WordDensity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WordDensity|null find($id, $lockMode = null, $lockVersion = null)
 * @method WordDensity|null findOneBy(array $criteria, array $orderBy = null)
 * @method WordDensity[]    findAll()
 * @method WordDensity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordDensityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordDensity::class);
    }
}
