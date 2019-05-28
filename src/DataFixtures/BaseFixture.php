<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

abstract class BaseFixture extends Fixture
{
	/** @var ObjectManager */
    private $manager;

    /** @var Generator */
    protected $faker;

    // Making this Random Reference System Reusable
    private $referencesIndex = [];

    abstract protected function loadData(ObjectManager $manager);

	public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->faker = Factory::create();

        $this->loadData($manager);
    }

	protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);
            // store for usage later as App\Entity\ClassName_#COUNT#
            // This reference system is a little "extra" built into Doctrine's fixtures library. 
            // When you add a "reference" from one fixture class, you can fetch it out in another class. 
            // It's super handy when you need to relate entities. And hey, that's exactly what we're trying to do!
            $this->addReference($className . '_' . $i, $entity);
        }
    }

    // this new getRandomReference() does exactly what its name says: 
    // you pass it a class, like the Article class, and it will find a random Article for you:
    // THX SymfonyCast :-)
    protected function getRandomReference(string $className) {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $className.'_') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$className])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }
        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);
        return $this->getReference($randomReferenceKey);
    }

}