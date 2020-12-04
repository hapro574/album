<!-- BEGIN: main -->
<!-- BEGIN: err -->
<div class="alert alert-warning" role="alert">{ERROR}</div>
<!-- END: err -->
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post" enctype="multipart/form-data">
    <input class="form-control" name="id" type="hidden" value="{POST.id}" />
    <div class="container">
        <div class="form-group">
            <label for="">Tên Album : </label> <input type="text" class="form_control" name="albumname" " value="{POST.album_name}">*
        </div>
        <div class="form-group">
            <label>Image : </label> <input type="file" name="uploadfile">
        </div>
        <div class="form-group">
            <label for="">Mô tả : </label>
            <textarea name="description" class="form-control" >{POST.description}</textarea>
        </div>
        <div class="text-center">
            <input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" />
        </div>
    </div>
  
</form>
<!-- END: main -->