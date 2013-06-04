<?php

namespace EcampDB\Controller;

use Zend\View\Model\ViewModel;

use EcampLib\Entity\UId;

use Zend\Code\Reflection\ClassReflection;
use Zend\Mvc\Controller\AbstractActionController;

class MaintenanceController extends AbstractActionController
{

    public function indexAction()
    {
        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

    public function repairUidAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $uidRepo = $em->getRepository('EcampLib\Entity\Uid');
        $uidRef = new ClassReflection('EcampLib\Entity\Uid');
        $idRef = $uidRef->getProperty('id');
        $idRef->setAccessible(true);

        $uids = $uidRepo->findAll();
        foreach ($uids as $uid) {
            $hasRepo = false;

            foreach ($metadatas as $metadata) {
                if ($metadata->name == $uid->getClass()) {
                    $hasRepo = true;
                    break;
                }
            }

            if ($hasRepo) {
                $repo = $em->getRepository($uid->getClass());
                if (! $repo->findOneBy(array('id' => $uid->getId()))) {
                    $em->remove($uid);
                }
            } else {
                $em->remove($uid);
            }
        }

        foreach ($metadatas as $classMetadata) {
            if (! $classMetadata->isMappedSuperclass
            &&	  $classMetadata->name != 'EcampLib\Entity\Uid'
            ) {
                $repo = $em->getRepository($classMetadata->name);
                $entities = $repo->findAll();

                foreach ($entities as $entity) {
                    if (method_exists($entity, 'getId')) {
                        if (! $uidRepo->findOneBy(array('id' => $entity->getId()))) {
                            $uid = new UId($classMetadata->name);
                            $idRef->setValue($uid, $entity->getId());
                            $em->persist($uid);
                        }
                    }
                }
            }
        }

        $em->flush();

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-db/index/index');

        return $viewModel;
    }

}
