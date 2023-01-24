<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Repository\ProfileRepository;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

class CampCollaborationCreateProcessor extends AbstractPersistProcessor {
    use CampCollaborationSendEmailTrait;

    public function __construct(
        ProcessorInterface $decorated,
        private Security $security,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private ProfileRepository $profileRepository,
        private EntityManagerInterface $em,
        private MailService $mailService,
        private ValidatorInterface $validator
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param CampCollaboration $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): CampCollaboration {
        /** @var CampCollaboration $data */
        $inviteEmail = $data->user?->getEmail() ?? $data->inviteEmail;
        if (CampCollaboration::STATUS_INVITED == $data->status && $inviteEmail) {
            $profileByInviteEmail = $this->profileRepository->findOneBy(['email' => $inviteEmail]);
            if (null != $profileByInviteEmail) {
                $data->user = $profileByInviteEmail->user;
                $data->inviteEmail = null;
                $this->validator->validate($data, ['groups' => ['Default', 'create']]);
            }
            $data->inviteKey = IdGenerator::generateRandomHexString(64);
            $data->inviteKeyHash = $this->passwordHasherFactory->getPasswordHasher('MailToken')->hash($data->inviteKey);
        }

        return $data;
    }

    /**
     * @param CampCollaboration $data
     */
    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
        $this->sendInviteEmail($data);

        $materialList = new MaterialList();
        $materialList->campCollaboration = $data;
        $data->camp->addMaterialList($materialList);
        $this->em->persist($materialList);

        $this->em->flush();
    }
}
