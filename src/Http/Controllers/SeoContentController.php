<?php

namespace Netauratech\CoreCms\Http\Controllers;

use Illuminate\Routing\Controller;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Models\Option;
use Illuminate\Http\Response;

class SeoContentController extends Controller
{
    /**
     * @var ContentProviderInterface
     */
    protected ContentProviderInterface $contentProvider;

    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * Serves the content of the dynamically generated sitemap.xml.
     *
     * @return Response
     */
    public function sitemap(): Response
    {
        $homepageOption = Option::where('key', 'homepage')->first();
        $homepageId = $homepageOption ? intval($homepageOption->value) : null;

        $urls = [
            [
                'loc' => route('home'),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ]
        ];

        $contents = Content::where('status', 'published')
            ->whereNotIn('type', ['header', 'footer', 'template'])
            ->get();

        foreach ($contents as $content) {
            if ($content->id === $homepageId) {
                continue;
            }

            $urls[] = [
                'loc' => route($content->type . '.show', $content->slug),
                'lastmod' => $content->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        return response()
            ->view('core-cms::front.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Serves the content of the static robots.txt file but with the URL of the dynamic sitemap.
     *
     * @return Response
     */
    public function robotsTxt(): Response
    {
        $sitemapUrl = route('sitemap');

        $robotsContent = "User-agent: *\n";
        $robotsContent .= "Disallow: /admin/\n";
        $robotsContent .= "Disallow: /profile/\n";
        $robotsContent .= "\nSitemap: {$sitemapUrl}\n";

        return response($robotsContent, 200)
            ->header('Content-Type', 'text/plain');
    }
}