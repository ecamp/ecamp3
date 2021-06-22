<?php

namespace App\ContentNodeStrategy;

use App\Entity\ContentNode;
use App\Entity\SingleText;
use App\Repository\SingleTextRepository;
use Doctrine\ORM\EntityManagerInterface;

class SingleTextStrategy extends ContentNodeStrategy {
    private SingleTextRepository $singleTextRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SingleTextRepository $singleTextRepository, EntityManagerInterface $entityManager) {
        $this->singleTextRepository = $singleTextRepository;
        $this->entityManager = $entityManager;
    }

    public function contentNodeCreated(ContentNode $contentNode, ?ContentNode $prototype = null): void {
        parent::contentNodeCreated($contentNode, $prototype);

        $data = ['text' => null];
        if (isset($prototype)) {
            /** @var SingleText $singleText */
            $singleText = $this->singleTextRepository->findOneByContentNode($prototype);
            $data = ['text' => $singleText->getText()];
        }
        $singleText = new SingleText();
        $singleText->setText($data['text']);
        $singleText->setContentNode($contentNode);
        $this->entityManager->persist($singleText);
    }

    public function getContentTypeEntities(): array {
        return [SingleText::class];
    }
}
