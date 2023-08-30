@extends('statamic::layout')
@section('title', 'Autograph')

@section('content')
    <header class="mb-6">
        <h1>@yield('title')</h1>
    </header>

    <div class="card">
        <form method="POST" action="{{ cp_route('autograph.generate.index') }}">
            @csrf

            <div class="publish-fields">
                <div
                    class="
                        form-group publish-field publish-field__heading textarea-fieldtype w-full
                        @if ($errors->has('user_id')) has-error @endif
                    ">

                    <div class="field-inner">
                        <label for="field_heading" class="publish-field-label">
                            <span class="mr-1">{{ __('statamic-autograph::messages.user') }}</span>
                        </label>
                    </div>

                    <div class="select-input-container w-full">
                        <select class="select-input pl-4" name="user_id">
                            @if ($allow_empty_user)
                                <option value="" selected>-</option>
                            @elseif(!count($users))
                                <option disabled>{{ __('statamic-autograph::messages.no_users') }}</option>
                            @endif


                            @foreach ($users as $user)
                                <option value="{{ $user->id() }}"
                                    @if (old('user_id') == $user->id) selected="selected" @endif>
                                    {{ $user_formatter($user) }}
                                </option>
                            @endforeach
                        </select>

                        <div class="select-input-toggle pr-4">
                            <svg-icon name="micro/chevron-down-xs" class="w-2"></svg-icon>
                        </div>

                        @if ($errors->has('user_id'))
                        <div>
                            <small class="help-block text-red-500 mt-2 mb-0">{{ $errors->first('user_id') }}</small>
                        </div>
                    @endif
                    </div>
                </div>

                <div
                    class="
                    form-group publish-field publish-field__heading textarea-fieldtype w-full
                    @if ($errors->has('template_path')) has-error @endif
                ">
                    <div class="field-inner">
                        <label for="field_heading" class="publish-field-label">
                            <span class="mr-1">{{ __('statamic-autograph::messages.template') }}</span>
                        </label>
                    </div>

                    <div class="select-input-container w-full mt-2">
                        <select class="select-input pl-4" name="template_path">
                            @if (count($templates))
                                @foreach ($templates as $template)
                                    <option value="{{ $template['path'] }}"
                                        @if (old('template_path') == $template['path']) selected="selected" @endif>
                                        {{ $template['label'] }}
                                    </option>
                                @endforeach
                            @else
                                <option disabled>{{ __('statamic-autograph::messages.no_templates') }}</option>
                            @endif
                        </select>

                        <div class="select-input-toggle pr-4">
                            <svg-icon name="micro/chevron-down-xs" class="w-2"></svg-icon>
                        </div>
                    </div>

                    @if ($errors->has('template_path'))
                        <div>
                            <small class="help-block text-red-500 mt-2 mb-0">{{ $errors->first('template_path') }}</small>
                        </div>
                    @endif

                </div>
            </div>

            <div class="form-group publish-field publish-field__heading textarea-fieldtype w-full">
                <button class="btn-primary mt-2">{{ __('statamic-autograph::messages.generate') }}</button>
            </div>
        </form>
    </div>

    @if ($code_snippet)
        <div class="publish-sections">
            <div class="publish-sections-section">
                <div class="card p-0 mt-10 ">
                    <header class="publish-section-header">
                        <div class="publish-section-header-inner">
                            <label
                                class="text-base font-semibold">{{ __('statamic-autograph::messages.html_code') }}</label>
                            <div class="help-block">
                                <p>
                                <p>{{ __('statamic-autograph::messages.html_code_instructions') }}</p>
                                </p>
                            </div>
                        </div>
                    </header>
                    <div class="publish-fields">
                        <div class="form-group publish-field publish-field__related relationship-fieldtype w-full">
                            <div class="field-inner">
                                <div class="prose" style="max-width: none;">
                                    <code-block copyable text="{{ $code_snippet }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="publish-sections-section">
                <div class="card p-0 mt-10 ">
                    <header class="publish-section-header">
                        <div class="publish-section-header-inner">
                            <label class="text-base font-semibold">{{ __('statamic-autograph::messages.preview') }}</label>
                            <div class="help-block">
                                <p>
                                <p>{{ __('statamic-autograph::messages.preview_instructions') }}</p>
                                </p>
                            </div>
                        </div>
                    </header>
                    <div class="publish-fields">
                        <div class="form-group publish-field publish-field__related relationship-fieldtype w-full">
                            <div class="field-inner" id="preview-container">
                                <iframe srcdoc="{{ $code_snippet }}" id="preview" class="w-full" height="500"
                                    onload="onLoad()"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function onLoad() {
            // Get a reference to your iframe element
            var iframe = document.getElementById('preview');
            // Set up auto resizing for the iframe
            if (iframe) {
                resizeIframeToFitContent(iframe);
                setupIframeAutoResizing(iframe);
            }
        }

        function resizeIframeToFitContent(iframe) {
            if (iframe.contentWindow) {
                // Remove body margin because it's not included in the scrollHeight
                iframe.contentWindow.document.body.style.margin = 0;
                // Set iframe height to it's contents height
                iframe.height = iframe.contentWindow.document.body.scrollHeight;
            }
        }

        function setupIframeAutoResizing(iframe) {
            // Observe changes in the iframe's body and its descendants
            new MutationObserver(function() {
                resizeIframeToFitContent(iframe);
            }).observe(iframe.contentWindow.document.body, {
                attributes: true,
                childList: true,
                subtree: true
            });
        }
    </script>
@stop
