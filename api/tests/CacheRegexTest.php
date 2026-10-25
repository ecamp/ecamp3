<?php

namespace App\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use function PHPUnit\Framework\assertDoesNotMatchRegularExpression;
use function PHPUnit\Framework\assertMatchesRegularExpression;

/**
 * @internal
 */
class CacheRegexTest extends KernelTestCase {
    private string $cacheRegex;

    protected function setUp(): void {
        $this->cacheRegex = '{'.$this->getContainer()->getParameter('app.httpCache.matchPath').'}';
    }

    #[TestWith(data: [''], name: '')]
    #[TestWith(data: ['/'], name: '/')]
    #[TestWith(data: ['index.jsonhal'], name: '/index.jsonhal')]
    #[TestWith(data: ['/camps/25a82475e0b7/activities'], name: '/camps/25a82475e0b7/activities')]
    #[TestWith(data: ['/camps/25a82475e0b7/categories'], name: '/camps/25a82475e0b7/categories')]
    #[TestWith(data: ['/camps/25a82475e0b7/checklists'], name: '/camps/25a82475e0b7/checklists')]
    #[TestWith(data: ['/content_types'], name: '/content_types')]
    #[TestWith(data: ['/content_types/25a82375a0b6'], name: '/content_types/25a82375a0b6')]
    #[TestWith(data: ['/periods/25a82475e0b7/schedule_entries'], name: '/periods/25a82475e0b7/schedule_entries')]
    public function testIncludesUrls(string $url) {
        assertMatchesRegularExpression($this->cacheRegex, $url);
    }

    #[TestWith(data: ['/camps/25a82475e0b7/categories/c53dd7917e63'], name: '/camps/25a82475e0b7/categories/c53dd7917e63')]
    public function testAlsoIncludesUrls(string $url) {
        assertMatchesRegularExpression($this->cacheRegex, $url);
    }

    #[TestWith(data: ['/invitations'], name: '/invitations')]
    #[TestWith(data: ['/personal_invitations'], name: '/personal_invitations')]
    #[TestWith(data: ['/activity_progress_labels'], name: '/activity_progress_labels')]
    #[TestWith(data: ['/activity_responsibles'], name: '/activity_responsibles')]
    #[TestWith(data: ['/camp_collaborations'], name: '/camp_collaborations')]
    #[TestWith(data: ['/categories'], name: '/categories')]
    #[TestWith(data: ['/checklists'], name: '/checklists')]
    #[TestWith(data: ['/checklist_items'], name: '/checklist_items')]
    #[TestWith(data: ['/content_nodes'], name: '/content_nodes')]
    #[TestWith(data: ['/content_node/checklist_nodes'], name: '/content_node/checklist_nodes')]
    #[TestWith(data: ['/content_node/column_layouts'], name: '/content_node/column_layouts')]
    #[TestWith(data: ['/content_node/material_nodes'], name: '/content_node/material_nodes')]
    #[TestWith(data: ['/content_node/multi_selects'], name: '/content_node/multi_selects')]
    #[TestWith(data: ['/content_node/storyboards'], name: '/content_node/storyboards')]
    #[TestWith(data: ['/days'], name: '/days')]
    #[TestWith(data: ['/day_responsibles'], name: '/day_responsibles')]
    #[TestWith(data: ['/material_items'], name: '/material_items')]
    #[TestWith(data: ['/material_lists'], name: '/material_lists')]
    #[TestWith(data: ['/periods'], name: '/periods')]
    #[TestWith(data: ['/profiles'], name: '/profiles')]
    #[TestWith(data: ['/schedule_entries'], name: '/schedule_entries')]
    #[TestWith(data: ['/users'], name: '/users')]
    public function testDoesNotIncludeUrls(string $url) {
        assertDoesNotMatchRegularExpression($this->cacheRegex, $url);
    }
}
