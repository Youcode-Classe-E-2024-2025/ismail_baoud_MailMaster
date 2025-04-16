<?php
namespace App\Http\Controllers\API;

use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Campaigns",
 *     description="API endpoints for managing campaigns"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Campaign",
 *     type="object",
 *     required={"id", "subject", "content", "newsletter_id", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="subject", type="string", example="New Product Launch"),
 *     @OA\Property(property="content", type="string", example="Check out our new features..."),
 *     @OA\Property(property="newsletter_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="draft"),
 *     @OA\Property(property="sent_at", type="string", format="date-time", example="2025-04-16T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-16T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-16T10:30:00Z")
 * )
 */

class CampaignController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/campaigns",
     *     summary="List all campaigns of the authenticated user",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of campaigns",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Campaign"))
     *     )
     * )
     */
    public function index()
    {
        return Campaign::where('user_id', Auth::id())->with('subscribers')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/campaigns",
     *     summary="Create a new campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject", "content", "newsletter_id"},
     *             @OA\Property(property="subject", type="string", example="Welcome Series"),
     *             @OA\Property(property="content", type="string", example="Welcome to our community!"),
     *             @OA\Property(property="newsletter_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="draft"),
     *             @OA\Property(property="sent_at", type="string", format="date-time", example="2025-04-16T12:00:00Z"),
     *             @OA\Property(property="subscriber_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Campaign created",
     *         @OA\JsonContent(ref="#/components/schemas/Campaign")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'newsletter_id' => 'required|exists:newsletters,id',
            'status' => 'in:draft,sent,pending',
            'sent_at' => 'nullable|date',
            'subscriber_ids' => 'array',
            'subscriber_ids.*' => 'exists:subscribers,id',
        ]);

        $campaign = Campaign::create([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'newsletter_id' => $validated['newsletter_id'],
            'status' => $validated['status'] ?? 'draft',
            'sent_at' => $validated['sent_at'] ?? null,
            'user_id' => Auth::id(),
        ]);

        if (isset($validated['subscriber_ids'])) {
            $campaign->subscribers()->sync($validated['subscriber_ids']);
        }

        return response()->json($campaign->load('subscribers'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/campaigns/{id}",
     *     summary="Get a specific campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaign details",
     *         @OA\JsonContent(ref="#/components/schemas/Campaign")
     *     )
     * )
     */
    public function show($id)
    {
        $campaign = Campaign::where('user_id', Auth::id())->with('subscribers')->findOrFail($id);
        return response()->json($campaign);
    }

    /**
     * @OA\Put(
     *     path="/api/campaigns/{id}",
     *     summary="Update an existing campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject", "content", "newsletter_id"},
     *             @OA\Property(property="subject", type="string", example="Updated Welcome Series"),
     *             @OA\Property(property="content", type="string", example="Welcome again!"),
     *             @OA\Property(property="newsletter_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="sent"),
     *             @OA\Property(property="sent_at", type="string", format="date-time", example="2025-04-16T14:00:00Z"),
     *             @OA\Property(property="subscriber_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaign updated",
     *         @OA\JsonContent(ref="#/components/schemas/Campaign")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'newsletter_id' => 'required|exists:newsletters,id',
            'status' => 'in:draft,sent,pending',
            'sent_at' => 'nullable|date',
            'subscriber_ids' => 'array',
            'subscriber_ids.*' => 'exists:subscribers,id',
        ]);

        $campaign->update([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'newsletter_id' => $validated['newsletter_id'],
            'status' => $validated['status'],
            'sent_at' => $validated['sent_at'],
        ]);

        if (isset($validated['subscriber_ids'])) {
            $campaign->subscribers()->sync($validated['subscriber_ids']);
        }

        return response()->json($campaign->load('subscribers'));
    }

    /**
     * @OA\Delete(
     *     path="/api/campaigns/{id}",
     *     summary="Delete a campaign",
     *     tags={"Campaigns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Campaign deleted"
     *     )
     * )
     */
    public function destroy($id)
    {
        $campaign = Campaign::where('user_id', Auth::id())->findOrFail($id);
        $campaign->delete();

        return response()->json(null, 204);
    }
}
