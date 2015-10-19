<?php
	set_time_limit(0);
	$con=mysqli_connect("HOST_NAME","USER_NAME","PASSWORD","DB_NAME");
	if (mysqli_connect_errno()) {
		die("<script>alert(\"Database connection failed. Reload, try again later or inform webmaster.\");</script>");
	}
	require 'phpQuery/phpQuery.php';
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	$i=$argv[1];
	do{
		curl_setopt($curl,CURLOPT_URL,"http://www.flipkart.com/mobiles/pr?sid=tyy%2C4io&start=".$i."&ajax=true");
		$page = phpQuery::newDocumentHTML(curl_exec($curl));
		$items=pq("div.gd-col");
	}while(count($items->length)==0);
	foreach($items as $item){
		$productData=[
			"category"=>mysqli_real_escape_string($con,trim(pq($item)->find(".browse-product")->attr("data-vertical"))),
			"pid"=>mysqli_real_escape_string($con,trim(pq($item)->find(".product-unit")->attr("data-pid"))),
			"title"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-title a")->text())),
			"link"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-title a")->attr("href"))),
			"image"=>mysqli_real_escape_string($con,trim(pq($item)->find("img")->attr("src"))),
			"price"=>mysqli_real_escape_string($con,trim(pq($item)->find(".fk-font-17")->text())),
			"emi"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-emi")->text())),
			"feature1"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-usp li:nth-child(1) span")->text())),
			"feature2"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-usp li:nth-child(2) span")->text())),
			"feature3"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-usp li:nth-child(3) span")->text())),
			"feature4"=>mysqli_real_escape_string($con,trim(pq($item)->find(".pu-usp li:nth-child(4) span")->text())),
			"stars"=>mysqli_real_escape_string($con,trim(pq($item)->find(".fk-stars-small")->attr("title")))
		];
		$query = "SELECT * FROM `products` WHERE `pid`='".$productData['pid']."';";
		$result = mysqli_query($con,$query);
		//echo var_dump($productData);
		//echo "Product ID ".$productData['pid']." checked with query ".$query.". ".mysqli_num_rows($result)." rows returned.\n";
		if(!mysqli_num_rows($result)){
			$query = "INSERT INTO `products`(`pid`, `title`, `price`, `stars`, `img`, `emi`, `category`, `link`, `spec1`, `spec2`, `spec3`, `spec4`) VALUES ('".$productData['pid']."','".$productData['title']."','".$productData['price']."','".$productData['stars']."','".$productData['image']."','".$productData['emi']."','".$productData['category']."','".$productData['link']."','".$productData['feature1']."','".$productData['feature2']."','".$productData['feature3']."','".$productData['feature4']."');";
			if(!mysqli_query($con,$query))
				echo "Entry failed for Product ID ".$productData['pid']."\nQuery: ".$query."\n";
		}
		else if(!sizeof(array_diff(mysqli_fetch_array($result), $productData))){
			$query = "UPDATE `products` SET `pid`='".$productData['pid']."',`title`='".$productData['title']."',`price`='".$productData['price']."',`stars`='".$productData['stars']."',`img`='".$productData['image']."',`emi`='".$productData['emi']."',`category`='".$productData['category']."',`link`='".$productData['link']."',`spec1`='".$productData['feature1']."',`spec2`='".$productData['feature2']."',`spec3`='".$productData['feature3']."',`spec4`='".$productData['feature4']."' WHERE `title`='".$productData['title']."';";
			if(!mysqli_query($con,$query))
				echo "Update failed for Product ID ".$productData['pid']."\nQuery: ".$query."\n";
		}
		echo "Product ID ".$productData['pid']." executed.\n";
	}
?>
