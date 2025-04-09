<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
 /**
     * @OA\Get(
     *     path="/v1/auth/categories",
     *     summary="Get all categories with pagination",
     *     description="Fetch a paginated list of categories. Requires authentication.",
     *     operationId="getAllCategories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of categories per page (Default: 10, Min: 1, Max: 100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             ),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="total", type="integer", example=50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token missing or invalid"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - You do not have permission"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getAllCategories(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'integer|min:1|max:100'
        ]);
    
        $limit = $validated['limit'] ?? 10; 
        $categories = Category::paginate($limit);
        return response()->json($categories);
    }
}
