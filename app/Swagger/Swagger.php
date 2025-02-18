<?php

namespace App\Swagger;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Movie App Laravel API Documentation",
 *         version="1.0",
 *         description="This is the API documentation for our movie app using Laravel.",
 *         @OA\Contact(
 *             email="zoroloffy95@gmail.com"
 *         ),
 *         @OA\License(
 *             name="MIT",
 *             url="https://opensource.org/licenses/MIT"
 *         )
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8080/api", 
 *         description="API Server"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT"
 *         )
 *     )
 * )
 */
class Swagger
{
    // This class is only for Swagger metadata.
}
