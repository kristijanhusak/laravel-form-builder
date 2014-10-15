@if ($showLabel && $showField)
<div {!! $options['wrapperAttrs'] !!} >
@endif

    @if ($showLabel)
    {!! Form::label($name, $options['label'], $options['label_attr']) !!}
    @endif

    @if ($showField)
        @foreach($options['children'] as $child)
            {!! $child->render() !!}
        @endforeach
    @endif

    @if ($showError && isset($errors))
        {!! $errors->first($name, '<div '.$options['errorAttrs'].'>:message</div>') !!}
    @endif

@if ($showLabel && $showField)
</div>
@endif
