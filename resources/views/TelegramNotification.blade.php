<b>{{ $title }}</b>
Date: {{ date("Y-m-d H:i:s") }}
Details:
@foreach($data as $key => $value)
    <b>{{ $key }}</b>: {{ $value }}
@endforeach
