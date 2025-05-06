<?php

namespace Tests\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\FilterParsers\SearchFilterParser;
use Sowl\JsonApi\Request;

class FilterStringParserTest extends TestCase
{
    public function testArrayKeySearch(): void
    {
        $request = new Request(['filter' => ['search' => 'queryString']]);
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }

    public function testPropertyQueryParser(): void
    {
        $request = new Request(['filter' => ['search' => 'queryString']]);
        $parser = new SearchFilterParser($request, 'testField');

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var Comparison $where */
        $where = $criteria->getWhereExpression();

        $this->assertEquals('testField', $where->getField());
        $this->assertEquals(Comparison::CONTAINS, $where->getOperator());
        $this->assertEquals('queryString', $where->getValue()->getValue());
    }
}
