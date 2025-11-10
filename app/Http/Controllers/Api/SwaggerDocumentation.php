<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="Calendros API Documentation",
 *     version="1.0.0",
 *     description="API documentation for Calendros - A comprehensive calendar management system",
 *     @OA\Contact(
 *         email="support@calendros.example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://prm.positive.io.vn/api",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Enter your Sanctum token"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Calendars",
 *     description="API endpoints for calendar management"
 * )
 * 
 * @OA\Tag(
 *     name="Events",
 *     description="API endpoints for event management"
 * )
 * 
 * @OA\Tag(
 *     name="Invites",
 *     description="API endpoints for event invitations"
 * )
 */
class SwaggerDocumentation
{
    // This class is only for Swagger documentation
}
