<input type="number" class="form-control" name="{{ $row->field }}"
       placeholder="{{ $row->display_name }}"
       value="@if(isset($dataTypeContent->{$row->field})){{ old($row->field, $dataTypeContent->{$row->field}) }}@else{{old($row->field)}}@endif"
       @if (isset($options) && isset($options->step)) step="{{$options->step}}"@endif >
