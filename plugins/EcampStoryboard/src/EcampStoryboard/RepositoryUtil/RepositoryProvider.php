<?php

namespace EcampStoryboard\RepositoryUtil;

use EcampCore\RepositoryUtil\RepositoryProvider as ProviderParent;

class RepositoryProvider extends ProviderParent
{

    /**
     * @return EcampStoryboard\Repository\SectionRepository
     */
    public function ecampStoryboard_SectionRepo()
    {
        return $this->serviceLocator->get('ecampstoryboard.repo.section');
    }


}

