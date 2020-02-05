<?php
/*
J & K Cabinetry
Created by Jill Atkins (admin@cybril.com)
March 2017
*/

class admin {

    var $db, $conn;
	
    public $siteadminemail = 'saleswa@grandjk.com';
	public $siteurl = 'https://www.grandjk.com/catalog/';
	public $sitename = 'Grand JK Cabinetry';
	public $image_path = '/home1/grandjkc/public_html/catalog/images/'; //directory for uploading images (check this)
	public $image_display = '../images/'; //directory for displaying images 
	public $accepted_types = array("image/gif","image/jpeg","image/pjpeg","image/png","image/tiff","image/x-tiff","image/x-png");
	public $admin_username = 'grandadmin';
	public $admin_password = 'UX2YX34(*&!';

    public function __construct(){
		
		$server = 'localhost';
//		$username = 'grandjkc_catalog';
//		$database = 'grandjkc_catalog';
//		$password = '~(AFnUzd1xXE';
		$username = 'grandjkc_catlog';
		$database = 'grandjkc_catalog';
		$password = '%#mPW{8,m#c=';
		
        $this->conn = mysqli_connect($server, $username, $password, $database);
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
    public function update_array_new($table, $key_column_name, $id, $update_values) {
        echo $update_values ;
        foreach($update_values as $key=>$value) {
            $sets[] = $key.'=\''.$value.'\'';
        }
		
        $sets = implode(',', $sets);
//        echo  $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 8 or `colour_id` = 10 or `colour_id` = 15 or `colour_id` = 9 or `colour_id` = 11 or `colour_id` = 7 or `colour_id` = 3 or `colour_id` = 1 or `colour_id` = 6) ";
        
//        echo  $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 3 or `colour_id` = 1 or `colour_id` = 6) ";
//        
//        echo  $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 8 or `colour_id` = 10 or `colour_id` = 15 or `colour_id` = 9 or `colour_id` = 11 or `colour_id` = 7 or `colour_id` = 3 or `colour_id` = 1 or `colour_id` = 6) ";
//        
// for main page S1|S2|S5|S8 |K10|J5| CO66
      // $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 8 or `colour_id` = 10 or `colour_id` = 15 or `colour_id` = 9 or `colour_id` = 2 or `colour_id` = 7 or `colour_id` = 6) ";
      // 
//        $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 4 or `colour_id` = 2 or `colour_id` = 5) ";
    // for main page S1=8 |S2 =10|S5=15|S8=9|J5=7|H9=11|A7=3|M01=1|CO66=6
          //echo  $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 5 or `colour_id` = 2 or `colour_id` = 4) ";
//           $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 7 or `colour_id` = 2 or `colour_id` = 6) ";

// // All Colours    
// for main page S1=8 |S2 =10|S5=15|S8=9|J5=7|H9=11|A7=3|M01=1|CO66=6|K3=5|K10=2|K8=4
//        $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 8 or `colour_id` = 10 or `colour_id` = 15 or `colour_id` = 9 or `colour_id` = 7 or `colour_id` = 11 or `colour_id` = 3 or `colour_id` = 1 or `colour_id` = 6 or `colour_id` = 5 or `colour_id` = 2 or `colour_id` = 4) ";
     // for main page K3=5|K10=2|K8=4
        $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 1 or `colour_id` = 3 or `colour_id` = 4 or `colour_id` = 5) ";
     // for main page K3=5|K10=|K8=4 
//        $sql = "UPDATE $table SET $sets WHERE $key_column_name = '$id' and (`colour_id` = 5 or `colour_id` = 2 or `colour_id` = 4) ";
        $this->sql_run($sql);
    }

    public function get_array($sql){

        $result = $this->sql_run($sql);
        
        while($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }

        return $records;
		
    }

    public function delete($table, $key_column_name, $id) {

        $sql = "DELETE FROM $table WHERE $key_column_name = '$id'";
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
			if ($selected) {
				if (is_array($selected) && in_array($row[$id_field],$selected)) $select .= ' selected="selected"';
				else if ($selected == $row[$id_field]) $select .= ' selected="selected"';
			}
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

	public function resizeImage($source, $destination = NULL, $w = 21, $h = 16, $destroy = 0) {
		
		$im_str =  @file_get_contents($source) or die('I cannot open '.$source); 
		$source = @imagecreatefromstring($im_str) or die('Not a recognised image format'); 
		$x = imagesx($source); 
		$y = imagesy($source); 
		if($w && ($x < $y)) $w = round(($h / $y) * $x); // maintain aspect ratio 
		else $h = round(($w / $x) * $y);                // maintain aspect ratio 
		$slate = @imagecreatetruecolor($w, $h) or die('Invalid image dimmensions'); 
		imagecopyresampled($slate, $source, 0, 0, 0, 0, $w, $h, $x, $y); 
		if(!$destination) header('Content-type: image/jpeg'); 
		@imagejpeg($slate, $destination, 100) or die('Directory permission problem'); 
		if ($destroy == 1) {
			imagedestroy($slate); 
			imagedestroy($source);
		} 
		if(!$destination) exit;   
		return true; 
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
        die('error: '. $sql);
    }
}
?>