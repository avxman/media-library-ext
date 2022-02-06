@if($lazy)
    <source media="{{$media}}" type="{{$type}}" srcset="{{$src}}" data-srcset="{{$srcset}}">
@else
    <source media="{{$media}}" type="{{$type}}" srcset="{{$srcset}}">
@endif
