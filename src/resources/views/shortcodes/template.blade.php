@foreach($template->getContent() as $block)
    @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
@endforeach