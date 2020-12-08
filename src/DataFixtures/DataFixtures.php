<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\CommentState;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DataFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Creation des roles
        $userRole = new Role();
        $userRole->setName('ROLE_USER');
        $manager->persist($userRole);

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);


        // Creation des utilisateurs
        $users = $this->createUsers($manager, $adminRole, $userRole);
        $manager->flush();

        //Ajout de Catégorie
        $categories = [];
        $categoriesLabel = ['Frisson', 'Horreur', 'Humour', 'Action', 'Héroique'];
        foreach ($categoriesLabel as $category) {
            $newCategory = new Category();
            $newCategory->setName($category);
            $categories[] = $newCategory;
            $manager->persist($newCategory);
        }
        $manager->flush();

        // Création d'articles        
        $articles = [];
        for ($i = 0; $i < 20; $i++) {
            $expr = $i % 3;
            
            $urlPic = '';
            $altPic = '';
            if($expr == 0){
                $urlPic = 'https://i.pinimg.com/originals/2a/d9/b8/2ad9b872a860cbe3b45fcc00d4875ebb.jpg';
                $altPic = 'BATMAN';
            }
            else if ($expr == 1){
                $urlPic = 'https://hdwallpaperim.com/wp-content/uploads/2017/08/23/465413-Flash-superhero-DC_Comics-748x560.jpg';
                $altPic = 'FLASH';

            }              
            else{
                $urlPic = 'https://cutewallpaper.org/21/the-joker-comic-wallpaper/Joker-Comic-Wallpaper-the-best-75+-images-in-2018-.jpg';
                $altPic = 'JOKER';
            }
            $randomPicture = new Picture();
            $randomPicture->setUrl($urlPic)
                ->setAlt($altPic);
            //Random
            $randomDate = $faker->dateTimeBetween('-30 years', 'now', 'Europe/Paris');
            $content = $faker->paragraph($faker->numberBetween(20, 50), true);
            //IndexRandom
            $indexRandomUser = $faker->numberBetween(0, 3);
            $indexRandomCategory = $faker->numberBetween(0, count($categories) - 1);

            $article = new Article();
            if($i == 1){
                
            $article->setTitle($faker->word)
            ->setUser($users[$indexRandomUser])
            ->setCreationDate($randomDate)
            ->setContent($content)
            ->setShared(0)
            ->setLiked(1)
            ->setVisible(true)
            ->setNotification(1)
            ->setPicture($randomPicture);
            }
            $article->setTitle($faker->word)
                ->setUser($users[$indexRandomUser])
                ->setCreationDate($randomDate)
                ->setContent($content)
                ->setShared(0)
                ->setLiked(0)
                ->setVisible(true)
                ->setNotification(1)
                ->setPicture($randomPicture);
            $categoriesNumber = $faker->numberBetween(1 , count($categories) - 1);
            for ($y = 0; $y < $categoriesNumber; $y++) {
                $article->addCategory($categories[$y]);
            }
            $articles[] = $article;

            $manager->persist($article);
        }
        $manager->flush();

        $commentsStates = ['approved', 'reject', 'waiting'];
        $comments = [];
        foreach ($commentsStates as $commentState) {
            $comState = new CommentState();
            $comState->setName($commentState);
            $comments[] = $comState;
            $manager->persist($comState);
        }
        $manager->flush();

        for ($i = 0; $i < 60; $i++) {
            $indexRandomCommentState = $faker->numberBetween(0, count($commentsStates) - 1);

            $contentCom = $faker->sentence($faker->numberBetween(3, 20), true);
            $intComDate = $faker->dateTimeBetween('-30 years', 'now', 'Europe/Paris');
            $indexRandomUser = $faker->numberBetween(0, count($users) - 1);

            $comment = new Comment();
            $comment
                ->setContent($contentCom)
                ->setCreatedDate($intComDate)
                ->setAuthor($users[$indexRandomUser])
                ->setState($comments[$indexRandomCommentState])
                ->setArticle($articles[$faker->numberBetween(0, count($articles) - 1)]);
            $comments[] = $comment;
            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function createUsers(ObjectManager $manager, $adminRole, $userRole)
    {
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            if ($i < 3) {
                $user->setEmail('admin' . $i . '@admin.com')
                    ->setFirstName('AdminFirstName-' . $i)
                    ->setLastName('AdminLastName-' . $i)
                    ->setPassword($this->encoder->encodePassword($user, 'admin'))
                    ->addUserRole($adminRole);
            } else {
                $user->setEmail('user' . $i . '@user.com')
                    ->setFirstName('UserFirstName-' . $i)
                    ->setLastName('UserLastName-' . $i)
                    ->setPassword($this->encoder->encodePassword($user, 'user'))
                    ->addUserRole($userRole);
            }
            $users[] = $user;
            $manager->persist($user);
        }
        return $users;
    }
}
