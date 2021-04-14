<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 4; $i++){ 
            $category = new Category();
            $category->setTitle($faker->word())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            for ($j = 1; $j <= mt_rand(4, 6); $j++)
            { 
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>').'</p>';

                $article = new Article();  
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage('https://picsum.photos/450/450')
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                        
                $manager->persist($article);

                for ($k = 1; $k <= mt_rand(3, 9); $k++){ 
                    $comment = new Comment();
                    $content = '<p>' .join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $now = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $now . ' days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
