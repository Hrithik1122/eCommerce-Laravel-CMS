<?php 
    $data = $_POST["image"];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	$filename=time() . '.png';
	$imageName = 'upload/product/'.$filename;
	file_put_contents($imageName, $data,777);
	if($_POST['type']==1){
		echo '<img src="'.$imageName.'" class="img-thumbnail" /><input type="hidden" name="basic_img" id="basic_img" value="'.$filename.'"/>';
	}
	else{
		if(isset($_POST['nexttotal'])){
			echo '<div id="nexttotal'.$_POST['nexttotal'].'" class="add-img"><img src="'.$imageName.'" class="img-thumbnail" /><input type="hidden" name="arrimg[]" value="'.$filename.'"/><div class="add-box"><input type="button" id="removeImage1" value="x" class="btn-rmv1" onclick="removeimg('.$_POST['nexttotal'].')"  /></div></div>';
		}
		else{
			echo '<div id="imgid'.$_POST['nexttotal'].'" class="add-img"><img src="'.$fullimage.'" class="img-thumbnail" /><input type="hidden" name="arrimg[]" value="'.$filename.'"/><div class="add-box"><input type="button" id="removeImage1" value="x" class="btn-rmv1" onclick="removeimg('.$_POST['nexttotal'].')"  /></div></div>';
		}
		
	}
	

?>