<?php

namespace Spatie\QueryBuilder\Tests;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Tests\TestClasses\Models\SoftDeleteModel;

class FiltersTrashedTest extends TestCase
{
    /** @var \Illuminate\Support\Collection */
    protected $models;

    public function setUp(): void
    {
        parent::setUp();

        $this->models = factory(SoftDeleteModel::class, 2)->create()
            ->merge(factory(SoftDeleteModel::class, 1)->create(['deleted_at' => now()]));
    }

    /** @test */
    public function it_should_filter_not_trashed_by_default()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => '',
            ])
            ->allowedQuickSearches(AllowedFilter::trashed())
            ->get();

        $this->assertCount(2, $models);
    }

    /** @test */
    public function it_can_filter_only_trashed()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => 'only',
            ])
            ->allowedQuickSearches(AllowedFilter::trashed())
            ->get();

        $this->assertCount(1, $models);
    }

    /** @test */
    public function it_can_filter_only_trashed_by_scope_directly()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'only_trashed' => true,
            ])
            ->allowedQuickSearches(AllowedFilter::scope('only_trashed'))
            ->get();

        $this->assertCount(1, $models);
    }

    /** @test */
    public function it_can_filter_with_trashed()
    {
        $models = $this
            ->createQueryFromFilterRequest([
                'trashed' => 'with',
            ])
            ->allowedQuickSearches(AllowedFilter::trashed())
            ->get();

        $this->assertCount(3, $models);
    }

    protected function createQueryFromFilterRequest(array $filters): QueryBuilder
    {
        $request = new Request([
            'filter' => $filters,
        ]);

        return QueryBuilder::for(SoftDeleteModel::class, $request);
    }
}
