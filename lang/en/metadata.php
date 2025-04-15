<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Metadata Language Lines
    |--------------------------------------------------------------------------
    |
    | Used in GetFromJsonApiRouteStrategy to generate titles and descriptions
    | for Scribe documentation based on JSON:API actions.
    |
    */

    'list' => 'List :displayTypePlural',
    'show' => 'Show :displayTypeSingular',
    'create' => 'Create :displayTypeSingular',
    'update' => 'Update :displayTypeSingular',
    'delete' => 'Delete :displayTypeSingular',
    'show_related_to_one' => 'Show related :displayRelationshipSingular',
    'show_related_to_many' => 'List related :displayRelationshipPlural',
    'show_relationship_to_one' => 'Show :displayRelationshipSingular relationship',
    'update_relationship_to_one' => 'Update :displayRelationshipSingular relationship',
    'show_relationship_to_many' => 'Show :displayRelationshipPlural relationships',
    'add_relationship_to_many' => 'Add to :displayRelationshipPlural relationships',
    'update_relationship_to_many' => 'Update :displayRelationshipPlural relationships',
    'remove_relationship_to_many' => 'Remove from :displayRelationshipPlural relationships',
    'default_title' => 'Interact with :displayTypeSingular',

    'description' => [
        'list' => 'Retrieve a list of :displayTypePlural.',
        'show' => 'Retrieve a specific :displayTypeSingular by ID.',
        'create' => 'Create a new :displayTypeSingular.',
        'update' => 'Update a specific :displayTypeSingular by ID.',
        'delete' => 'Delete a specific :displayTypeSingular by ID.',
        'show_related_to_one' => 'Retrieve the related :displayRelationshipSingular for a specific :displayTypeSingular.',
        'show_related_to_many' => 'Retrieve the list of related :displayRelationshipPlural for a specific :displayTypeSingular.',
        'show_relationship_to_one' => 'Retrieve the identifier of the related :displayRelationshipSingular for a specific :displayTypeSingular.',
        'update_relationship_to_one' => 'Update the related :displayRelationshipSingular identifier for a specific :displayTypeSingular.',
        'show_relationship_to_many' => 'Retrieve the identifiers of the related :displayRelationshipPlural for a specific :displayTypeSingular.',
        'add_relationship_to_many' => 'Add identifiers to the :displayRelationshipPlural relationship of a specific :displayTypeSingular.',
        'update_relationship_to_many' => 'Replace all identifiers in the :displayRelationshipPlural relationship of a specific :displayTypeSingular.',
        'remove_relationship_to_many' => 'Remove identifiers from the :displayRelationshipPlural relationship of a specific :displayTypeSingular.',
        'default_description' => 'Perform an action related to :displayTypePlural.',
    ],
];
