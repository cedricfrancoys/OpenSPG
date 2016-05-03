<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\ContactType;
use AppBundle\Entity\Contact;
use Symfony\Component\Form\Test\TypeTestCase;

class TestedTypeTest extends TypeTestCase
{
    public function testSubmitValidGenericReceivedData()
    {
        $formData = array(
            'name' => 'Miguel Hauptmann',
            'email' => 'mhauptma73@gmail.com',
            'message' => 'A simple test message',
            'received' => new \DateTime(),
        );

        $form = $this->factory->create(ContactType::class);

        $object = Contact::fromArray($formData);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
