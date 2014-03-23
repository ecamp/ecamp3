<?php

namespace EcampMaterial\Controller;

use Doctrine\ORM\Query\Expr;

use Doctrine\ORM\Query;

use Zend\View\Model\JsonModel;

use EcampCore\Controller\AbstractEventPluginController;

class DictionaryController extends AbstractEventPluginController
{

    private function getDictionaryRepo()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Repository\MaterialDictionary');
    }

    public function indexAction()
    {
        $query = $this->params('query');
        $qb = $this->getDictionaryRepo()->createQueryBuilder('e');

        $dictionary = $qb
            ->select('e')
            ->add('orderBy', 'e.text ASC')
            ->where($qb->expr()->like('e.text', $qb->expr()->literal('%'.$query.'%')))
            ->setMaxResults(20)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        $result = new JsonModel($dictionary);

        return $result;
    }

}
