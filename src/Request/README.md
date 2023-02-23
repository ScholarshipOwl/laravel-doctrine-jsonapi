# Request

[reqeustResource]: 
We have next requests types:


### Resource actions
| Action                                    | Abstract request                                 | Method | Path           | Auth ability |
|-------------------------------------------|--------------------------------------------------|--------|----------------|--------------|
| [Show resource][action show resource]     | [AbstractShowRequest][request show resource]     | GET    | /resource/{id} | show         |
| [Update resource][action update resource] | [AbstractUpdateRequest][request update resource] | PATCH  | /resource/{id} | update       |
| [Remove resource][action remove resource] | [AbstractRemoveRequest][request remove resource] | DELETE | /resource/{id} | remove       |
| [Create resource][action create resource] | [AbstractCreateRequest][request create resource] | POST   | /resource      | create       |
| [List resources][action list resources]   | [AbstractListRequest][request list resources]    | GET    | /resource      | list         |

[action show resource]: ../Action/Resource/ShowResource.php
[action update resource]: ../Action/Resource/UpdateResource.php
[action remove resource]: ../Action/Resource/RemoveResource.php
[action create resource]: ../Action/Resource/CreateResource.php
[action list resources]: ../Action/Resource/ListResources.php

[request show resource]: ./Resource/AbstractShowRequest.php
[request update resource]: ./Resource/AbstractUpdateRequest.php
[request remove resource]: ./Resource/AbstractRemoveRequest.php
[request create resource]: ./Resource/AbstractCreateRequest.php
[request list resources]: ./Resource/AbstractListRequest.php

## Resource ToOne relationships
| Action                                       | Abstract request                                            | Method | Path                                      | Authentication rules |
|----------------------------------------------|-------------------------------------------------------------|--------|-------------------------------------------|----------------------|
| [Show related][action show related one]      | [AbstractShowRelatedRequest][request show related one]      | GET    | /resource/{id}/{relatedOne}               | showRelationships    |
| [Show relationship][action show rel one]]    | [AbstractShowRelationshipRequest][request show rel one]     | GET    | /resource/{id}/relationships/{relatedOne} | showRelationships    |
| [Update relationship][action update rel one] | [AbstractUpdateRelationshipRequest][request update rel one] | PATCH  | /resource/{id}/relationships/{relatedOne} | updateRelationships  |

[action show related one]: ../Action/Relationships/ToOne/ShowRelated.php
[action show rel one]: ../Action/Relationships/ToOne/ShowRelationship.php
[action update rel one]: ../Action/Relationships/ToOne/UpdateRelationship.php

[request show related one]: ./Relationships/ToOne/AbstractShowRelatedRequest.php
[request show rel one]: ./Relationships/ToOne/AbstractShowRelationshipRequest.php
[request update rel one]: ./Relationships/ToOne/AbstractUpdateRelationshipRequest.php

## Resource ToMany relationships
| Name                                           | Abstract resource                                         | Method | Path                                       | Authentication rules |
|------------------------------------------------|-----------------------------------------------------------|--------|--------------------------------------------|----------------------|
| [list related][action list related many]       | [AbstractListRelatedRequest][req list related many]       | GET    | /resource/{id}/{relatedMany}               | showRelationships    |
| [list relationships][action list rel many]     | [AbstractListRelationshipsRequest][req list rel many]     | GET    | /resource/{id}/relationships/{relatedMany} | showRelationships    |
| [Update relationships][action update rel many] | [AbstractUpdateRelationshipsRequest][req update rel many] | PATCH  | /resource/{id}/relationships/{relatedMany} | updateRelationships  |
| [Create relationships][action create rel many] | [AbstractCreateRelationshipsRequest][req create rel many] | POST   | /resource/{id}/relationships/{relatedMany} | createRelationships  |
| [Remove relationships][action remove rel many] | [AbstractRemoveRelationshipsRequest][req remove rel many] | DELETE | /resource/{id}/relationships/{relatedMany} | removeRelationships  |

[action list related many]: ../Action/Relationships/ToMany/ListRelated.php
[action list rel many]: ../Action/Relationships/ToMany/ListRelationships.php
[action update rel many]: ../Action/Relationships/ToMany/UpdateRelationships.php
[action create rel many]: ../Action/Relationships/ToMany/CreateRelationships.php
[action remove rel many]: ../Action/Relationships/ToMany/RemoveRelationships.php

[req list related many]: ./Relationships/ToMany/AbstractListRelatedRequest.php
[req list rel many]: ./Relationships/ToMany/AbstractListRelationshipsRequest.php
[req update rel many]: ./Relationships/ToMany/AbstractUpdateRelationshipsRequest.php
[req create rel many]: ./Relationships/ToMany/AbstractCreateRelationshipsRequest.php
[req remove rel many]: ./Relationships/ToMany/AbstractRemoveRelationshipsRequest.php
