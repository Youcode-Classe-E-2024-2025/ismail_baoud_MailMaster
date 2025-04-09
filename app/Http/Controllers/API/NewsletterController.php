<?php

namespace App\Http\Controllers\API;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
    /**
 * Get all newsletters for the authenticated user.
 * 
 * This endpoint returns a list of all newsletters that were created by the authenticated user.
 *
 * @authenticated
 * @response 200 [
 *   {
 *     "id": 1,
 *     "title": "Tech Updates",
 *     "content": "Latest updates in the tech world.",
 *     "user_id": 1,
 *     "created_at": "2025-04-07T12:00:00",
 *     "updated_at": "2025-04-07T12:00:00"
 *   }
 * ]
 * @response 401 {
 *   "message": "Unauthorized"
 * }
 */
    public function index()
    {
        return Newsletter::where('user_id', Auth::id())->get();
    }


    /**
 * Create a new newsletter.
 * 
 * This endpoint allows the authenticated user to create a new newsletter.
 * 
 * @authenticated
 * @bodyParam title string required The title of the newsletter. Example: "Tech Updates"
 * @bodyParam content string required The content of the newsletter. Example: "Latest news in tech."
 * @response 201 {
 *   "id": 2,
 *   "title": "Tech Updates",
 *   "content": "Latest news in tech.",
 *   "user_id": 1,
 *   "created_at": "2025-04-07T12:00:00",
 *   "updated_at": "2025-04-07T12:00:00"
 * }
 * @response 422 {
 *   "message": "The given data was invalid."
 * }
 */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $newsletter = Newsletter::create([
            'title' => $validated['title'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($newsletter, 201);
    }


    /**
 * Display the specified newsletter.
 * 
 * This endpoint returns the details of a specific newsletter.
 *
 * @authenticated
 * @urlParam id integer required The ID of the newsletter. Example: 1
 * @response 200 {
 *   "id": 1,
 *   "title": "Tech Updates",
 *   "content": "Latest updates in the tech world.",
 *   "user_id": 1,
 *   "created_at": "2025-04-07T12:00:00",
 *   "updated_at": "2025-04-07T12:00:00"
 * }
 * @response 404 {
 *   "message": "Newsletter not found"
 * }
 */
    public function show($id)
    {
        $newsletter = Newsletter::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($newsletter);
    }


    /**
 * Update the specified newsletter.
 * 
 * This endpoint allows the authenticated user to update a specific newsletter.
 *
 * @authenticated
 * @urlParam id integer required The ID of the newsletter to be updated. Example: 1
 * @bodyParam title string required The updated title of the newsletter. Example: "Updated Tech Updates"
 * @bodyParam content string required The updated content of the newsletter. Example: "New updates in tech."
 * @response 200 {
 *   "id": 1,
 *   "title": "Updated Tech Updates",
 *   "content": "New updates in tech.",
 *   "user_id": 1,
 *   "created_at": "2025-04-07T12:00:00",
 *   "updated_at": "2025-04-07T12:30:00"
 * }
 * @response 404 {
 *   "message": "Newsletter not found"
 * }
 * @response 422 {
 *   "message": "The given data was invalid."
 * }
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
 * Remove the specified newsletter.
 * 
 * This endpoint allows the authenticated user to delete a specific newsletter.
 *
 * @authenticated
 * @urlParam id integer required The ID of the newsletter to be deleted. Example: 1
 * @response 200 {
 *   "message": "Newsletter deleted successfully"
 * }
 * @response 404 {
 *   "message": "Newsletter not found"
 * }
 */
    public function destroy($id)
    {
        $newsletter = Newsletter::where('user_id', Auth::id())->findOrFail($id);
        $newsletter->delete();

        return response()->json(null, 204);
    }
}
