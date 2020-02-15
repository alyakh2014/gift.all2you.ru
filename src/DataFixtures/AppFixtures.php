<?php


namespace App\DataFixtures;

use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create feedback
            $feedback = new Feedback();
            $feedback->setName('TestName ')
                        ->setSubject('Test Subject')
                        ->setEmail('test@test.ts')
                        ->setMessage("Test message");
            $manager->persist($feedback);
        $manager->flush();
    }
}