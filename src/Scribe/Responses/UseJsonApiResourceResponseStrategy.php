<?php

namespace Sowl\JsonApi\Scribe\Responses;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Sowl\JsonApi\Scribe\AbstractStrategy;

/**
 * Strategy to generate JSON:API compliant response examples
 */
class UseJsonApiResourceResponseStrategy extends AbstractStrategy
{
    /**
     * @inheritDoc
     */
    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): array
    {
        $route = $endpointData->route;
        if (!$route || !$this->isJsonApiRoute($route)) {
            // Not a JSON:API route, skip
            return [];
        }

        $methodName = $this->getMethodNameFromRoute($route);
        if (!$methodName) {
            return [];
        }

        $actionType = $this->determineActionType($methodName);
        $resourceType = $this->extractResourceTypeFromRoute($route);

        if (!$resourceType) {
            return [];
        }

        // Use 'users' as a placeholder for {resourceType}
        $exampleType = $resourceType === '{resourceType}' ? 'users' : $resourceType;

        // Generate proper status code based on action type
        $statusCode = in_array($actionType, ['create']) ? 201 : 200;

        // For DELETE operations, use 204 No Content
        if (in_array($actionType, ['delete', 'remove-relationships'])) {
            $statusCode = 204;
        }

        $responses = [];

        // Add successful response
        $responses[] = [
            'status' => $statusCode,
            'description' => 'Successful response',
            'content' => $this->generateResponseContent($exampleType, $actionType),
        ];

        // Add common error responses
        $responses[] = [
            'status' => 404,
            'description' => 'Resource not found',
            'content' => $this->generateErrorResponse(404, 'Not Found', 'The requested resource could not be found.')
        ];

        // Add validation error for operations with request body
        if (in_array($actionType, ['create', 'update']) || strpos($actionType, 'relationship') !== false) {
            $responses[] = [
                'status' => 422,
                'description' => 'Validation error',
                'content' => $this->generateValidationErrorResponse()
            ];
        }

        return $responses;
    }

    /**
     * Generate a JSON:API error response
     *
     * @param int $status
     * @param string $title
     * @param string $detail
     * @param array|null $source
     * @return array
     */
    protected function generateErrorResponse(int $status, string $title, string $detail, ?array $source = null): array
    {
        $errorObject = [
            'errors' => [
                [
                    'status' => (string)$status,
                    'title' => $title,
                    'detail' => $detail,
                ]
            ]
        ];

        if ($source) {
            $errorObject['errors'][0]['source'] = $source;
        }

        // Convert the error object to JSON string and back to ensure proper formatting
        $errorJson = json_encode($errorObject);

        return [
            'application/vnd.api+json' => $errorJson
        ];
    }

    /**
     * Generate a validation error response
     *
     * @return array
     */
    protected function generateValidationErrorResponse(): array
    {
        return $this->generateErrorResponse(
            422,
            'Validation Error',
            'The given data was invalid.',
            ['pointer' => '/data/attributes/email']
        );
    }

    /**
     * Generate response content based on action type and resource type
     *
     * @param string $resourceType
     * @param string $actionType
     * @return array
     */
    protected function generateResponseContent(string $resourceType, string $actionType): array
    {
        // For delete operations, return empty response
        if (in_array($actionType, ['delete', 'remove-relationships'])) {
            return [
                'application/vnd.api+json' => 'null'
            ];
        }

        $responseContent = null;

        if ($actionType === 'list') {
            $responseContent = [
                'data' => [
                    [
                        'type' => $resourceType,
                        'id' => '550e8400-e29b-41d4-a716-446655440000',
                        'attributes' => [
                            'name' => 'Example Name',
                            'email' => 'example@example.com',
                            'created_at' => '2023-01-01T12:00:00Z',
                        ],
                        'relationships' => [
                            'roles' => [
                                'links' => [
                                    'self' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/relationships/roles"),
                                    'related' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/roles"),
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000"),
                        ],
                    ],
                ],
                'links' => [
                    'self' => url("/{$resourceType}"),
                    'first' => url("/{$resourceType}?page[number]=1"),
                    'last' => url("/{$resourceType}?page[number]=1"),
                ],
                'meta' => [
                    'total' => 1,
                ],
            ];
        } elseif (in_array($actionType, ['show', 'create', 'update'])) {
            $responseContent = [
                'data' => [
                    'type' => $resourceType,
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                    'attributes' => [
                        'name' => 'Example Name',
                        'email' => 'example@example.com',
                        'created_at' => '2023-01-01T12:00:00Z',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'self' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/relationships/roles"),
                                'related' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/roles"),
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000"),
                    ],
                ],
            ];
        } elseif (strpos($actionType, 'relationships') !== false) {
            $responseContent = [
                'data' => $actionType === 'show-relationships' ? [
                    [
                        'type' => 'roles',
                        'id' => '550e8400-e29b-41d4-a716-446655440000',
                    ],
                ] : [
                    'type' => 'roles',
                    'id' => '550e8400-e29b-41d4-a716-446655440000',
                ],
                'links' => [
                    'self' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/relationships/roles"),
                    'related' => url("/{$resourceType}/550e8400-e29b-41d4-a716-446655440000/roles"),
                ],
            ];
        } else {
            $responseContent = [
                'data' => null,
            ];
        }

        // Convert to JSON string to ensure proper formatting for Scribe
        return [
            'application/vnd.api+json' => json_encode($responseContent)
        ];
    }
}
