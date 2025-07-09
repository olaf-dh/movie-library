<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;

class ActorHelper
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importActor($name): void
    {
        $new = new Actor();
        $new->setName($name);

        $this->entityManager->persist($new);

        $this->entityManager->flush();
    }
}
