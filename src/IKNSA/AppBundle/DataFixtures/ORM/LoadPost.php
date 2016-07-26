<?php

namespace IKNSA\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use IKNSA\BlogBundle\Entity\Post;
use IKNSA\AppBundle\Entity\User;

class LoadPost extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface 
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $post1 = new Post();
        $post1->setTitle('La guinée');
        $post1->setSummary("Pays");
        $post1->setContent("sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.");
        $post1->setExtension("jpeg");
        $post1->setCreatedAt(new \DateTime("2016-07-16 16:14:06"));
        $post1->setUser($this->getReference('user-user'));
        $manager->persist($post1);
        
        $post2 = new Post();
        $post2->setTitle('Conakry');
        $post2->setSummary("capitale");
        $post2->setContent("sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.");
        $post2->setExtension("jpeg");
        $post2->setCreatedAt(new \DateTime("2016-07-16 16:14:06"));
        $post2->setUser($this->getReference('admin-admin'));
        $manager->persist($post2);

        $post3 = new Post();
        $post3->setTitle('Cosa');
        $post3->setSummary("Ville");
        $post3->setContent("sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.");
        $post3->setExtension("jpeg");
        $post3->setCreatedAt(new \DateTime("2016-07-16 16:14:06"));
        $post3->setUser($this->getReference('user-user'));
        $manager->persist($post3);

        $post4 = new Post();
        $post4->setTitle('Pita');
        $post4->setSummary("Ville");
        $post4->setContent("sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.");
        $post4->setExtension("jpeg");
        $post4->setCreatedAt(new \DateTime("2016-07-16 16:14:06"));
        $post4->setUser($this->getReference('user-user'));
        $manager->persist($post4);
        $manager->flush();
        $this->addReference('post-post', $post);
    }

     public function getOrder()
   {
    return 101;
   }
}