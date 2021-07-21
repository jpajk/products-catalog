<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i<100; $i++) {
            $manager->persist(
                (new Product())
                    ->setTitle($faker->words(5, true))
                    ->setPrice($faker->randomFloat(2, 5, 200))
            );
        }

        $manager->flush();
    }
}
