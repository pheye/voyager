<textarea name="{{$row->field}}" id="editor_{{$row->field}}" style="width:100%;height:600px;">

    @if(isset($dataTypeContent->{$row->field}))
      {{ old($row->field, $dataTypeContent->{$row->field}) }}
    @else
      {{ old($row->field) }}
    @endif
</textarea>
<script type="text/javascript">
KindEditor.options.filterMode = false;
KindEditor.ready(function(K) {
    window.editor = K.create('#editor_{{$row->field}}',  {
                uploadJson : '{{route('voyager.kindeditor_upload', [], false)}}',
                fileManagerJson : '../php/file_manager_json.php',
                allowFileManager : true
        });
});

</script>
