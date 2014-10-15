@if ( $showLabel && $showField && !$options['is_child'] )
<div {!! $options['wrapperAttrs'] !!} >
@endif

    @if ($showLabel)
        @if($options['is_child'])
            <label {!! $options['labelAttrs'] !!}>{!! $options['label'] !!}</label>
        @else
            {!! Form::label($name, $options['label'], $options['label_attr']) !!}
        @endif
    @endif

    @if ($showField)
        {!! Form::radio($name, $options['default_value'], $options['selected'], $options['attr']) !!}
    @endif

    @if ($showError && isset($errors))
        {!! $errors->first($name, '<div '.$options['errorAttrs'].'>:message</div>') !!}
    @endif

@if ( $showLabel && $showField && !$options['is_child'] )
</div>
@endif
