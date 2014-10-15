@if($showStart)
    @if ($model && $model->exists)
        {!! Form::model($model, $formOptions) !!}
    @else
        {!! Form::open($formOptions) !!}
    @endif
@endif

@if($showFields)
    @foreach ($fields as $field)
        {!! $field->render() !!}
    @endforeach
@endif

@if($showEnd)
    {!! Form::close() !!}
@endif
