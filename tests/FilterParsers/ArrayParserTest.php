<?php

namespace Tests\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use PHPUnit\Framework\TestCase;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\FilterParsers\ArrayFilterParser;
use Sowl\JsonApi\Request;
use Symfony\Component\HttpFoundation\Response;

class ArrayParserTest extends TestCase
{
    public function testFilterableQueryParserOperatorFilterException(): void
    {
        try {
            $request = new Request(['filter' => [
                'field1' => ['operator' => 'not', 'value' => false],
                'field2' => ['operator' => 'eq', 'value' => 'test2'],
            ]]);

            $parser = new ArrayFilterParser($request, ['field1', 'field2']);
            $parser(Criteria::create());
        } catch (JsonApiException $e) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $e->getCode());
            $this->assertEquals([
                [
                    'code' => 400,
                    'source' => [
                        'field' => 'field1',
                        'operator' => 'not',
                    ],
                    'detail' => 'Unknown operator.',
                ],
            ], $e->errors());
        }
    }

    public function testFilterableQueryParserOperatorFilter(): void
    {
        $request = new Request(['filter' => [
            'field1' => ['operator' => 'neq', 'value' => false],
            'field2' => ['operator' => 'eq', 'value' => 'test2'],
        ]]);
        $parser = new ArrayFilterParser($request, ['field1', 'field2']);

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::NEQ, $expressions[0]->getOperator());
        $this->assertEquals(false, $expressions[0]->getValue()->getValue());
        $this->assertEquals('field2', $expressions[1]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[1]->getOperator());
        $this->assertEquals('test2', $expressions[1]->getValue()->getValue());
    }

    public function testFilterableQueryParserBetweenFilter(): void
    {
        $request = new Request(['filter' => ['field1' => ['start' => 1, 'end' => 10]]]);
        $parser = new ArrayFilterParser($request, ['field1', 'field2']);

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::GTE, $expressions[0]->getOperator());
        $this->assertEquals(1, $expressions[0]->getValue()->getValue());
        $this->assertEquals('field1', $expressions[1]->getField());
        $this->assertEquals(Comparison::LTE, $expressions[1]->getOperator());
        $this->assertEquals(10, $expressions[1]->getValue()->getValue());
    }

    public function testFilterableQueryParserEqualFilter(): void
    {
        $request = new Request(['filter' => ['field1' => 'test1', 'field2' => 'test2', 'field3' => 'test3']]);
        $parser = new ArrayFilterParser($request, ['field1', 'field2']);

        /** @var Criteria $criteria */
        $criteria = $parser(Criteria::create());

        /** @var CompositeExpression $where */
        $where = $criteria->getWhereExpression();

        /** @var Comparison[] $expressions */
        $expressions = $where->getExpressionList();

        $this->assertCount(2, $expressions);
        $this->assertEquals('field1', $expressions[0]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[0]->getOperator());
        $this->assertEquals('test1', $expressions[0]->getValue()->getValue());
        $this->assertEquals('field2', $expressions[1]->getField());
        $this->assertEquals(Comparison::EQ, $expressions[1]->getOperator());
        $this->assertEquals('test2', $expressions[1]->getValue()->getValue());
    }
}
