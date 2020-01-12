<?php
require_once('DB.php');



function parse_data($xml_file) {
	//$xml = simplexml_load_string($xml_file);
	$xml = simplexml_load_file($xml_file);
	
	$db = new DB;
	$db->connect();
	
	foreach($xml->items[0]->item as $item) {
		$images = [];
		
		$item->availability;
		$item->code; //BB7771_StanSmith
		$item->description;
		foreach($item->tags->tag as $tag) {
			if ($tag->name == gender) {
				$gender = $tag->value->translations->translation->description;
			}
			
			if ($tag->name == subcategory) {
				$cat_name = $tag->value->translations->translation->description;
			}
		}
		foreach($item->pictures->image as $pic){
			
			preg_match("/[^\/]+$/", $pic->url, $matches);
			$last_word = $matches[0]; // test
			$filename = 'catalog/brandsdist/' . $last_word;
			
			$images[] = $filename;
			download_image($pic->url); 
		}
		/*
		foreach($item->models->model as $model) {
			$model->code; //BB7771_StanSmith
			$model->availability; 
			$model->color;
			$model->model; // UK 10.0
			$model->size; // UK 10.0
		}
		$item->name; //Stan Smith
		$item->suggestedPrice;
		
		$result = '';
		$sql = "SELECT product_id FROM oc_product WHERE model = '".$item->code."'";
		$result = $db->select($sql);
		echo $result[0]['product_id'] . ' : ';
		
		if ( isset($result[0]['product_id']) AND !empty($result[0]['product_id']) ) {
			echo $result[0]['product_id'] . ' exists<br>';
			$product_id = $result[0]['product_id'];
		} else {
			$sql = "SELECT MAX(product_id) from oc_product";
			$result = $db->select($sql);
			$product_id = $result[0]['MAX(product_id)'] + 1;
			echo $product_id . ' : ';
			
			$sql = "INSERT INTO oc_product (product_id, model, quantity, stock_status_id, image, shipping, price, points, tax_class_id, date_available, weight, weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, `status`, date_added, date_modified) VALUES (".$product_id.", '".$item->code."', ".$item->availability.", 6, '".$images[0]."', 1, ".$item->suggestedPrice.", 0, 0, '".date("Y-m-d")."', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, NOW(), NOW()) ";
			$db->query($sql);
			
			$sql = "INSERT INTO oc_product_description (product_id, language_id, name, description, tag, meta_title) VALUES (".$product_id.", 1, '".$item->name."', \"".$item->description."\", '".$gender."', '".$item->code."')";
			$db->query($sql);
			
			foreach ($images as $img) {
				$sql = "INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (".$product_id.", '".$img."', 0)";
				$db->query($sql);
			}
			
			$sql = "SELECT category_id FROM oc_category_description WHERE name = '".$cat_name."'";
			$cat_result = $db->select($sql);
			$cat_id = $cat_result[0]['category_id'];
			echo ' cat_id: '.$cat_id;
			
			$sql = "INSERT INTO oc_product_to_category (product_id, category_id) VALUES (".$product_id.", ".$cat_id.")";
			$db->query($sql);
			
			$sql = "INSERT INTO oc_product_to_layout (product_id, store_id, layout_id) VALUES (".$product_id.", 0, 0)";
			$db->query($sql);
			
			$sql = "INSERT INTO oc_product_to_store (product_id, store_id) VALUES (".$product_id.", 0)";
			$db->query($sql);
			
			echo 'added' . $item->code . '<br>';
		}
		*/
	}
	
	
}

function parse_cats($xml_file){
	$xml = simplexml_load_file($xml_file);
	
	$db = new DB;
	$db->connect();
	
	foreach($xml->items[0]->item as $item){
		echo '<br>' . $item->name . ' : ';
		$cat_parent = '';
		$cat_sub = '';
		
		foreach($item->tags->tag as $tag) {
			if ($tag->name == category) {
				$cat_parent = $tag->value->translations->translation->description;
			}
			if ($tag->name == subcategory) {
				$cat_sub = $tag->value->translations->translation->description;
			}
			
			if (!empty($cat_parent) AND !empty($cat_sub)) {
				echo $cat_parent. ' ' . $cat_sub . ' <br>';
				// find cat_parent, if not -> create, yes -> get_id
				$result = '';
				$sql = "SELECT category_id FROM oc_category_description WHERE name = '".$cat_parent."'";
				$result = $db->select($sql);
				//echo $result[0]['category_id'];
				if ( isset($result[0]['category_id']) ) { // if exists
					$category_id = $result[0]['category_id'];
					echo 'cat_parent exists';
				} else { // create line
					$sql = "SELECT MAX(category_id) from oc_category_description";
					$result = $db->select($sql);
					$category_id = $result[0]['MAX(category_id)'] + 1;
					
					$sql = "INSERT INTO oc_category_description (category_id, language_id, name,  meta_title) VALUES (".$category_id.", 1, '".$cat_parent."', '".$cat_parent."')";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category (category_id, parent_id, top, `column`, sort_order, `status`, date_added, date_modified) VALUES (".$category_id.", 0, 0, 0, 0, 1, NOW(), NOW())";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category_path (category_id, path_id, level) VALUES (".$category_id.", ".$category_id.", 0)";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category_to_layout (category_id, store_id, layout_id) VALUES (".$category_id.", 0, 0)";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category_to_store (category_id, store_id) VALUES (".$category_id.", 0)";
					$db->query($sql);
					echo ' ' . $category_id . ' added ';
				}
				
				// find cat_sub, if not -> create, yes -> get_id
				
				$result2 = '';
				$sql = "SELECT category_id FROM oc_category_description WHERE name = '".$cat_sub."'";
				$result2 = $db->select($sql);
				
				echo ' res2: ' . $result2[0]['category_id'];
				
				if ( isset($result2[0]['category_id']) AND !empty($result2[0]['category_id']) ) { // if exists
					$sub_category_id = $result[0]['category_id'];
					echo 'sub cat exists';
				} else { // create line
					$sql = "SELECT MAX(category_id) from oc_category_description";
					$result = $db->select($sql);
					$sub_category_id = $result[0]['MAX(category_id)'] + 1;
					
					$sql = "INSERT INTO oc_category_description (category_id, language_id, name,  meta_title) VALUES (".$sub_category_id.", 1, '".$cat_sub."', '".$cat_sub."')";
					$db->query($sql);  
					
					$sql = "INSERT INTO oc_category (category_id, parent_id, top, `column`, sort_order, `status`, date_added, date_modified) VALUES (".$sub_category_id.", '".$cat_parent."', 0, 0, 0, 1, NOW(), NOW())"; 
					$db->query($sql); 
					
					$sql = "INSERT INTO oc_category_path (category_id, path_id, level) VALUES (".$sub_category_id.", ".$sub_category_id.", 1)";
					$db->query($sql);
					$sql = "INSERT INTO oc_category_path (category_id, path_id, level) VALUES (".$sub_category_id.", ".$category_id.", 0)";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category_to_layout (category_id, store_id, layout_id) VALUES (".$sub_category_id.", 0, 0)";
					$db->query($sql);
					
					$sql = "INSERT INTO oc_category_to_store (category_id, store_id) VALUES (".$sub_category_id.", 0)";
					$db->query($sql);
					
					echo 'added '. $sub_category_id . ' ' . $category_id . ' <br />';
				}
			}
			
		}
	}
}

