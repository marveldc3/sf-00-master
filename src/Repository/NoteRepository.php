<?php
namespace App\Repository;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }
   public function findByQuery($query): array
    {
        return $this->createQueryBuilder('n')
			->where('n.is_public =true')
            ->andWhere('n.title LIKE :q OR n.content LIKE :q')
            ->setParameter('q','%' .  $query . '%')
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByCreator($id): ?Note
   {
        return $this->createQueryBuilder('n')
		->where('n.is_public =true')
         ->andWhere('n.creator = :id')
          ->setParameter('id', $id)
		  ->orderBy('n.created_at', 'DESC')
		  ->setMaxResults(3)
            ->getQuery()
			->getResult()
       ;
    }
}
