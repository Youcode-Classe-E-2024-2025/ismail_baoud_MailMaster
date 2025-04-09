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


 
}
