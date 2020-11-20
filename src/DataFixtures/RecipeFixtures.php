<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Comment;
use Faker;

class RecipeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 3; $i++){
            $category = new Category;
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            
            $manager->persist($category);
            $manager->flush();
        }

        for($j = 1; $j <=mt_rand(4,6); $j++){
            $recipe = new Recipe();

            $content =''.join($faker->paragraphs(5), '</p><p>').'';


            $recipe->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 month','now'))
                    ->setCategory($category);

            $manager->persist($recipe);

            for($k = 1; $k <=mt_rand(4,10); $k++){

                $comment = new Comment();

                $content =''.join($faker->paragraphs(2), '</p><p>').'';

                $days = (new \DateTime())->diff($recipe->getCreatedAt())->days;

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween($days))
                        ->setRecipe($recipe);

                $manager->persist($comment);

            }
            $manager->flush();
        }
        
    }
}

