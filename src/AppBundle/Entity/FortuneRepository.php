<?php
/**
 * Created by PhpStorm.
 * User: Kelmas2a
 * Date: 19/10/15
 * Time: 12:12
 */

namespace AppBundle\Entity;


class FortuneRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return mixed
     */
    public function findLasts()
    {
        return $this->createQueryBuilder('F')
            ->orderBy("F.createdAt", "DESC")
            ->getQuery()
            ->getResult();

    }

    public function findBestRated()
    {
        return $this->createQueryBuilder('F')
            ->orderBy("F.upVote+F.downVote", "DESC")
            ->getQuery()
            ->getResult();

    }

    public function findAuthor($author)
    {
        return $this->createQueryBuilder('F')
            ->orderBy("F.createdAt", "DESC")
            ->where("F.author = :author")
            ->setParameter("author", $author)
            ->getQuery()
            ->getResult();

    }

    public function findBestOne()
    {
        return $this->createQueryBuilder('F')
            ->setMaxResults(1)
            ->where("F.upVote+F.downVote > 0")
            ->getQuery()
            ->getSingleResult();
    }

    public function findTitle($id)
    {
        return $this->createQueryBuilder('F')
            ->setMaxResults(1)
            ->where("F.id = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->getSingleResult();
    }
}