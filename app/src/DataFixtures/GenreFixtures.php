<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $genres = [
            'Action',
            'Drama',
            'Thriller',
            'Fantasy',
            'Romance',
            'Adventure',
            'Sci-Fi',
            'Horror',
            'Western',
            'Crime',
            'Porn',
            'Comedy',
            'Mystery',
            'Espionage',
            'Martial arts',
            'Biography',
            'Short',
            'Animation',
            'War',
            'Family',
            'Sport',
            'News',
            'Documentary',
            'Music',
            'History',
            'Musical'
        ];

        foreach ($genres as $item) {
            $genre = new Genre();
            $genre->setName($item);
            $manager->persist($genre);
        }
        $manager->flush();
    }
}
