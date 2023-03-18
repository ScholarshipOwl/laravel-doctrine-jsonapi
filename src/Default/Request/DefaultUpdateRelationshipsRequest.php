<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class used for handling update relationships requests.
 *
 * It utilizes the RelationshipsDataRulesTrait trait to define the validation rules required for updating relationships.
 * When processing an update relationships request, the DefaultCreateRelationshipsRequest class ensures that the request
 * data follows the rules defined in the RelationshipsDataRulesTrait.
 */
final class DefaultUpdateRelationshipsRequest extends Request
{
    use RelationshipsDataRulesTrait;
}
