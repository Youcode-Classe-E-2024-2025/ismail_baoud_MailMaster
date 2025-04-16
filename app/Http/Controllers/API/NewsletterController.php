<?php

namespace App\Http\Controllers\API;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Newsletter",
 *     title="Newsletter",
 *     description="Newsletter model",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Tech Updates"),
 *     @OA\Property(property="content", type="string", example="Latest updates in the tech world."),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-07T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-07T12:30:00Z")
 * )
 */



class NewsletterController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/newsletters",
 *     summary="Get all newsletters for authenticated user",
 *     tags={"Newsletters"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of newsletters",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Newsletter"))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */

    public function index()
    {
        return Newsletter::where('user_id', Auth::id())->get();
    }


/**
 * @OA\Post(
 *     path="/api/newsletters",
 *     summary="Create a new newsletter",
 *     tags={"Newsletters"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "content"},
 *             @OA\Property(property="title", type="string", example="Tech Updates"),
 *             @OA\Property(property="content", type="string", example="Latest news in tech.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Newsletter created",
 *         @OA\JsonContent(ref="#/components/schemas/Newsletter")
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content'=> 'required'
        ]);

        $newsletter = Newsletter::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($newsletter, 201);
    }


/**
 * @OA\Get(
 *     path="/api/newsletters/{id}",
 *     summary="Get a single newsletter by ID",
 *     tags={"Newsletters"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Newsletter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Newsletter details",
 *         @OA\JsonContent(ref="#/components/schemas/Newsletter")
 *     ),
 *     @OA\Response(response=404, description="Newsletter not found")
 * )
 */

    public function show($id)
    {
        $newsletter = Newsletter::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($newsletter);
    }


/**
 * @OA\Put(
 *     path="/api/newsletters/{id}",
 *     summary="Update a newsletter",
 *     tags={"Newsletters"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Newsletter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "content"},
 *             @OA\Property(property="title", type="string", example="Updated Title"),
 *             @OA\Property(property="content", type="string", example="Updated content.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Newsletter updated",
 *         @OA\JsonContent(ref="#/components/schemas/Newsletter")
 *     ),
 *     @OA\Response(response=404, description="Newsletter not found"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    public function update(Request $request, $id)
    {
        $newsletter = Newsletter::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $newsletter->update($validated);
        return response()->json($newsletter);
    }


/**
 * @OA\Delete(
 *     path="/api/newsletters/{id}",
 *     summary="Delete a newsletter",
 *     tags={"Newsletters"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Newsletter ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=204, description="Newsletter deleted"),
 *     @OA\Response(response=404, description="Newsletter not found")
 * )
 */

    public function destroy($id)
    {
        $newsletter = Newsletter::where('user_id', Auth::id())->findOrFail($id);
        $newsletter->delete();

        return response()->json(null, 204);
    }
}
