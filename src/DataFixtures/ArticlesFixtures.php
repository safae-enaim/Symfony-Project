<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticlesFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);

        $userRole = new Role();
        $userRole->setName('ROLE_USER');
        $manager->persist($userRole);

        //Ajout user admin article
        $user = new User();
        $user->setEmail('adminArticle@ex.com')
             ->setPassword($this->encoder->encodePassword($user, 'admin'))
             ->addUserRole($userRole)
             ->addUserRole($adminRole);

        $manager->persist($user);
        $manager->flush();

             //Ajout user admin comment
        $userC = new User();
        $userC->setEmail('adminComment@ex.com')
             ->setPassword($this->encoder->encodePassword($userC, 'admin'))
             ->addUserRole($userRole)
             ->addUserRole($adminRole);

        $manager->persist($userC);
        $manager->flush();

        // Création de commentaires
        $comments = [];
        $intComDate = $faker->dateTimeBetween('-6 month', '+6 month', 'Europe/Paris');
        for($i=0; $i <60; $i++)
        {
            $contentCom = $faker->sentence($faker->numberBetween(3, 20), true);
            $comment = new Comment();    
            $comment
                ->setContent($contentCom)
                ->setCreatedDate($intComDate)
                ->setAuthor($userC);
            $comments[] = $comment;
            $manager->persist($comment);
        }
        $manager->flush();
        
        
        //Utilisation d'image local
        //$picture = $faker->image($dir = '/tmp', $width = 640, $height = 480);
        $picture = $faker->imageUrl($width = 640, $height = 480);
        $content = $faker->paragraph($faker->numberBetween(1, 10), true);
        $intDate = $faker->dateTimeBetween('-6 month', '+6 month', 'Europe/Paris');
        $updateDate = $faker->dateTimeBetween('-5 month', '+5 month', 'Europe/Paris');
        $deleteDate = $faker->dateTimeBetween('-4 month', '+4 month', 'Europe/Paris');
        
        // Création d'articles
        $articles = [];
        for($i=0; $i <20; $i++)
        {
            $article = new Article();
            
            $article->addComment($comments[$faker->numberBetween(0, count($comments))]);
                
            $article->setTitle($faker->word)
                    ->setPicture($picture)
                    ->setUser($user->getId())
                    ->setCreationDate($intDate)
                    ->setUpdatedDate($updateDate)
                    ->setDeletedDate($deleteDate)
                    ->setContent($content)
                    ->setShared(0)
                    ->setLiked(0)
                    ->setVisible(true);
            $articles[] = $article;
            $manager->persist($article);
        }
        $manager->flush();

        
    }
}
