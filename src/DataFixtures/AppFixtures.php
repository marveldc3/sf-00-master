<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Note;
use App\Entity\Network;
use App\Entity\Like;
use App\Entity\View;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slugger;
    private $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

       
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
        ];
        $categoryEntities = [];
        foreach ($categories as $title => $icon) {
            $category = new Category();
            $category->setTitle($title)->setIcon($icon);
            $categoryEntities[] = $category;
            $manager->persist($category);
        }

       
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $username = $faker->userName;
            $user->setEmail($this->slugger->slug($username) . '@' . $faker->freeEmailDomain())
                ->setUsername($username)
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s')))
                ->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')))
                ->setImage('https://avatar.iran.liara.run/public/' . $i);

            $users[] = $user;
            $manager->persist($user);
        }

     
        $notes = [];
        foreach ($users as $user) {
            for ($j = 0; $j < 3; $j++) {
                $note = new Note();
                $title = $faker->sentence(4); 
                $note->setTitle($title)
                    ->setSlug($this->slugger->slug($title))
                    ->setContent($faker->paragraph(3))
                    ->setIsPublic($faker->boolean())
                    ->setCreator($user)
                    ->setCategory($faker->randomElement($categoryEntities))
                    ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s')))
                    ->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')));

                $notes[] = $note;
                $manager->persist($note);

            
                $viewCount = $faker->numberBetween(1, 10);
                for ($k = 0; $k < $viewCount; $k++) {
                    $view = new View();
                    $view->setIpAdress($faker->ipv4)
                        ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')))
                        ->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 week')->format('Y-m-d H:i:s')))
                        ->setNote($note);
                    $manager->persist($view);
                }
            }
        }

       
        foreach ($notes as $note) {
            $likeCount = $faker->numberBetween(0, 3);
            for ($k = 0; $k < $likeCount; $k++) {
                $like = new Like();
                $like->setNote($note)
                    ->setCreator($faker->randomElement($users));
                $manager->persist($like);
            }
        }

     
        $networkTypes = ['Twitter', 'LinkedIn', 'GitHub', 'Facebook'];
        foreach ($users as $user) {
            $networkCount = $faker->numberBetween(0, 2);
            $shuffledNetworks = $faker->shuffleArray($networkTypes);
            for ($i = 0; $i < $networkCount; $i++) {
                $network = new Network();
                $network->setName($shuffledNetworks[$i])
                    ->setUrl($faker->url)
                    ->setCreator($user);
                $manager->persist($network);
            }
        }

        $manager->flush();
    }
}