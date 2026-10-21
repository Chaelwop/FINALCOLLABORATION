<?php

namespace App\Http\Controllers;

use App\Enums\SocialProvider;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * The marketing landing page.
     */
    public function home(): View
    {
        return view('home', [
            'providers' => SocialProvider::cases(),
        ]);
    }

    /**
     * The signed-in user's portfolio dashboard.
     */
    public function dashboard(Request $request): View
    {
        $portfolios = Portfolio::query()
            ->ownedBy($request->user()->getKey())
            ->latest()
            ->get();

        return view('dashboard', [
            'portfolios' => $portfolios,
            'totalCount' => $portfolios->count(),
            'publicCount' => $portfolios->where('is_public', true)->count(),
            'draftCount' => $portfolios->where('is_public', false)->count(),
            'templateCount' => $portfolios->pluck('template')->unique()->count(),
        ]);
    }

    /**
     * Show the empty editor for a brand new portfolio.
     */
    public function create(): View
    {
        return view('editor', [
            'portfolio' => null,
            'payload' => $this->emptyPayload(),
            'mode' => 'create',
        ]);
    }

    /**
     * Show the editor pre-filled with an existing portfolio.
     */
    public function edit(Request $request, Portfolio $portfolio): View
    {
        $this->ensureOwnership($request, $portfolio);

        return view('editor', [
            'portfolio' => $portfolio,
            'payload' => $portfolio->payload(),
            'mode' => 'edit',
        ]);
    }

    /**
     * Show the full screen preview of a saved portfolio.
     */
    public function preview(Request $request, Portfolio $portfolio): View
    {
        $this->ensureOwnership($request, $portfolio);

        return view('preview', [
            'portfolio' => $portfolio,
            'payload' => $portfolio->payload(),
        ]);
    }

    /**
     * Show the publicly shared version of a portfolio.
     */
    public function show(Portfolio $portfolio): View
    {
        abort_unless($portfolio->is_public, 404);

        return view('share', [
            'portfolio' => $portfolio,
            'payload' => $portfolio->payload(),
        ]);
    }

    /**
     * Render a template preview for the live editor canvas.
     */
    public function fragment(Request $request): View
    {
        $template = (string) $request->input('template', 'simple');

        abort_unless(in_array($template, Portfolio::TEMPLATES, true), 404);

        return view('components.portfolio.fragment', [
            'template' => $template,
            'portfolio' => $this->payloadFromRequest($request),
        ]);
    }

    /**
     * Build a payload from the raw request without enforcing validation rules.
     *
     * @return array<string, mixed>
     */
    private function payloadFromRequest(Request $request): array
    {
        $payload = $request->input('portfolio', []);

        return [
            'fullName' => (string) ($payload['fullName'] ?? ''),
            'jobTitle' => (string) ($payload['jobTitle'] ?? ''),
            'email' => (string) ($payload['email'] ?? ''),
            'phone' => (string) ($payload['phone'] ?? ''),
            'address' => (string) ($payload['address'] ?? ''),
            'profilePic' => (string) ($payload['profilePic'] ?? ''),
            'aboutMe' => (string) ($payload['aboutMe'] ?? ''),
            'eduSchool' => (string) ($payload['eduSchool'] ?? ''),
            'eduDegree' => (string) ($payload['eduDegree'] ?? ''),
            'eduYear' => (string) ($payload['eduYear'] ?? ''),
            'skills' => array_values((array) ($payload['skills'] ?? [])),
            'projects' => array_values((array) ($payload['projects'] ?? [])),
            'experience' => array_values((array) ($payload['experience'] ?? [])),
            'socialGithub' => (string) ($payload['socialGithub'] ?? ''),
            'socialLinkedin' => (string) ($payload['socialLinkedin'] ?? ''),
            'socialWebsite' => (string) ($payload['socialWebsite'] ?? ''),
        ];
    }

    /**
     * The shape used by a brand new portfolio.
     *
     * @return array<string, mixed>
     */
    private function emptyPayload(): array
    {
        return [
            'id' => null,
            'fullName' => '',
            'jobTitle' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'profilePic' => '',
            'aboutMe' => '',
            'eduSchool' => '',
            'eduDegree' => '',
            'eduYear' => '',
            'skills' => [],
            'projects' => [],
            'experience' => [],
            'socialGithub' => '',
            'socialLinkedin' => '',
            'socialWebsite' => '',
            'template' => 'simple',
            'isPublic' => false,
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
