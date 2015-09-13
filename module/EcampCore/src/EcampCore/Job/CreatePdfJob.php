<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractJobBase;
use EcampLib\Job\JobResultInterface;
use EcampLib\Printable\PrintableInterface;
use EcampLib\ServiceManager\PrintableManager;
use mikehaertl\shellcommand\Command;
use mikehaertl\tmp\File;
use mikehaertl\wkhtmlto\Pdf;
use Zend\View\Model\ViewModel;

class CreatePdfJob extends AbstractJobBase implements JobResultInterface
{
    const PAGE = 'PAGE';
    const TOC = 'TOC';

    /** @return \Zend\View\View */
    private function getView()
    {
        return $this->getService('View');
    }

    /** @return array */
    protected function getConfig()
    {
        return $this->getService('Config');
    }

    /** @return PrintableManager */
    protected function getPrintableManager()
    {
        return $this->getService('PrintableManager');
    }

    public function __construct(array $pages = null)
    {
        parent::__construct();

        $this->header = null;
        $this->footer = null;

        $this->pages = $pages ?: array();
    }

    public function addPage($items = array())
    {
        $this->pages[] = array('type' => self::PAGE, 'items' => $items);
    }

    public function addToc($options = array())
    {
        $this->pages[] = array('type' => self::TOC, 'options' => $options);
    }

    public function setHeader($header)
    {
        $this->header = $header;
    }

    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    public function execute()
    {
        $pdf = $this->getPdfFactory();

        foreach ($this->pages as $page) {
            if ($page['type'] == self::PAGE) {
                $pdf->addPage($this->renderPage($page));
            }

            if ($page['type'] == self::TOC) {
                $pdf->addToc($page['options']);
            }
        }

        echo $this->createPdf($pdf);
    }

    private function getPdfFactory()
    {
        $config = $this->getService('Config');
        $config = $config['wkhtmltopdf']['config'];

        if ($this->header != null) {
            $config['header-html'] = $this->header;
        }

        if ($this->footer != null) {
            $config['footer-html'] = $this->footer;
        }

        return new Pdf($config);
    }

    private function renderPage($page)
    {
        $content = "";

        foreach ($page['items'] as $item) {
            /** @var PrintableInterface $printable */
            $printable = $this->getPrintableManager()->get($item['name']);
            $viewModel = $printable->create($item);

            if ($viewModel != null) {
                $content .= $this->render($viewModel);
            }
        }

        $pageModel = new ViewModel();
        $pageModel->setTemplate('ecamp-core/pdf/page.twig');
        $pageModel->setVariable('content', $content);
        $pageModel->setVariable('__BASE_URL__', __BASE_URL__);

        return $this->render($pageModel);
    }

    private function createPdf(Pdf $pdf)
    {
        $pdfFilename = __DATA__ . '/print/' . $this->getId() . '.pdf';

        $pdf->saveAs($pdfFilename);

        $tmpA4 = new File('', '.pdf', 'tmp_wkhtmlto_pdf_', __DATA__ . '/tmp');

        $command = new Command(__VENDOR__ . '/coherentgraphics/cpdf-binaries/OSX-Intel/cpdf');
        $command->addArg('-scale-page', array('0.5 0.5'));
        $command->addArg('-o', array($tmpA4->getFileName()));
        $command->addArg($pdfFilename);

        if ($command->execute()) {
            $tmpA4->saveAs($pdfFilename);

            return $pdfFilename;
        } else {
            echo $command->getError();
            die((string) $command->getExitCode());
        }
    }

    public function getResult()
    {
        $pdfFilename = __DATA__ . '/print/' . $this->getId() . '.pdf';

        if (file_exists($pdfFilename)) {
            return $pdfFilename;
        }

        return null;
    }

    /**
     * @param  ViewModel $viewModel
     * @return string
     */
    protected function render(ViewModel $viewModel)
    {
        $viewModel->setOption('has_parent', true);

        /** @noinspection PhpVoidFunctionResultUsedInspection */

        return $this->getView()->render($viewModel);
    }

}
