<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Director;
use Doctrine\ORM\EntityManagerInterface;

class DirectorHelper
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importDirector($name): void
    {
        $new = new Director();
        $new->setName($name);

        $this->entityManager->persist($new);

        $this->entityManager->flush();
    }
}
