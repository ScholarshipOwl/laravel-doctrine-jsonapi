<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class used for handling remove relationships requests.
 *
 * It utilizes the RelationshipsDataRulesTrait trait to define the validation rules required for removing relationships.
 *
 * When processing a remove relationships request, the DefaultCreateRelationshipsRequest class ensures that the request
 * data follows the rules defined in the RelationshipsDataRulesTrait.
 */
final class RemoveToManyRelationshipsRequest extends Request
{
    use ToManyRelationshipDataRulesTrait;
}
