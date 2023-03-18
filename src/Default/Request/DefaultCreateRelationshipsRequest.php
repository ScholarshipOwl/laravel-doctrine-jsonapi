<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class used for handling create relationships requests.
 *
 * It utilizes the RelationshipsDataRulesTrait trait to define the validation rules required for creating relationships
 * between resources.
 *
 * When processing a create relationships request, the DefaultCreateRelationshipsRequest class ensures that the request
 * data follows the rules defined in the RelationshipsDataRulesTrait.
 */
final class DefaultCreateRelationshipsRequest extends Request
{
    use RelationshipsDataRulesTrait;
}
