<?php

namespace Netauratech\CoreCms\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Models\Option;

class PageController extends Controller
{
    protected ContentProviderInterface $contentProvider;

    protected FormRegistry $formRegistry;

    public function __construct(ContentProviderInterface $contentProvider, FormRegistry $formRegistry)
    {
        $this->contentProvider = $contentProvider;
        $this->formRegistry = $formRegistry;
    }

    /**
     * Displays the site's home page.
     * The page is determined by the ‘homepage’ option.
     *
     * @return View
     */
    public function homepage(): View
    {
        $homepageOption = Option::where('key', 'homepage')->first();
        $homepageContentId = $homepageOption ? $homepageOption->value : null;

        $content = null;
        if ($homepageContentId) {
            $content = $this->contentProvider->getContentById((int) $homepageContentId);
        }

        if (!$content) {
            abort(404, 'Page d\'accueil non configurée ou introuvable.');
        }

        return view('core-cms::front.page', [
            'content' => $content,
            'isHomepage' => true,
            'metas' => $this->formRegistry->getFormFields('content_meta'),
        ]);
    }
}