@if($lazy)
    <img class="dvacom-lazy w-100" src="{{$src}}" data-src="{{$dataSrc}}" width="{{$size['width']}}" height="{{$size['height']}}" alt="{{$alt}}">
@else
    <img class="w-100" src="{{$dataSrc}}" width="{{$size['width']}}" height="{{$size['height']}}" alt="{{$alt}}">
@endif
