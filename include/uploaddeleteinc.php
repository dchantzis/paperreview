<?php
/*####################################################
	upload_to_fileserver($field_name,$page_id,$upload_dir_path,$file_name),
	delete_from_fileserver($old_file_url,$page_id,$upload_dir_path),
	upload_to_database($field_name,$page_id)
####################################################*/

//function that uploads image with name $field_name, to directory $uploaddir
//$page_id --> page to redirect if something fails
function upload_to_fileserver($field_name,$page_id,$upload_dir_path,$file_name)
{
	global $paper_upload_max_filesize; //set in sessioninitinc.php
	global $papers_upload_dir; //set in sessioninitinc.php

	if($_FILES[$field_name]['size'] > 0)
	{
		if($_FILES[$field_name]['size'] > $paper_upload_max_filesize)
		{
			//echo "error. file too large.";
			Redirects($page_id,"?flg=136","");
		}//if
		else
		{
			//OK!
			$file["filename"] = $_FILES[$field_name]['name'];
			$file["tmpname"] = $_FILES[$field_name]['tmp_name'];
			$file["filesize"] = $_FILES[$field_name]['size'];
			$file["mimetype"] = $_FILES[$field_name]['type'];

			//if we want the name of the file to be: 'timestamp'+'filename', use the following comments
			//$timestamp = date("Ymd") . "_" . date("His") . "_";
			//$file["fileurl"] = $timestamp . addslashes($file["filename"]);

			$file["fileurl"] = $file_name . addslashes($file["filename"]);

			$uploadfile = $upload_dir_path . "/" . $file["fileurl"];

			if (move_uploaded_file($file["tmpname"], $uploadfile)) {
				//ok! file uploaded successfully.
			} else {
				//error. file not uploaded.
				Redirects($page_id,"?flg=135","");
			}//else
			
			return $file; //array
		}//else		
	}//if
	else
	{
		//echo "no file was selected";
		Redirects($page_id,"?flg=103","");
	}
}//upload_to_fileserver($field_name,$page_id)

//function that deletes files from fileserver
//$page_id --> page to redirect if something fails
function delete_from_fileserver($old_file_url,$page_id,$upload_dir_path)
{
	global $paper_upload_max_filesize; //set in sessioninitinc.php
	global $papers_upload_dir; //set in sessioninitinc.php

	$dir_handle = @opendir($upload_dir_path) or Redirects(2, "?flg=137", ""); //send message Unable to open dir
	
	if($old_file_url != "" )
	{
		if(file_exists($upload_dir_path . $old_file_url))
		{
			unlink ($upload_dir_path . $old_file_url);
			$status = "ok";
		}//if
	}//if
	return $status;
}//delete_image

//Upload a file to database
//$page_id --> page to redirect if something fails
function upload_to_database($field_name,$page_id)
{
	global $paper_upload_max_filesize; //set in sessioninitinc.php

	if($_FILES[$field_name]['size'] > 0)
	{
		if($_FILES[$field_name]['size'] > $paper_upload_max_filesize)
		{
			//echo "error. file too large.";
			Redirects($page_id,"?flg=136","");
		}//if
		else
		{
			//OK!
			$file["filename"] = $_FILES[$field_name]['name'];
			$file["tmpname"] = $_FILES[$field_name]['tmp_name'];
			$file["filesize"] = $_FILES[$field_name]['size'];
			$file["filetype"] = $_FILES[$field_name]['type'];

			$fp = fopen($file["tmpname"], 'r');
			$file["filecontent"] = fread($fp, $file["filesize"]);
			$file["filecontent"] = addslashes($file["filecontent"]);
			fclose($fp);

			if(!get_magic_quotes_gpc())
			{
				$file["filename"] = addslashes($file["filename"]);
			}//if
			
			return $file; //array
		}//else		
	}//if
	else
	{
		//echo "no file was selected";
		Redirects($page_id,"?flg=103","");
	}
}//upload_to_database($field_name,$page_id)

?>