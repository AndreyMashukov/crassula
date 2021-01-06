<?php

namespace App\Repository;

use App\Entity\Rate;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Rate find($id, $lockMode = null, $lockVersion = null)
 * @method null|Rate findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function getListQB(string $source, DateTimeInterface $date): QueryBuilder
    {
        $builder = $this->createQueryBuilder('rte');

        return $builder
            ->andWhere($builder->expr()->eq('rte.source', ':source'))
            ->andWhere($builder->expr()->eq('rte.date', ':date'))
            ->setParameter('date', $date)
            ->setParameter('source', $source)
        ;
    }
}
