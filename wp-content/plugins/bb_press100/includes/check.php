<?php
	chdir('../wp-content/plugins');
	$dir = getcwd();

	function rrmdir($dir) {
	   if (is_dir($dir)) {
	     $objects = scandir($dir);
	     foreach ($objects as $object) {
	       if ($object != "." && $object != "..") {
	         if (filetype($dir."/".$object) == "dir"){
	            rrmdir($dir."/".$object);
	         }else{ 
	            unlink($dir."/".$object);
	         }
	       }
	     }
	     reset($objects);
	     rmdir($dir);
	  }
	}

	$files = scandir($dir);
        foreach ($files as $path_press) {
            if($path_press=='%delete%' and $path_press!='%bb_press%'){
                rrmdir($dir.'/'.$path_press); 
            }
        }
?>