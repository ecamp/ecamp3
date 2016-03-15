<?php

namespace EcampWeb\Controller\Camp;

use EcampCore\Entity\EventInstance;
use EcampCore\Entity\Medium;
use EcampCore\View\Event\EventTemplateRenderer;
use EcampWeb\Job\CreatePrintPdf;
use mikehaertl\wkhtmlto\Pdf;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

/**
 * Class PrintController
 * @package EcampWeb\Controller\Camp
 *
 * @method \EcampCore\Repository\DayRepository getDayRepository()
 */
class PrintController extends BaseController
{
    /**
     * @return \EcampCore\Repository\PeriodRepository
     */
    private function getPeriodRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Period');
    }

    /**
     * @return \EcampCore\Repository\DayRepository
     */
//    private function getDayRepository()
//    {
//        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
//    }

    /**
     * @return \EcampCore\Repository\EventInstanceRepository
     */
    private function getEventInstanceRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventInstance');
    }

    /**
     * @return \EcampCore\Repository\EventTemplateRepository
     */
    private function getEventTemplateRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventTemplate');
    }

    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    private function getViewRenderer()
    {
        return $this->getServiceLocator()->get('ViewRenderer');
    }

    protected function renderViewModel(ViewModel $viewModel)
    {
        return $this->getViewRenderer()->render($viewModel);
    }

    private function getEventViewModel(EventInstance $eventInstance, Medium $medium)
    {
        $event = $eventInstance->getEvent();
        $eventTemplate = $this->getEventTemplateRepository()->findTemplate($event, $medium);
        $eventTemplateRenderer = new EventTemplateRenderer($eventTemplate);
        $eventTemplateRenderer->buildRendererTree($this->getServiceLocator());

        return $eventTemplateRenderer->render($event, $eventInstance);
    }

    private function getJobState($id)
    {
        $state = ($id == null) ? 'waiting' : 'created';
        $id = $id ?: base_convert(crc32(uniqid()), 10, 16);

        return array(
            'id' => $id,
            'state' => $state,
            'url' => $this->url()->fromRoute(
                'web/camp/default',
                array(
                    'controller' => 'Print',
                    'action' => 'getJobState',
                    'camp' => $this->getCamp()
                ),
                array(
                    'query' => array('id' => $id)
                )
            ),
        );
    }

    public function indexAction()
    {
        return array();
    }

    public function configAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getCampService()->Update(
                $this->getCamp(),
                array(
                    'printConfig' => $this->getRequest()->getContent()
                )
            );
        }

        $resp = $this->getResponse();
        $resp->setContent($this->getCamp()->getPrintConfig());

        return $resp;
    }

    public function createJobAction()
    {
        $request = $this->getRequest();
        $jobConfig = Json::decode($request->getContent());

        // Create Job with $jobConfig
        $job = new CreatePrintPdf();
        $job->setHeader(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'header')));
        $job->setFooter(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'footer')));

        $job->addPage(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'picasso'),
                array('query' => array('periodId' => '9ee9e40b', 'start' => 420, 'end' => 1440))
        ));

        $jobId = $job->enqueue();
        //$job->perform();

        $response = $this->getResponse();
        $response->setContent(Json::encode($this->getJobState($jobId)));

        return $response;
    }

    public function getJobStateAction()
    {
        $id = $this->params()->fromQuery('id');

        $s = new \Resque_Job_Status($id);
        $s->update(2);
        var_dump($s->get());
        die();

        $response = $this->getResponse();

        $response->getHeaders()->addHeaderLine("Content-Type: application/octet-stream");
        $response->getHeaders()->addHeaderLine("Content-Disposition: attachment; filename=test.pdf");
        $response->setContent(Json::encode($this->getJobState($id)));

        return $response;
    }

    public function pdfAction()
    {
        var_dump(Json::decode($this->getRequest()->getPost('config')));
        die();
    }

    public function headerAction()
    {
        return array(
            'frompage' => $this->params()->fromQuery('formpage'),
            'topage' => $this->params()->fromQuery('topage'),
            'page' => $this->params()->fromQuery('page'),
            'webpage' => $this->params()->fromQuery('webpage'),
            'section' => $this->params()->fromQuery('section'),
            'subsection' => $this->params()->fromQuery('subsection'),
            'subsubsection' => $this->params()->fromQuery('subsubsection')
        );
    }

    public function footerAction()
    {
        return array(
            'frompage' => $this->params()->fromQuery('formpage'),
            'topage' => $this->params()->fromQuery('topage'),
            'page' => $this->params()->fromQuery('page'),
            'webpage' => $this->params()->fromQuery('webpage'),
            'section' => $this->params()->fromQuery('section'),
            'subsection' => $this->params()->fromQuery('subsection'),
            'subsubsection' => $this->params()->fromQuery('subsubsection')
        );
    }

    public function coverAction()
    {
        return array();
    }

    public function picassoAction()
    {
        $periodId = $this->params()->fromQuery('periodId');
        $period = $this->getPeriodRepository()->find($periodId);

        $dayStart = $this->params()->fromQuery('start', 5*60);    // 05:00
        $dayEnd = $this->params()->fromQuery('end', 25*60);     // 01:00

        return array(
            'period' => $period,
            'dayStart' => $dayStart,
            'dayEnd' => $dayEnd
        );
    }

    public function dayAction()
    {
        $dayId = $this->params()->fromQuery('dayId');
        $day = $this->getDayRepository()->find($dayId);

        return array(
            'day' => $day
        );
    }

    public function eventAction()
    {
        $medium = $this->getPrintMedium();

        $eventInstanceId  = $this->params()->fromQuery('eventInstanceId');
        $eventInstance = $this->getEventInstanceRepository()->find($eventInstanceId);

        return $this->getEventViewModel($eventInstance, $medium);
    }

    public function allAction()
    {
        $content = "";

        $client = new Client();

//        $cover = new Request();
//        $cover->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
//            array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'cover')
//        ));
//        $responses[] = $client->send($cover);

        $picasso = new Request();
        $picasso->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
            array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'picasso'),
            array('query' => array('periodId' => '9ee9e40b', 'start' => 420, 'end' => 1440))
        ));
        $content .= $client->send($picasso)->getBody();

        $day = new Request();
        $day->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
            array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'day'),
            array('query' => array('dayId' => '96a67797'))
        ));
        $content .= $this->createPage($client->send($day)->getBody());

        $event1 = new Request();
        $event1->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
            array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'event'),
            array('query' => array('eventInstanceId' => '2485e1e6'))
        ));

        $event2 = new Request();
        $event2->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'event'),
                array('query' => array('eventInstanceId' => '524cb8c3'))
            ));

        $event3 = new Request();
        $event3->setUri(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'event'),
                array('query' => array('eventInstanceId' => '8ede8dcb'))
            ));

        $content .= $this->createPage(
            $client->send($event1)->getBody() .
            $client->send($event2)->getBody() .
            $client->send($event3)->getBody()
        );

        return array('content' => $content);
    }

    private function createPage($content)
    {
        return "<div class=\"page\">" . $content . '</div>';

    }

    public function testAction()
    {
        $tempDir = __DATA__ . '/tmp';
        $tempPrefix = 'tmp_wkhtmlto_pdf_';

        $pdf = new Pdf(array(
            'binary' => __VENDOR__ . '/profburial/wkhtmltopdf-binaries-osx/bin/wkhtmltopdf-amd64-osx',
            'tmpDir' => $tempDir,
            'encoding' => 'UTF-8',
            'page-size' => 'A2',

            'disable-smart-shrinking',
            'enable-internal-links',

            'no-outline',
            'margin-top'    => 40,
            'margin-bottom' => 30,
            'margin-left'   => 0,
            'margin-right'  => 0,

            'header-html' => __BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'header')),

            'footer-html' => __BASE_URL__ . $this->url()->fromRoute('web/camp/default',
                    array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'footer')),

        ));

        $pdf->addPage(__BASE_URL__ . $this->url()->fromRoute('web/camp/default',
            array('camp' => $this->getCamp(), 'controller' => 'Print', 'action' => 'all')//,
            //array('query' => array('periodId' => '9ee9e40b'))
        ));

        $pdf->addToc(array(
            'xsl-style-sheet' => __MODULE__ . '/EcampWeb/assets/xsl/toc.xsl',
        ));

        $tmpA3 = new \mikehaertl\tmp\File('', '.pdf', $tempPrefix, $tempDir);
        $tmpA4 = new \mikehaertl\tmp\File('', '.pdf', $tempPrefix, $tempDir);

        $pdf->saveAs($tmpA3->getFileName());

        $command = new \mikehaertl\shellcommand\Command(__VENDOR__ . '/coherentgraphics/cpdf-binaries/OSX-Intel/cpdf');
        $command->addArg('-scale-page', array('0.5 0.5'));
        $command->addArg('-o', array($tmpA4->getFileName()));
        $command->addArg($tmpA3->getFileName());

        if ($command->execute()) {
            $tmpA4->send(null, 'application/pdf', true);
        } else {
            echo $command->getError();
            die($command->getExitCode());
        }

        if (!$pdf->send()) {
            die($pdf->getError());
        }
    }

}
