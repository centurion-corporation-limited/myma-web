@foreach($menu as $val)
<option value="{{ $val->id }}">{{ $val->name }}</option>
@endforeach
