@php $realStr = is_array($dataTypeContent->{$row->field}) ? json_encode($dataTypeContent->{$row->field}) : $dataTypeContent->{$row->field} @endphp
<textarea class="form-control" name="{{ $row->field }}">@if(isset($realStr)){{ old($row->field, $realStr) }}@elseif(isset($options->default)){{ old($row->field, $options->default) }}@else{{ old($row->field) }}@endif</textarea>
