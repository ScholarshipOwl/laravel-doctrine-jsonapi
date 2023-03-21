<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class represents a request specifically for updating single resource.
 *
 * It utilizes the ResourceDataRulesTrait trait to define the validation rules required for updating resources.
 * By using the DefaultCreateRequest class, you can ensure that the incoming request data is validated before updating
 * resource
 */
final class UpdateResourceRequest extends Request
{
    use ResourceDataRulesTrait;
}
