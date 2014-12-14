<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class ActivityRepository extends EntityRepository
{

    public function getCurrentByUser($userId)
    {
        $q = $this->createQueryBuilder('a')
            ->andWhere('a.id_user = :id_user')
            ->andWhere('a.begins_at <= CURRENT_TIMESTAMP()')
            ->andWhere('a.ends_at IS NULL')
            ->setParameter('id_user', $userId)
            ->orderBy('a.begins_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        try {
            return $q->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getAllPingOut()
    {
        $q = $this->createQueryBuilder('a')
            ->andWhere('a.begins_at <= CURRENT_TIMESTAMP()')
            ->andWhere('a.ends_at IS NULL')
            ->andWhere('a.alerted = FALSE')
            ->andWhere('a.ping_limit_at < CURRENT_TIMESTAMP()')
            ->setMaxResults(100)
            ->getQuery();

        return $q->getResult();
    }

}
