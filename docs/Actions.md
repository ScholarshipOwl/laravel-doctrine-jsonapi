# Actions
List of actions implemented by default or can be inherited.

## Resource
| Action                                    | Method | Route               | Auth ability |
|-------------------------------------------|--------|---------------------|--------------|
| [Show resource][action show resource]     | GET    | /{resourceKey}/{id} | show         |
| [Update resource][action update resource] | PATCH  | /{resourceKey}/{id} | update       |
| [Remove resource][action remove resource] | DELETE | /{resourceKey}/{id} | remove       |
| [Create resource][action create resource] | POST   | /{resourceKey}      | create       |
| [List resources][action list resources]   | GET    | /{resourceKey}      | list         |

[action show resource]: ../src/Action/Resource/ShowResourceAction.php
[action update resource]: ../src/Action/Resource/UpdateResourceAction.php
[action remove resource]: ../src/Action/Resource/RemoveResourceAction.php
[action create resource]: ../src/Action/Resource/CreateResourceAction.php
[action list resources]: ../src/Action/Resource/ListResourcesAction.php

## To-One relationships
| Action                                       | Method | Route                                            | Authentication rules |
|----------------------------------------------|--------|--------------------------------------------------|----------------------|
| [Show related][action show related one]      | GET    | /{resourceKey}/{id}/{relationship}               | show{Relationship}   |
| [Show relationship][action show rel one]     | GET    | /{resourceKey}/{id}/relationships/{relationship} | show{Relationship}   |
| [Update relationship][action update rel one] | PATCH  | /{resourceKey}/{id}/relationships/{relationship} | update{Relationship} |

[action show related one]: ../src/Action/Relationships/ToOne/ShowRelatedAction.php
[action show rel one]: ../src/Action/Relationships/ToOne/ShowRelationshipAction.php
[action update rel one]: ../src/Action/Relationships/ToOne/UpdateRelationshipAction.php

## To-Many relationships
| Action                                         | Method | Route                                            | Authentication rules |
|------------------------------------------------|--------|--------------------------------------------------|----------------------|
| [list related][action list related many]       | GET    | /{resourceKey}/{id}/{relationship}               | show{Relationship}   |
| [list relationships][action list rel many]     | GET    | /{resourceKey}/{id}/relationships/{relationship} | show{Relationship}   |
| [Update relationships][action update rel many] | PATCH  | /{resourceKey}/{id}/relationships/{relationship} | update{Relationship} |
| [Create relationships][action create rel many] | POST   | /{resourceKey}/{id}/relationships/{relationship} | create{Relationship} |
| [Remove relationships][action remove rel many] | DELETE | /{resourceKey}/{id}/relationships/{relationship} | remove{Relationship} |

[action list related many]: ../src/Action/Relationships/ToMany/ListRelatedAction.php
[action list rel many]: ../src/Action/Relationships/ToMany/ListRelationshipsAction.php
[action update rel many]: ../src/Action/Relationships/ToMany/UpdateRelationshipsAction.php
[action create rel many]: ../src/Action/Relationships/ToMany/CreateRelationshipsAction.php
[action remove rel many]: ../src/Action/Relationships/ToMany/RemoveRelationshipsAction.php

