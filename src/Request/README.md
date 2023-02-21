# Request

We have next requests types:


### Resource actions
| Name            | Method | Path           | Authentication |
|-----------------|--------|----------------|----------------|
| Show resource   | GET    | /resource/{id} | show           |
| Update resource | PATCH  | /resource/{id} | update         |
| Remove resource | DELETE | /resource/{id} | remove         |
| Create resource | POST   | /resource      | create         |
| List resource   | GET    | /resource      | list           |

### Resource ToOne relationships
| Name                | Method | Path                                    | Authentication rules |
|---------------------|--------|-----------------------------------------|----------------------|
| Show related        | GET    | /resource/{id}/relatedOne               | showRelationships    |
| Show relationship   | GET    | /resource/{id}/relationships/relatedOne | showRelationships    |
| Update relationship | PATCH  | /resource/{id}/relationships/relatedOne | updateRelationships  |

### Resource ToMany relationships
| Name                 | Method | Path                                        | Authentication rules |
|----------------------|--------|---------------------------------------------|----------------------|
| Show related         | GET    | /resource/{id}/relatedMany                  | showRelationships    |
| Show relationships   | GET    | /resource/{id}/relationships/relatedMany    | showRelationships    |
| Update relationships | PATCH  | /resource/{id}/relationships/relatedMany    | updateRelationships  |
| Create relationships | POST   | /resource/{id}/relationships/relatedMany    | createRelationships  |
| Remove relationships | DELETE | /resource/{id}/relationships/relatedMany    | removeRelationships  |
