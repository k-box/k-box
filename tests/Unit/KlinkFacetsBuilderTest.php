<?php

namespace Tests\Unit;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\KlinkFacets;
use KSearchClient\Model\Data\Aggregation;

class KlinkFacetsBuilderTest extends TestCase
{
    public function testBuilderAllNames()
    {
        $current = KlinkFacetsBuilder::allNames();

        $this->assertEquals(array_values(KlinkFacets::enums()), $current);
    }

    public function testBuilderAll()
    {
        $enums = KlinkFacets::enums();
        $facets = KlinkFacetsBuilder::all();

        $this->assertEquals(count($enums), count($facets));
        $this->assertContainsOnlyInstancesOf(Aggregation::class, $facets);
    }

    public function testAggregateMethodInsertAggregations()
    {
        $facets = KlinkFacetsBuilder::aggregate([
            KlinkFacets::LANGUAGE
        ])->buildAggregations();

        $this->assertEquals(1, count($facets));
        $this->assertContainsOnlyInstancesOf(Aggregation::class, $facets);
        $this->assertArrayHasKey(KlinkFacets::LANGUAGE, $facets);
    }
}
