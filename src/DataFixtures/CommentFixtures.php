<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// When you have a fixture class that is dependent on another fixture class, 
// you need to implement an interface called DependentFixtureInterface:
class CommentFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Comment::class, 100, function(Comment $comment) {

        	$comment->setContent(
        		$this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
        	);
        	$comment->setAuthorName($this->faker->name);
        	$comment->setCreatedAt($this->faker->dateTimeBetween('-1 months', '-1 seconds'));

        	// $comment->setArticle($this->getReference(Article::class.'_0'));
            // That is much better! Just like that, each comment is related to a random article! 
            // $comment->setArticle($this->getReference(Article::class.'_'.$this->faker->numberBetween(0, 9)));
            // Use getRandomReference()
            $comment->setArticle($this->getRandomReference(Article::class));
        });

        $manager->flush();   
    }

    public function getDependencies(){
        return [ArticleFixtures::class];  
    }
}
