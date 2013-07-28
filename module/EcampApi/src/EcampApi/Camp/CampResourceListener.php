<?php
namespace EcampApi\Camp;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class CampResourceListener extends AbstractListenerAggregate
{
    protected $campRepo;

    public function __construct(\EcampCore\Repository\CampRepository $campRepo)
    {
        $this->campRepo = $campRepo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('create', array($this, 'onCreate'));
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onCreate(ResourceEvent $e)
    {
        $data  = $e->getParam('data');
        //$camp = $this->persistence->save($data);
        if (!$camp) {
            throw new CreationException();
        }
        return $camp;
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $camp = $this->campRepo->find($id);
        if (!$camp) {
            throw new DomainException('Camp not found', 404);
        }
        return $camp;
        //$campSerializer = new CampArray();
        
        //return $campSerializer->toArray($camp);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	return $this->campRepo->findAll();
    }
}