<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User('00000000-0000-4000-b000-000000000001', 'test1@app.by', '$2y$13$s5tSrPPHKcRirIhS8DRrFO/FsNIeIJzoj6P43oAah5nadmK.Y5HRq');
        $user1->activate($user1->getSecurity());
        $manager->persist($user1);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
