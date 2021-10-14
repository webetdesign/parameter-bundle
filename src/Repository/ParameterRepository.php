<?php

namespace WebEtDesign\ParameterBundle\Repository;

use WebEtDesign\ParameterBundle\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function findCached($id): ?Parameter
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->andWhere(
                $qb->expr()->eq('p.code', ':code')
            )
            ->setParameter('code', $id);

        $query = $qb->getQuery();
        $query->setCacheable(true);

        return $query->getOneOrNullResult();
    }
}
