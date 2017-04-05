<?php
namespace Empregos\Repository;

use Doctrine\ORM\EntityRepository;
use Empregos\Entity\Empregos;

/**
 * This is the custom repository class for Post entity.
 */
class EmpregosRepository extends EntityRepository
{
    /**
     * Finds all published posts having any tag.
     * @return array
     */
    public function findPostsHavingAnyTag()
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED);
        
        $posts = $queryBuilder->getQuery()->getResult();
        
        return $posts;
    }
    
    /**
     * Finds all published posts having the given tag.
     * @param string $tagName Name of the tag.
     * @return array
     */
    public function findPostsByTag($tagName)
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->andWhere('t.name = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED)
            ->setParameter('2', $tagName);
        
        //$posts = $queryBuilder->getQuery()->getResult();
        $posts = $queryBuilder->getQuery();
                
        return $posts;
    }        
}