<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Note;
use App\Entity\Network;
use App\Entity\Like;
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

        // Categories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            // ... (autres catégories)
        ];
        $categoryEntities = [];
        foreach ($categories as $title => $icon) {
            $category = new Category();
            $category->setTitle($title)->setIcon($icon);
            $categoryEntities[] = $category;
            $manager->persist($category);
        }

        // Users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $username = $faker->userName;
            $user->setEmail($this->slugger->slug($username) . '@' . $faker->freeEmailDomain())
                 ->setUsername($username)
                 ->setPassword($this->hasher->hashPassword($user, 'password'))
                 ->setRoles(['ROLE_USER'])
                 ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s')))
                 ->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')));
            
            // Ajout de l'image de profil (nouvelle propriété)
            $user->setImage($faker->imageUrl(640, 480, 'people'));
            
            $users[] = $user;
            $manager->persist($user);
        }

        // Notes
        $notes = [];
        foreach ($users as $user) {
            for ($j = 0; $j < 10; $j++) {
                $note = new Note();
                $title = $faker->sentence();
                $note->setTitle($title)
                     ->setSlug($this->slugger->slug($title))
                     ->setContent($faker->paragraphs(4, true))
                     ->setPublic($faker->boolean())
                     ->setViews($faker->numberBetween(100, 1000))
                     ->setAuthor($user)
                     ->setCategory($faker->randomElement($categoryEntities))
                     ->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s')))
                     ->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')));
                
                $notes[] = $note;
                $manager->persist($note);
            }
        }

        // Likes
        foreach ($notes as $note) {
            $likeCount = $faker->numberBetween(0, 20);
            for ($k = 0; $k < $likeCount; $k++) {
                $like = new Like();
                $like->setNote($note)
                     ->setCreator($faker->randomElement($users));
                $manager->persist($like);
            }
        }

        // Networks
        $networkTypes = ['Twitter', 'LinkedIn', 'GitHub', 'Facebook'];
        foreach ($users as $user) {
            foreach ($networkTypes as $type) {
                if ($faker->boolean(70)) {  // 70% chance to have this network
                    $network = new Network();
                    $network->setName($type)
                            ->setUrl($faker->url)
                            ->setCreator($user);
                    $manager->persist($network);
                }
            }
        }

        $manager->flush();
    }
}