function parse_gender($xml_file){
	$xml = simplexml_load_file($xml_file);
	
	$data = [];
	
	foreach($xml->items[0]->item as $item){
		foreach($item->tags->tag as $tag) {
			if ($tag->name == gender) echo $tag->value->translations->translation->description;

		}
	}
}

function download_image($url){
	$base = 'https://www.brandsdistribution.com';
	$full_url = $base . $url;
	
	preg_match("/[^\/]+$/", $full_url, $matches);
	$last_word = $matches[0]; // test
	
	$filename = 'image/catalog/brandsdist/' . $last_word;
	
	if (! file_exists($filename) ){
		file_put_contents($filename, file_get_contents($full_url));

		echo 'got ' . $filename . ' from ' . $full_url . '<br / >';
	} else {
		echo $filename . ' already exists <br />';
	}
}

function parse_options($xml_file) {
	//$xml = simplexml_load_string($xml_file);
	$xml = simplexml_load_file($xml_file);
	
	$db = new DB;
	$db->connect();
	
	foreach($xml->items[0]->item as $item) {
		$item->code;
		$sql = "SELECT product_id FROM oc_product WHERE model = '".$item->code."'";
		$res = $db->select($sql);
		$product_id = $res[0]['product_id'];
		
		foreach($item->models->model as $model) {
			$model->code; //BB7771_StanSmith
			$model->availability; 
			$model->color;
			$model->model; // UK 10.0
			$model->size; // UK 10.0
			
			$sql = "SELECT option_value_id FROM oc_option_value_description WHERE name = '".$model->size."'";
			$result = $db->select($sql);
			
			
			
			if (isset($result[0]['option_value_id']) AND !empty($result[0]['option_value_id'])){
				$option_value_id = $result[0]['option_value_id'];
				echo 'option_value_id exists <br>';
			} else {
				//$sql = "SELECT MAX(option_value_id) FROM oc_option_value_description";
				//$result = $db->select($sql);
				//$option_value_id = $result[0]['MAX(option_value_id)'] + 1;
				
				$sql = "SELECT MAX(option_value_id) FROM oc_option_value";
				$res_t = $db->select($sql);
				$option_value_id = $res_t[0]['MAX(option_value_id)'] + 1;
				
				$sql = "INSERT INTO oc_option_value_description (option_value_id, language_id, option_id, name) VALUES (".$option_value_id.", 1, 14, '".$model->size."')";
				$db->query($sql);
				
				$sql = "INSERT INTO oc_option_value (option_id, sort_order) VALUES (14, 0)"; 
				$db->query($sql);
				
				echo ' '.$model->size.' added <br>'; 
			}
			
			
				
			$sql = "INSERT INTO oc_product_option (product_id, option_id, required) VALUES (".$product_id.", 14, 1)";
			$db->query($sql);
			
			$sql = "SELECT MAX(product_option_id) FROM oc_product_option WHERE product_id = ".$product_id."";
			$res_op = $db->select($sql);
			$product_option_id = $res_op[0]['MAX(product_option_id)'];
			
			$sql = "INSERT INTO oc_product_option_value (product_option_id, product_id, option_id, option_value_id, quantity, subtract, price, price_prefix, points, points_prefix, weight, weight_prefix) VALUES (".$product_option_id.", ".$product_id.", 14, ".$option_value_id.", ".$model->availability.", 1, 0, '+', 0, '+', 0, '+')";
			$db->query($sql);
				
			echo ' '.$model->size.' added to '.$product_id.'<br>';
			
		}
	}
}
//parse_options('parser_data/products_all.xml');
//parse_data('parser_data/products_all.xml'); //parse and ? download images
//parse_cats('parser_data/products_all.xml'); //parse and add
//parse_gender('parser_data/products_all.xml');
?>