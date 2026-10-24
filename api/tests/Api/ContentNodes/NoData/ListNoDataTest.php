<?php

declare(strict_types=1);

namespace App\Tests\Api\ContentNodes\NoData;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListNoDataTest extends ListContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpointBase = '/content_node/no_datas';

        $this->contentNodesCamp1 = [
            $this->getIriFor('noData1'),
        ];
        $this->contentNodesCamp2 = [
            // none
        ];
        $this->contentNodesCampUnrelated = [
            $this->getIriFor('noDataCampUnrelated'),
        ];
        $this->contentNodesCampPrototype = [
            $this->getIriFor('noDataCampPrototype'),
        ];
        $this->contentNodesCampShared = [
            $this->getIriFor('noDataCampShared'),
        ];
    }
}
