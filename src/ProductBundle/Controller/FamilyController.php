<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProductBundle\Entity\Family;

/**
 * @Route("/producer/product/family")
 */
class FamilyController extends Controller
{
    /**
     * @Route("/get")
     */
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = $request->query->get('suggestion');
        $group = $request->query->get('group');

        $families = $em->
            getRepository('ProductBundle:Family')
            ->createQueryBuilder('f')
            ->select('f');

        if ($group) {
            $Group = $em->getRepository('ProductBundle:ProductGroup')->findOneByName($group);
            $families->where('f.Group = :group')
                ->setParameter('group', $Group);
        }

        $q = $families->getQuery();
        $families = $q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($families);

        return $response;
    }

    /**
     * @Route("/add")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = $request->request->get('suggestion');
        $group = $request->request->get('group');
        $Group = $em->getRepository('ProductBundle:ProductGroup')->findOneByName($group);

        if (null === $Group) {
            $response = new JsonResponse();
            $response->setData(array('msg' => 'Error saving family data. Group is missing.'));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $response;
        }

        try {
            $family = new Family();
            $family->setName($suggestion);
            $family->setGroup($Group);

            $em->persist($family);
            $em->flush();
        } catch (\Exception $e) {
        }

        $families = $em->
            getRepository('ProductBundle:Family')
            ->createQueryBuilder('f')
            ->select('f');

        if ($group) {
            $families->where('f.Group = :group')
                ->setParameter('group', $Group);
        }

        $q = $families->getQuery();
        $families = $q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($families);
        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }
}
