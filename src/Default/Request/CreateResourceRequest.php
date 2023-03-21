<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class represents a request specifically for creating a new resource.
 *
 * Class uses DefaultResourceDataRulesTrait trait, which provides a default implementation of the dataRules() method.
 * By using the DefaultCreateRequest class, you can ensure that the incoming request data is validated before creating
 * a new resource
 */
final class CreateResourceRequest extends Request
{
    use ResourceDataRulesTrait;
}
