<?php

namespace App\Repository;

use App\Entity\AdherentDebtor;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @param float|null $min
     * @param float|null $max
     * @param int|null $adherent
     * @param int|null $debtor
     * @param string|null $sortField
     * @param string|null $sortType
     * @return QueryBuilder
     */
    public function getFilteredData(?float $min, ?float $max, ?int $adherent, ?int $debtor, ?string $sortField, ?string $sortType)
    {
        $qb = $this->createQueryBuilder('i');
            if ($min && $max) {
                $qb->andWhere('i.invoiceAmount BETWEEN :min AND :max')
                   ->setParameter('min', $min)
                   ->setParameter('max', $max);
            }

            if ($adherent) {
                $qb->andWhere('i.adherent =:adherent')
                   ->setParameter('adherent', $adherent);
            }

            if ($debtor) {
                 $qb->andWhere('i.debtor =:debtor')
                    ->setParameter('debtor', $debtor);
            }

            if ($sortField && $sortType) {
                $qb->orderBy('i.'.$sortField, $sortType);
            }

            return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getWithSearchQueryBuilder()
    {
        return $this->createQueryBuilder('i')
            ->select('i')
        ;
    }


    /**
     * @param QueryBuilder $qb
     * @return array
     */
    public function getTotalAmounts(QueryBuilder $qb)
    {
          return  $qb->select('SUM(i.requestedAmount) as totalRequestedAmount,SUM(i.invoiceAmount) as totalInvoiceAmount, SUM(i.approvedAmount) as totalApprovedAmount')
                     ->getQuery()
                     ->getArrayResult()
          ;
    }

    /**
     * @return array
     */
    public function getApprovedRange()
    {
        return $this->createQueryBuilder('i')
            ->select('MAX(i.approvedAmount) as max, MIN(i.approvedAmount) as min')
            ->getQuery()
            ->getArrayResult()
        ;
    }


}
