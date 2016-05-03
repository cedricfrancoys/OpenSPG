<?php

namespace Tests\AppBundle\Form\DataTransformer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use AppBundle\Form\DataTransformer\EntitiesToPropertyTransformer;

use AppBundle\Entity\Contact;

class EntitiesToPropertyTransformerTest extends KernelTestCase
{
    protected $array;
    protected $cls = 'AppBundle\\Entity\\Contact';
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();
    }

    public function testTransform()
    {
        $contact = $this->getMock(Contact::class);
        $contact->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(10));
        $this->array[] = $contact;

        $transformer = new EntitiesToPropertyTransformer($this->em, $this->cls);

        $result = $transformer->transform($this->array);

        $this->assertEquals(1, count($result));
        $this->assertEquals(10, $result[0]);
    }

    public function testReverseTransform()
    {
    	$contact = $this->getMock(Contact::class);
        $this->array[] = $contact;

    	$contactRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contactRepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($this->array[0]));

        $entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($contactRepository));


    	$transformer = new EntitiesToPropertyTransformer($entityManager, $this->cls);

        $result = $transformer->reverseTransform(array(10));

        $this->assertEquals(1, count($result));
        $this->assertEquals($this->array[0], $result[0]);
    }
}