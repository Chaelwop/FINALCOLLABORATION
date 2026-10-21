<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioApiController extends Controller
{
    /**
     * List the portfolios owned by the signed-in user.
     */
    public function index(Request $request): JsonResponse
    {
        $portfolios = Portfolio::query()
            ->ownedBy($request->user()->getKey())
            ->latest()
            ->get()
            ->map(fn (Portfolio $portfolio): array => $portfolio->payload());

        return response()->json($portfolios);
    }

    /**
     * Store a new portfolio for the signed-in user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => $request->user()->getKey(),
            'full_name' => $data['fullName'],
            'email' => $data['email'],
            'template' => $data['template'],
            'is_public' => $data['isPublic'],
            'data' => $data,
        ]);

        return response()->json($portfolio->payload(), 201);
    }

    /**
     * Update an existing portfolio owned by the signed-in user.
     */
    public function update(Request $request, Portfolio $portfolio): JsonResponse
    {
        $this->ensureOwnership($request, $portfolio);

        $data = $this->validatedData($request);

        $portfolio->update([
            'full_name' => $data['fullName'],
            'email' => $data['email'],
            'template' => $data['template'],
            'is_public' => $data['isPublic'],
            'data' => $data,
        ]);

        return response()->json($portfolio->refresh()->payload());
    }

    /**
     * Delete a portfolio owned by the signed-in user.
     */
    public function destroy(Request $request, Portfolio $portfolio): JsonResponse
    {
        $this->ensureOwnership($request, $portfolio);

        $portfolio->delete();

        return response()->json(['message' => 'Portfolio deleted.']);
    }

    /**
     * Validate the incoming portfolio payload.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'jobTitle' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'profilePic' => ['nullable', 'url', 'max:2048'],
            'aboutMe' => ['required', 'string', 'max:5000'],
            'eduSchool' => ['nullable', 'string', 'max:255'],
            'eduDegree' => ['nullable', 'string', 'max:255'],
            'eduYear' => ['nullable', 'string', 'max:100'],
            'skills' => ['present', 'array', 'max:100'],
            'skills.*' => ['string', 'max:100'],
            'projects' => ['present', 'array', 'max:100'],
            'projects.*.title' => ['required', 'string', 'max:255'],
            'projects.*.desc' => ['nullable', 'string', 'max:2000'],
            'projects.*.link' => ['nullable', 'url', 'max:2048'],
            'experience' => ['present', 'array', 'max:100'],
            'experience.*.company' => ['nullable', 'string', 'max:255'],
            'experience.*.role' => ['nullable', 'string', 'max:255'],
            'experience.*.duration' => ['nullable', 'string', 'max:100'],
            'experience.*.desc' => ['nullable', 'string', 'max:2000'],
            'socialGithub' => ['nullable', 'url', 'max:2048'],
            'socialLinkedin' => ['nullable', 'url', 'max:2048'],
            'socialWebsite' => ['nullable', 'url', 'max:2048'],
            'template' => ['required', 'string', Rule::in(Portfolio::TEMPLATES)],
            'isPublic' => ['required', 'boolean'],
        ]);

        return [
            ...$validated,
            'phone' => $validated['phone'] ?? '',
            'address' => $validated['address'] ?? '',
            'profilePic' => $validated['profilePic'] ?? '',
            'eduSchool' => $validated['eduSchool'] ?? '',
            'eduDegree' => $validated['eduDegree'] ?? '',
            'eduYear' => $validated['eduYear'] ?? '',
            'socialGithub' => $validated['socialGithub'] ?? '',
            'socialLinkedin' => $validated['socialLinkedin'] ?? '',
            'socialWebsite' => $validated['socialWebsite'] ?? '',
        ];
    }

    /**
     * Guarantee the portfolio belongs to the signed-in user.
     */
    private function ensureOwnership(Request $request, Portfolio $portfolio): void
    {
        abort_unless($portfolio->user_id === $request->user()->getKey(), 404);
    }
}
