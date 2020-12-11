<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        'The Walking Dead' => [
            'summary' => 'Sheriff Deputy Rick Grimes wakes up from a coma to learn the world is in ruins and must lead a group of survivors to stay alive.',
            'category' => 'categorie_4',
            ],
            'The Haunting Of Hill House' => [
                'summary' => 'Flashing between past and present, a fractured family confronts haunting memories of their old home and the terrifying events that drove them from it.',
                'category' => 'categorie_4',
                ],
            'American Horror Story' => [
                'summary' => 'An anthology series centering on different characters and locations, including a house with a murderous past, an insane asylum, a witch coven, a freak show circus, a haunted hotel, a possessed farmhouse, a cult, the apocalypse, and a slasher summer camp.',
                'category' => 'categorie_4',
                ],
            'Love Death And Robots' => [
                'summary' => 'A collection of animated short stories that span various genres including science fiction, fantasy, horror and comedy.',
                'category' => 'categorie_4',
                ],
            'Penny Dreadful' => [
                'summary' => 'Explorer Sir Malcolm Murray, American gunslinger Ethan Chandler, scientist Victor Frankenstein and medium Vanessa Ives unite to combat supernatural threats in Victorian London.',
                'category' => 'categorie_4',
                ],
            'Fear The Walking Dead' => [
                'summary' => 'A Walking Dead spin-off, set in Los Angeles, following two families who must band together to survive the undead apocalypse.',
                'category' => 'categorie_4',
                ],
            'The Crown' => [
                'summary' => 'Follows the political rivalries and romance of Queen Elizabeth II\'s reign and the events that shaped the second half of the twentieth century.',
                'category' => 'categorie_4',
                ]

        ];

        public function load(ObjectManager $manager)
        {
            $faker = Faker\Factory::create('en_US');
            
            $i = 0;
            foreach (self::PROGRAMS as $title => $data) {  
                $program = new Program();  
                $program->setTitle($title);  
                $program->setSummary($data['summary']);  
                $program->setPoster($faker->imageUrl(500,400));
                $manager->persist($program);  
                $this->addReference('program_'.$i, $program);
                $program->setCategory($this->getReference('category_' .rand(0,5)));
                $i++;
            }  
            $manager->flush();
        }
    
        public function getDependencies()
        {
            return [CategoryFixtures::class];
        }
    }
