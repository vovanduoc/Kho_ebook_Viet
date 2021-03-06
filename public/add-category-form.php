<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content" class="container col-md-12">
	<?php 
		if(isset($_POST['btnAdd'])){
			$category_name = $_POST['category_name'];
			$author = $_POST['author'];
			// get image info
			$menu_image = $_FILES['category_image']['name'];
			$image_error = $_FILES['category_image']['error'];
			$image_type = $_FILES['category_image']['type'];
			// create array variable to handle error
			$error = array();
			
			if(empty($category_name)){
				$error['category_name'] = " <span class='label label-danger'>Must Insert!</span>";
			}

			if(empty($author)){
				$error['author'] = " <span class='label label-danger'>Must Insert!</span>";
			}

            //image validate upload -------------------------------------------------------------
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["category_image"]["name"]));
					
			if($image_error > 0){
				$error['category_image'] = " <span class='label label-danger'>You're not insert images!!</span>";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['category_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
			}
            //END image validate upload -------------------------------------------------------------

            //pdf ###############################################################################3

            // get image info
            $menu_pdf = $_FILES['category_pdf']['name'];
            $pdf_type = $_FILES['category_pdf']['type'];
            $error_pdf = array();
            $extension_pdf = end(explode(".", $_FILES["category_pdf"]["name"]));
//            $f_info = finfo_open(FILEINFO_MIME_TYPE);
//            $mime = finfo_file($f_info, $_FILES['category_pdf']['name']);
//            if ($mime != "application/pdf") {
//                if($_FILES['category_pdf']['error'] > 0) {
//                    $error_pdf['category_pdf'] = " <span class='label label-danger'>Please provide another file pdf type!!</span>";
//                }
//

            //END PDF ###############################################################################3

			if(!empty($category_name) && !empty($author) && !empty($menu_pdf) && empty($error['category_image'])){
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['category_image']['name']);
				$file_pdf = preg_replace("/\s+/", "_", $_FILES['category_pdf']['name']);
				$function = new functions;
                $string_name = $function->get_random_string($string, 6);
				$menu_image = $string_name."-".date("Y-m-d").".".$extension;
				$menu_pdf = $string_name."-".date("Y-m-d").".".$extension_pdf;
				// upload new image
				$upload = move_uploaded_file($_FILES['category_image']['tmp_name'], 'upload/category/'.$menu_image);
                move_uploaded_file($_FILES['category_pdf']['tmp_name'], 'upload/pdf/'.$menu_pdf);
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_news_category (category_name, category_image, author)
						VALUES(?, ?, ?)";
				
				$upload_image = $menu_image;
				$upload_pdf = $menu_pdf;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('sss', 
								$category_name, 
								$upload_image,
								$author
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_category'] = " <h4><div class='alert alert-success'>
														* New Category success added.
														<a href='category.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
				}else{
					$error['add_category'] = " <span class='label label-danger'>Failed add category</span>";
				}
			}
			
		}

		if(isset($_POST['btnCancel'])){
			header("location: category.php");
		}

	?>
	<div class="col-md-12">
		<h1>Add New Book</h1>
		<?php echo isset($error['add_category']) ? $error['add_category'] : '';?>
		<hr />
	</div>
	
	<div class="col-md-5">
		<form method="post"
			enctype="multipart/form-data">
			<label>Book Name :</label><?php echo isset($error['category_name']) ? $error['category_name'] : '';?>
			<input type="text" class="form-control" name="category_name"/>
			<br/>
			<label>Author Name :</label><?php echo isset($error['author']) ? $error['author'] : '';?>
			<input type="text" class="form-control" name="author"/>
			<br/>
			<label>Image :</label><?php echo isset($error['category_image']) ? $error['category_image'] : '';?>
			<input type="file" name="category_image" id="category_image" />
			<br/>
            <label>File PDF :</label><?php echo isset($error_pdf['category_pdf']) ? $error_pdf['category_pdf'] : '';?>
            <input type="file" name="category_pdf"/>
            <br/>
			<input type="submit" class="btn-primary btn" value="Submit" name="btnAdd"/>
			<input type="reset" class="btn-warning btn" value="Clear"/>
			<input type="submit" class="btn-danger btn" value="Cancel" name="btnCancel"/>
		</form>
	</div>

	<div class="separator"> </div>
</div>
	
<?php include_once('includes/close_database.php'); ?>