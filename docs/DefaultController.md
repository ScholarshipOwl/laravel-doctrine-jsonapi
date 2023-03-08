# Default Controller
List of actions implemented by default or can be inherited.

## Resource
| Method | Route               | Action                                    |
|--------|---------------------|-------------------------------------------|
| GET    | /{resourceKey}/{id} | [Show resource][action show resource]     |
| PATCH  | /{resourceKey}/{id} | [Update resource][action update resource] |
| DELETE | /{resourceKey}/{id} | [Remove resource][action remove resource] |
| POST   | /{resourceKey}      | [Create resource][action create resource] |
| GET    | /{resourceKey}      | [List resources][action list resources]   |

[action show resource]: ../src/Action/Resource/ShowResourceAction.php
[action update resource]: ../src/Action/Resource/UpdateResourceAction.php
[action remove resource]: ../src/Action/Resource/RemoveResourceAction.php
[action create resource]: ../src/Action/Resource/CreateResourceAction.php
[action list resources]: ../src/Action/Resource/ListResourcesAction.php

## To-One relationships
| Method | Route                                            | Action                                       |
|--------|--------------------------------------------------|----------------------------------------------|
| GET    | /{resourceKey}/{id}/{relationship}               | [Show related][action show related one]      |
| GET    | /{resourceKey}/{id}/relationships/{relationship} | [Show relationship][action show rel one]     |
| PATCH  | /{resourceKey}/{id}/relationships/{relationship} | [Update relationship][action update rel one] |

[action show related one]: ../src/Action/Relationships/ToOne/ShowRelatedAction.php
[action show rel one]: ../src/Action/Relationships/ToOne/ShowRelationshipAction.php
[action update rel one]: ../src/Action/Relationships/ToOne/UpdateRelationshipAction.php

## To-Many relationships
| Method | Route                                            | Action                                         |
|--------|--------------------------------------------------|------------------------------------------------|
| GET    | /{resourceKey}/{id}/{relationship}               | [List related][action list related many]       |
| GET    | /{resourceKey}/{id}/relationships/{relationship} | [List relationships][action list rel many]     |
| PATCH  | /{resourceKey}/{id}/relationships/{relationship} | [Update relationships][action update rel many] |
| POST   | /{resourceKey}/{id}/relationships/{relationship} | [Create relationships][action create rel many] |
| DELETE | /{resourceKey}/{id}/relationships/{relationship} | [Remove relationships][action remove rel many] |

[action list related many]: ../src/Action/Relationships/ToMany/ListRelatedAction.php
[action list rel many]: ../src/Action/Relationships/ToMany/ListRelationshipsAction.php
[action update rel many]: ../src/Action/Relationships/ToMany/UpdateRelationshipsAction.php
[action create rel many]: ../src/Action/Relationships/ToMany/CreateRelationshipsAction.php
[action remove rel many]: ../src/Action/Relationships/ToMany/RemoveRelationshipsAction.php

