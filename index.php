<?php
    if (isset($_FILES['attachments'])) {
        $msg = "";
        $targetFile =  time() . basename($_FILES['attachments']['name'][0]);
        if (file_exists($targetFile))
            $msg = array("status" => 0, "msg" => "File already exists!");
        else if (move_uploaded_file($_FILES['attachments']['tmp_name'][0], "uploads/" . $targetFile)){
            $msg = array("status" => 1, "msg" => "File Has Been Uploaded", "path" => "uploads/" . $targetFile);

          $conn = new mysqli("localhost", "root", "", "photoalbum");
          $conn -> query("INSERT INTO galeri (path, uploadedOn) VALUES ('$targetFile', NOW())");
        }
        exit(json_encode($msg));
    }
?>
<html>
	<head>
		<title>Photo Album</title>
		<style type="text/css">
			#dropZone {
				border: 3px dashed #0088cc;
				padding: 50px;
				width: 500px;
				margin-top: 20px;
			}

			#files {
				border: 1px dotted #0088cc;
				padding: 20px;
				width: 200px;
				display: none;
			}

            #error {
                color: red;
            }
            .container {
              margin-top: 50px; 
            }
		</style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	</head>
	<body>
		<div class="container">
      <div class="row">
        <div class="col-md-12" align="center">
  			 <img src="images/pala.png"><br><br>
  			 <div id="dropZone">
  				  <h1>Masukkan Files..</h1>           
  				  <input type="file" id="fileupload" name="attachments[]" multiple>
  			</div>
  			 <h1 id="error"></h1><br><br>
  			 <h1 id="progress"></h1><br><br>
  			<div id="files"></div>
		  </div>
    </div>
  </div>
  <div class="container" id="uploadedFiles">
    <div class="row"></div>
  </div>

		<script src="http://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
		<script src="js/jquery.iframe-transport.js" type="text/javascript"></script>
		<script src="js/jquery.fileupload.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function () {
               var files = $("#files");

               $("#fileupload").fileupload({
                   url: 'index.php',
                   dropZone: '#dropZone',
                   dataType: 'json',
                   autoUpload: false
               }).on('fileuploadadd', function (e, data) {
                   var fileTypeAllowed = /.\.(gif|jpg|png|jpeg)$/i;
                   var fileName = data.originalFiles[0]['name'];
                   var fileSize = data.originalFiles[0]['size'];

                   if (!fileTypeAllowed.test(fileName))
                        $("#error").html('Only images are allowed!');
                   else if (fileSize > 500000)
                       $("#error").html('Your file is too big! Max allowed size is: 500KB');
                   else {
                       $("#error").html("");
                       data.submit();
                   }
               }).on('fileuploaddone', function(e, data) {
                    var status = data.jqXHR.responseJSON.status;
                    var msg = data.jqXHR.responseJSON.msg;

                    if (status == 1) {
                        var path = data.jqXHR.responseJSON.path;

                      if ($("#uploadedFiles").find('.row:last').find('.myImg').length === 3)
                          $("#uploadedFiles").append('<div class="row"></div>');

                        $("#uploadedFiles").find('.row:last').append('<div class="col-md-3"><img style="width: 100px; height: 100px;" src="'+path+'" /></div>');
                    } else
                        $("#error").html(msg);
               }).on('fileuploadprogressall', function(e,data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $("#progress").html("Completed: " + progress + "%");
               });
            });
        </script>
	</body>
</html>