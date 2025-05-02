# Scribe JSON:API Strategies Reference

> **What is a Scribe Strategy?**
>
> A **strategy** in Scribe is a powerful plugin mechanism that allows you to customize or extend how Scribe generates API documentation for your Laravel application. Strategies control how specific parts of your API—such as query parameters, request bodies, responses, headers, authentication, and more—are detected and documented.
>
> Scribe executes a series of strategies for each documentation section. Each strategy can extract information from your codebase (attributes, annotations, validation rules, etc.) and contribute to the generated docs. You can use Scribe's built-in strategies, override them, or write your own to handle special cases or custom logic.
>
> Strategies are the recommended way to adapt Scribe to your project's unique requirements, and are essential for advanced documentation scenarios. For a full explanation and implementation guide, see the official documentation: [Creating a Strategy (Scribe Plugins)](https://scribe.knuckles.wtf/laravel/advanced/plugins#creating-a-strategy).

This document provides advanced documentation for the custom Scribe strategies used in the Laravel Doctrine JSON:API package. Each strategy automates the extraction and documentation of key aspects of your API, ensuring full JSON:API compliance and rich, accurate API docs.



### Table of Content

- [Metadata](#metadata)
- [Headers](#headers)
- [Query Parameters](#query-parameters)
- [Responses](#responses)
- [URL Parameters](#url-parameters)
- [Request Body Parameters](#request-body-parameters)


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

## Request Body Parameters

**Strategy:**  
Scribe Default (FormRequest & Validation Rules)

For request body parameters, this package uses Scribe's default body parameter extraction strategy. You do not need to implement or extend a custom strategy for documenting request payloads. Scribe will automatically extract request body fields and examples from your Laravel FormRequest classes or inline validation rules, as described in the [official Scribe documentation](https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters).

- Supports all standard Laravel validation approaches (FormRequest, inline `$request->validate()`, etc.)
- Automatically documents all request fields, types, and examples
- No extra configuration required for JSON:API endpoints

This approach ensures your documentation is always in sync with your validation logic, and works seamlessly with this library.

### Validation Rules for Body Parameters

Body parameters are documented based on the validation rules defined in your application:

1. For POST/PATCH endpoints, validation rules determine required and optional fields
2. Field types (string, integer, boolean, etc.) are inferred from validation rules
3. Format constraints (email, date, URL, etc.) are respected in the examples
4. Relationship validation rules are used to structure relationship data correctly

For detailed information about validation rules, see our [Validation Documentation](./Validation.md) and [Scribe's documentation on validation rules](https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters#validation-rules).