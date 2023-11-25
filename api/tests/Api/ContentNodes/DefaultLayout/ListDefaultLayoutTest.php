<?php

namespace App\Tests\Api\ContentNodes\DefaultLayout;

use App\Tests\Api\ContentNodes\ListContentNodeTestCase;

/**
 * @internal
 */
class ListDefaultLayoutTest extends ListContentNodeTestCase {
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/default_layouts';

        $this->contentNodesCamp1and2 = [
            $this->getIriFor('defaultLayout1'),
        ];

        $this->contentNodesCampUnrelated = [
            $this->getIriFor('defaultLayoutCampUnrelated'),
        ];
    }
}
