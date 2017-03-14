<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProductBundle\Entity\Variety;

/**
 * @Route("/producer/product/variety")
 */
class VarietyController extends Controller
{
    /**
     * @Route("/get")
     */
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = $request->query->get('suggestion');
        $family = $request->query->get('family');

        $varieties = $em->
            getRepository('ProductBundle:Variety')
            ->createQueryBuilder('v')
            ->select('v');

        if ($family) {
            $Family = $em->getRepository('ProductBundle:Family')->findOneByName($family);
            $varieties->where('v.Family = :family')
                ->setParameter('family', $Family);
        }

        $q = $varieties->getQuery();
        $varieties = $q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($varieties);

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
        $Group = $em->getRepository('ProductBundle:ProductGroup')->findOneBy(array('name' => $group));
        $family = $request->request->get('family');
        $Family = $em->getRepository('ProductBundle:Family')->findOneBy(array('name' => $family, 'Group' => $Group));

        if (null === $Group) {
            $response = new JsonResponse();
            $response->setData(array('msg' => 'Error saving variety data. Group is missing.'));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $response;
        }

        if (null === $Family) {
            $response = new JsonResponse();
            $response->setData(array('msg' => 'Error saving variety data. Family is missing.'));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            return $response;
        }

        try {
            $variety = new Variety();
            $variety->setName($suggestion);
            $variety->setFamily($Family);

            $em->persist($variety);
            $em->flush();
        } catch (\Exception $e) {
        }

        $varieties = $em->
            getRepository('ProductBundle:Variety')
            ->createQueryBuilder('v')
            ->select('v');

        if ($family) {
            $varieties->where('v.Family = :family')
                ->setParameter('family', $Family);
        }

        $q = $varieties->getQuery();
        $varieties = $q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($varieties);
        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }
}
