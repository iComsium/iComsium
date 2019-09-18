<html>
<form enctype="multipart/form-data" action="ps.php" method="POST">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="9000000" />
<!-- Name of input element determines name in $_FILES array -->
Send this file: <input name="userfile" type="file" />
<input type="submit" value="Send File" />
</form>
</html>