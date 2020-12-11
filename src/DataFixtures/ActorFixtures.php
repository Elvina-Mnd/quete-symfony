<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = ['Claire Foy','Olivia Colman', 'Kim Dickens', 'Eva Green', 'Lady Gaga', 'Carla Gugino', 'Victoria Pedretti'];

    public function load(ObjectManager $manager)
    {
        foreach(self::ACTORS as $key =>$actorName){
        $actor = new Actor();
        $actor->setName($actorName);
        $actor->addProgram($this->getReference('program_0'));
        $manager->persist($actor);
        }

        $faker  =  Faker\Factory::create('en_US');
        for ($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $manager->persist($actor);
            $actor->addProgram($this->getReference('program_' . $faker->numberBetween(0,5)));
            $this->addReference('actor_'.$i,$actor);
        }
        $manager->flush();
    }

    public function getDependencies()  
    {
        return [ProgramFixtures::class];  
    }
}