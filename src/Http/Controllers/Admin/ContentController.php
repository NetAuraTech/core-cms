<?php

namespace Netauratech\CoreCms\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use JsonException;
use Netauratech\CoreCms\Http\Requests\Admin\ContentFormRequest;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Observers\ContentObserver;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Events\ContentSaved;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AdminController;

class ContentController extends AdminController
{
    protected array $permissions = [
        'content-list'   => ['index'],
        'content-create' => ['create', 'store'],
        'content-edit'   => ['edit', 'update', 'preview'],
        'content-delete' => ['destroy'],
    ];

    protected ContentProviderInterface $contentProvider;
    protected FormRegistry $formRegistry;

    public function __construct(ContentProviderInterface $contentProvider, FormRegistry $formRegistry)
    {
        parent::__construct();
        $this->contentProvider = $contentProvider;
        $this->formRegistry = $formRegistry;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $type): View
    {
        $contents = Content::where('type', $type)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

        return view('core-cms::admin.contents.index', [
            'contents' => $contents,
            'contentType' => $type,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $type): View
    {
        $content = new Content();
        $content->type = $type;
        $content->status = 'draft';
        $content->published_at = new Carbon();

        $formFields = [
            ...$this->formRegistry->getFormFields("content_form_$type"),
            ...$this->formRegistry->getFormFields("content_form")
        ];

        return view('core-cms::admin.contents.form', [
            'content' => $content,
            'contentType' => $type,
            'formFields' => $formFields,
            'articles' => $this->contentProvider->getContents("article"),
            'pages' => $this->contentProvider->getContents("page"),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentFormRequest $request, string $type): RedirectResponse
    {
        $content = new Content();
        $content->status = $request->validated('status', 'draft');
        $content->fill($request->validated());
        $content->save();

        ContentSaved::dispatch($content, $request);

        return to_route('admin.contents.index', ['type' => $type])->with('success', __('core-cms::admin.content.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content): View
    {
        $formFields = [
            ...$this->formRegistry->getFormFields("content_form_$content->type"),
            ...$this->formRegistry->getFormFields("content_form")
        ];

        return view('core-cms::admin.contents.form', [
            'content' => $content,
            'contentType' => $content->type,
            'formFields' => $formFields,
            'articles' => $this->contentProvider->getContents("article"),
            'pages' => $this->contentProvider->getContents("page"),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentFormRequest $request, Content $content): RedirectResponse
    {
        $content->status = $request->validated('status', 'draft');
        $content->update($request->validated());

        ContentSaved::dispatch($content, $request);

        $type = $content->type;

        return to_route('admin.contents.index', ['type' => $type])->with('success', __('core-cms::admin.content.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content): RedirectResponse
    {
        $content->delete();

        $type = $content->type;

        return to_route('admin.contents.index', ['type' => $type])->with('success', __('core-cms::admin.content.deleted'));
    }

    /**
     * Manages previews for content or templates.
     * @throws JsonException
     */
    public function preview(Request $request, string $type = 'content'): View
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $post = new Content();
        $post->id = 9_999_999;
        $post->title = 'Lorem ipsum dolor';
        $post->slug = 'lorem-ipsum-dolor';
        $post->created_at = now();

        $hideHeaderFooter = in_array($type, ['template']);

        $css = "";

        if (array_is_list($data)) {
            foreach ($data as $block) {
                if(!empty($block['layout-items'])) {
                    foreach ($block['layout-items'] as $item) {
                        $css = ContentObserver::generate($item, $css);
                    }
                }

                $css = ContentObserver::generate($block, $css);
            }
            return view('core-cms::admin.contents.preview', [
                'blocks' => $data,
                'content' => $post,
                'css' => $css,
                'hideHeaderFooter' => $hideHeaderFooter,
            ]);
        }

        if(!empty($data['layout-items'])) {
            foreach ($data['layout-items'] as $item) {
                $css = ContentObserver::generate($item, $css);
            }
        }

        $css = ContentObserver::generate($data, $css);

        return view('core-cms::shared.blocks.renderer', [
            'block' => $data,
            'content' => $post,
            'css' => $css,
            'hideHeaderFooter' => $hideHeaderFooter,
        ]);
    }
}
