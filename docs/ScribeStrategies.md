# Scribe JSON:API Strategies Reference

This document provides advanced documentation for the custom Scribe strategies used in the Laravel Doctrine JSON:API package. Each strategy automates the extraction and documentation of key aspects of your API, ensuring full JSON:API compliance and rich, accurate API docs.

### Table of Content

- [Metadata](#metadata)
- [Headers](#headers)
- [Query Parameters](#query-parameters)
- [Responses](#responses)
- [URL Parameters](#url-parameters)


## Metadata

**Strategy:**  
[GetFromResourceMetadataAttribute](../src/Scribe/Strategies/Metadata/GetFromResourceMetadataAttribute.php)

Extracts and documents metadata for each endpoint:
- Generates human-readable titles and descriptions for endpoints
- Groups endpoints by resource type and action (list, show, create, update, delete, relationships, etc.)
- Uses [#[ResourceMetadata]](../src/Scribe/Attributes/ResourceMetadata.php) attribute for customization
- Ensures endpoints are clearly described and discoverable in the generated documentation

## Headers

**Strategy:**  
[GetFromResourceAttributes](../src/Scribe/Strategies/Headers/GetFromResourceAttributes.php)

Adds required JSON:API headers to endpoints using PHP attributes:
- `Accept: application/vnd.api+json`: Required for all requests
- `Content-Type: application/vnd.api+json`: Required for POST, PATCH, and DELETE requests
- Extracts header info from [#[ResourceRequest]](../src/Scribe/Attributes/ResourceRequest.php), [#[ResourceResponse]](../src/Scribe/Attributes/ResourceResponse.php), and related attributes

## Query Parameters

**Strategy:**  
[GetFromResourceRequestAttributes](../src/Scribe/Strategies/QueryParameters/GetFromResourceRequestAttributes.php)

Documents standard JSON:API query parameters for GET endpoints:
- `include`: Include related resources (comma-separated)
- `fields[type]`: Sparse fieldsets to select specific fields
- `sort`: Sort resources by fields (prefix with `-` for descending)
- `page[number]`, `page[size]`: Pagination controls
- `filter[field]`: Filter resources by field values (if supported by resource)
- Uses real filter, sort, and pagination definitions from resource classes
- Extracts parameters from [#[ResourceRequest]](../src/Scribe/Attributes/ResourceRequest.php) and [#[ResourceRequestList]](../src/Scribe/Attributes/ResourceRequestList.php) attributes
- Supports custom parameters defined in resource attributes

## Responses

**Strategy:**  
[GetFromResourceResponseAttributes](../src/Scribe/Strategies/Responses/GetFromResourceResponseAttributes.php)

Generates JSON:API compliant response examples:
- Shows proper response structure with `data`, `included`, and `meta` sections
- Includes relationship links and resource linkage
- Documents error responses in JSON:API format
- Provides examples for different HTTP status codes
- Uses real Doctrine entities and transformers for examples

## URL Parameters

**Strategy:**  
[GetFromResourceRequestAttributes](../src/Scribe/Strategies/UrlParameters/GetFromResourceRequestAttributes.php)

Documents URL parameters for JSON:API routes:
- `{id}`: Resource identifier (type and example derived from Doctrine metadata)
- `{resourceType}` and `{relationship}`: Expanded to all registered resource types and relationships
- Provides accurate descriptions and validation for each parameter


