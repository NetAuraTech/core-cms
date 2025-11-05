<?php

namespace Netauratech\CoreCms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Netauratech\CoreCms\Mail\GenericFormMail;
use Netauratech\CoreCms\Contracts\ChallengeInterface;
//use netauratech\CoreCms\Mail\FormMail;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class FormSubmissionController extends Controller
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
     * Handles the submission of a form dynamically generated from content.
     *
     * @param string $slug The slug of the content that contains the form.
     * @param string $formType The type of form to be processed (e.g., ‘contact’, ‘form’).
     * @param Request $request The incoming HTTP request.
     * @param ChallengeInterface $challenge The service interface for the Captcha.
     * @return RedirectResponse
     */
    public function submit(string $slug, string $formType, Request $request, ChallengeInterface $challenge): RedirectResponse
    {
        $post = $this->contentProvider->getContentBySlug($slug);

        if (!$post) {
            abort(404, 'Contenu introuvable pour la soumission du formulaire.');
        }

        $rules = [
            'captcha-challenge' => ['required', 'string'],
            'captcha-answer' => ['required', 'string']
        ];
        $mailData = $request->all();
        $mailSections = [];

        switch ($formType) {
            case 'contact':
                $rules['email'] = ['required', 'email', 'max:255'];
                $rules['lastname'] = ['required', 'string', 'max:255'];
                $rules['firstname'] = ['required', 'string', 'max:255'];
                $rules['phone'] = ['required', 'string'];
                $rules['subject'] = ['required', 'string'];
                $rules['content'] = ['required', 'string'];
                break;

            case 'form':
                foreach ($post->getContent() as $bloc) {
                    if($bloc["_name"] == 'form') {
                        $mailSections = $bloc['sections'];
                        foreach ($mailSections as $section) {
                            if(key_exists('visible', $section) && $section['visible']) {
                                foreach ($section['fields'] as $field) {
                                    $name = Str::slug($field['label']);
                                    $type = $field['type'] ?? 'text';

                                    switch ($type) {
                                        case 'text':
                                        case 'textarea':
                                            $rules[$name] = ['nullable', 'string', 'max:1000'];
                                            break;
                                        case 'select':
                                            $validOptions = collect($field['options'] ?? [])
                                                ->pluck('option')
                                                ->map(fn($opt) => Str::slug($opt))
                                                ->toArray();
                                            $rules[$name] = ['required', 'in:' . implode(',', $validOptions)];
                                            break;
                                        case 'checkbox':
                                            $rules[$name] = ['nullable', 'boolean'];
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
                break;

            default:
                return back()->withInput()->with('error', __('core-cms::core.form.invalid'));
        }

        $validated = $request->validate($rules);

        if ($challenge->verify($validated['captcha-challenge'], $validated['captcha-answer'])) {
            switch ($formType) {
                case 'contact':
                    Mail::queue(new GenericFormMail($mailData, GenericFormMail::TYPE_CONTACT, $request));
                    break;
                case 'form':
                    Mail::queue(new GenericFormMail($mailData, GenericFormMail::TYPE_GENERAL_FORM, $request, $mailSections));
                    break;
            }

            return back()->with('success', __('core-cms::core.form.confirmed'));
        }

        return back()->withInput()->with('error', __('core-cms::core.captcha.invalid'));
    }
}