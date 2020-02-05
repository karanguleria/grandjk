<?php

/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

class grandclass {

    var $conn;

    public $siteadminemail = 'saleswa@grandjk.com';
	public $siteurl = 'https://www.grandjk.com/catalog/';
	public $sitename = 'Grand JK Cabinetry';
	public $image_path = '/home1/grandjkc/public_html/catalog/images/'; //directory for uploading images (check this)
	public $image_display = '/catalog/images/'; //directory for displaying images 
	public $accepted_types = array("image/gif","image/jpeg","image/pjpeg","image/png","image/tiff","image/x-tiff","image/x-png");
	public	$server = 'localhost';
//	public	$username = 'grandjkc_catalog';
//	public	$database = 'grandjkc_catalog';
//	public	$password = '~(AFnUzd1xXE';
        
	public	$username = 'grandjkc_catlog';
	public	$database = 'grandjkc_catalog';
	public	$password = '%#mPW{8,m#c=';

    public function __construct(){		

        $this->conn = mysqli_connect($this->server, $this->username, $this->password, $this->database);
		if (mysqli_connect_error()) die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());

    }

    public function insert_array($table, $insert_values) {

        foreach($insert_values as $key=>$value) {
            $keys[] = $key;
            $insertvalues[] = '\''.$value.'\'';
        }

        $keys = implode(',', $keys);
        $insertvalues = implode(',', $insertvalues);

        $sql = "INSERT INTO $table ($keys) VALUES ($insertvalues)";
		$this->sql_run($sql);

    }

    public function update_array($table, $key_column_name, $id, $update_values) {

        foreach($update_values as $key=>$value) {
            $sets[] = $key.'=\''.$value.'\'';
        }
		
        $sets = implode(',', $sets);

        $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id'";
        $this->sql_run($sql);
    }

    public function get_array($sql){
		
        $result = $this->sql_run($sql);
        
        while($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }

        return $records;
		
    }

    public function delete($table, $key_column_name, $id, $extra = '') {

        $sql = "DELETE FROM $table WHERE $key_column_name = '$id'";
		if ($extra != '') $sql .= " AND $extra";
        $this->sql_run($sql);
    }

    public function get_row($sql){

        $result = $this->sql_run($sql);
        return mysqli_fetch_assoc($result);

    }

    public function get_num_records($sql){

        $result = $this->sql_run($sql);
        return mysqli_num_rows($result);

    }

	public function get_latest_id(){

        return mysqli_insert_id($this->conn);

    }

    public function get_assoc_array($sql,$id_field,$text_field){

        $result = $this->sql_run($sql);
        
        while($row = mysqli_fetch_assoc($result)) {
            $assoc_array[$row[$id_field]] = stripslashes($row[$text_field]);
        }

        return $assoc_array;

    }

	public function get_select($sql,$id_field,$text_field,$selected='') {
	
		$select = '';
        $result = $this->sql_run($sql);
     
		while($row = mysqli_fetch_assoc($result)) {
            $select .= '<option value="'.$row[$id_field].'"';
			if ($selected != '' && $selected == $row[$id_field]) $select .= ' selected="selected"';
			$select .= '>'. stripslashes($row[$text_field]) .'</option>';
        }

        return $select;
	
	}

   	public function prepare_string($string) {

		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		$string = mysqli_real_escape_string($this->conn,$string);
		return $string;
	}

 	public function send_email($email,$subject,$message) {

		$adminemail = $this->siteadminemail;
		$siteurl = $this->siteurl;
		$sitename = $this->sitename;
        $image_display = $this->image_display;
		$header = '<img src="'.$siteurl.$image_display.'logo.jpg" border="0" />';
				
		$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>'.$subject.'</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style>
		/* For iOS devices and Apple Mail */
		body {
			background-color:#ffffff; 
			margin:0; 
			padding:0; 
			font:14px/20px "Trebuchet MS", Arial, Helvetica, sans-serif;
			color:#95918f;
		}
		.social {
			float: center;	
		}
		.social a {
			color: #4d4947;
			font-weight: bold ;	
		}
		</style>
		</head>
		<body style="color:#000000; margin:0; padding:0;">
			<!-- Wrapper table -->
			 <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						   <td align="center" style="background-color:#ffffff; color:#000000;" valign="top">
							 <!-- Content table -->
							 '.$header.'
								 <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                                        <tr><td>&nbsp;</td></tr>
										 <tr>
											   <td align="left" colspan="2" width="600" style="background-color:#ffffff;">
                                               <table><tr><td>
												'.$message.'
											   </td></tr></table></td>
										 </tr>
								 </table><br /><br />
								 <!-- End Content table -->
						   </td>
					</tr>
			 </table>
			 <!-- End Wrapper table -->
		</body>
		</html>';
			
		mail($email, $subject, 
		$body, 
		"From: $sitename <$adminemail>\n" . 
		"MIME-Version: 1.0\n" . 
		"Content-type: text/html; charset=iso-8859-1"); 
	
	}

    public function sql_run($sql) {
        
        $return_result = mysqli_query($this->conn,$sql);
        if($return_result) {
            return $return_result;
        } else {
            $this->sql_error($sql);
        }
    }

    private function sql_error($sql) {
       echo mysqli_error($this->conn).'<br>';
       die('Error: '. $sql);
    }
}
?>