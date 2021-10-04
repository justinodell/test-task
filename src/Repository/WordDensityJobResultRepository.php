<?php

namespace App\Repository;

use App\Entity\WordDensityJobResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WordDensityJobResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method WordDensityJobResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method WordDensityJobResult[]    findAll()
 * @method WordDensityJobResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordDensityJobResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordDensityJobResult::class);
    }
}
