<?php

namespace App\Http\Controllers\API;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="Subscriber",
 *     title="Subscriber",
 *     description="Subscriber model",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-16T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-16T12:00:00Z")
 * )
 */


class SubscriberController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/subscribers",
 *     summary="List all subscribers of the authenticated user",
 *     tags={"Subscribers"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of subscribers",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Subscriber"))
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function index()
    {
        return Subscriber::where('user_id', Auth::id())->get();
    }


    /**
 * @OA\Post(
 *     path="/api/subscribers",
 *     summary="Create a new subscriber",
 *     tags={"Subscribers"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Subscriber created",
 *         @OA\JsonContent(ref="#/components/schemas/Subscriber")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name'=>'required'
        ]);

        $subscriber = Subscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($subscriber, 201);
    }


    /**
 * @OA\Get(
 *     path="/api/subscribers/{id}",
 *     summary="Get a single subscriber by ID",
 *     tags={"Subscribers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscriber ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscriber data",
 *         @OA\JsonContent(ref="#/components/schemas/Subscriber")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscriber not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function show($id)
    {
        $subscriber = Subscriber::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($subscriber);
    }


    /**
 * @OA\Put(
 *     path="/api/subscribers/{id}",
 *     summary="Update a subscriber",
 *     tags={"Subscribers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscriber ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="updated@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated subscriber",
 *         @OA\JsonContent(ref="#/components/schemas/Subscriber")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscriber not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

    public function update(Request $request, $id)
    {
        $subscriber = Subscriber::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email,' . $subscriber->id,
        ]);

        $subscriber->update($validated);
        return response()->json($subscriber);
    }



    /**
 * @OA\Delete(
 *     path="/api/subscribers/{id}",
 *     summary="Delete a subscriber",
 *     tags={"Subscribers"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscriber ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Subscriber deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscriber not found"
 *     )
 * )
 */

    public function destroy($id)
    {
        $subscriber = Subscriber::where('user_id', Auth::id())->findOrFail($id);
        $subscriber->delete();

        return response()->json(null, 204);
    }
}
