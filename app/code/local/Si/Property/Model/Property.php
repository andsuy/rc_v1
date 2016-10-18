<?php
class Si_Property_Model_Property extends Mage_Core_Model_Abstract
{
    /* Get Property Collection */
    public function getPropertyCollection() {
        $propertyCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('type_id', array('eq' => 'virtual'))
                ->addAttributeToFilter('attribute_set_id', array('eq' => 9));
                //->addAttributeToFilter('status', array('eq' => 1));
        return $propertyCollection;
    }
    
    public function advanceSearch($data) {
        $state = "";
        $city = "";
        $country = "";
        $address = explode(",", $data["address"]);
        $amount = explode("-", $data["amount"]);
        $upperLimit = $data["upperLimitPrice"];
        $minval = $amount[0];
        $maxval = $amount[1];
        $add_count= count($address);
        if ($data["checkin"] != "") {
            $fromdate = date("Y-m-d", strtotime($data["checkin"]));
        }
        if ($data["checkout"] != "") {
            //$todate = date("Y-m-d", strtotime($data["checkout"]));
            $todateVal = date("Y-m-d", strtotime($data["checkout"]));
            $dateArr = explode("-", $todateVal);
            $todate = date('Y-m-d', mktime(0, 0, 0, $dateArr[1], $dateArr[2] + 1, $dateArr[0]));
        }
        
        if($data["address"] != '') {
        	$lat = '';
			$lnt = '';
			try {
				$q = $data["address"];
				$q = str_replace(" ", "+", $q);
				$geocodeURL = "http://maps.googleapis.com/maps/api/geocode/json?address=$q&sensor=false";
				$ch = curl_init($geocodeURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				if ($httpCode == 200) {
				  $geocode = json_decode($result);
				  $lat = $geocode->results[0]->geometry->location->lat;
				  $lnt = $geocode->results[0]->geometry->location->lng;
				  //echo 'Lat=' . $lat . ' || Lnt='. $lnt; exit;
				  $formatted_address = $geocode->results[0]->formatted_address;
				  $geo_status = $geocode->status;
				  $location_type = $geocode->results[0]->geometry->location_type;
				} else {
				 $geo_status = "HTTP_FAIL_$httpCode";
				}
			} catch (Exception $ex) {
			}
        }
        
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$table = $resource->getTableName('property/propertylocation');


        if(!empty($data["nelat"]) && !empty($data["nelng"]) && !empty($data["swlat"]) && !empty($data["swlng"])) {
			$query = "SELECT product_id FROM ".$table." WHERE latitude BETWEEN ".$data["swlat"]." AND ".$data["nelat"]." AND longitude BETWEEN ".$data["swlng"]." AND ". $data["nelng"];
			//echo $query; exit;
			$results = $readConnection->fetchAll($query);
			$propertyIds = array();
			if(count($results) > 0) {
				foreach($results as $_res) {
					$propertyIds[] = $_res['product_id'];
				}
			}
        } elseif($data["address"] != '') {
			$query = "SELECT product_id, ( 3959 * acos( cos( radians(". $lat .") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(". $lnt .") ) + sin( radians(". $lat .") ) * sin( radians( latitude ) ) ) ) AS distance FROM ".$table." HAVING distance < 20 ORDER BY distance";
			//echo $query; exit;
			$results = $readConnection->fetchAll($query);
			$propertyIds = array();
			if(count($results) > 0) {
				foreach($results as $_res) {
					$propertyIds[] = $_res['product_id'];
				}
			}
			//echo '<pre>'; print_r($results); echo '</pre>'; exit;
        }
		//echo '<pre>'; print_r($propertyIds); echo '</pre>'; exit;
			
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $collection->addFieldToFilter(array(array('attribute' => 'status', 'eq' => '1')));

        $collection->addFieldToFilter(array(array('attribute' => 'property_approved', 'eq' => '1')));
        $collection->addFieldToFilter('price', array('gteq' => $minval));
        $collection->setOrder('price', 'asc');

        $collection->addFieldToFilter('price', array('lteq' => $maxval));

        /*if (count($address) == 3) {
            $collection->addFieldToFilter(array(array('attribute' => 'city', 'in' => $address),
                array('attribute' => 'state', 'in' => $address),
                array('attribute' => 'country', 'in' => $address),
                array('attribute' => 'address', 'in' => $address),
            ));
        } else if (count($address) == 2) {
            $collection->addFieldToFilter(array(array('attribute' => 'city', 'in' => $address),
                array('attribute' => 'state', 'in' => $address),
                array('attribute' => 'country', 'in' => $address),
                array('attribute' => 'address', 'in' => $address),
            ));
        } else if ($add_count== 1) {
            $addressValue = $address[0];
            $collection->addFieldToFilter(array(array('attribute' => 'city', 'like' => "%" . $addressValue . "%"),
                array('attribute' => 'state', 'like' => "%" . $addressValue . "%"),
                array('attribute' => 'country', 'like' => "%" . $addressValue . "%"),
                array('attribute' => 'address', 'like' => "%" . $addressValue . "%"),
            ));
        }*/
        
        
        /*if(count($address) > 0) {
        	foreach($address as $_address) {
        		$collection->addFieldToFilter('search_keywords', array('like' => "%" . trim($_address) . "%"));
        	}
        }*/
        //echo $collection->getSelect(); exit;

        if(!empty($data["nelat"]) && !empty($data["nelng"]) && !empty($data["swlat"]) && !empty($data["swlng"])) {
        	$collection->addFieldToFilter('entity_id',array('in'=>$propertyIds));
        } elseif($data["address"] != '') {
        	if(!empty($propertyIds)) {
        		$collection->addFieldToFilter('entity_id',array('in'=>$propertyIds));
        	} else {
	        	foreach($address as $_address) {
	        		$collection->addFieldToFilter('search_keywords', array('like' => "%" . trim($_address) . "%"));
	        	}        	
        	}
        }
        
        if (trim($data["roomtypeval"]) != "") {
            $data["roomtypeval"] = explode(",", $data["roomtypeval"]);
            if (count($data["roomtypeval"]) > 0) {
                $collection->addFieldToFilter('property_type', array('in' => array($data["roomtypeval"])));
            }
        }
        if (trim($data["amenityVal"]) != "") {
            if (count($data["amenityVal"]) > 0) {
                $collection->addAttributeToFilter('amenities', array('like' => '%' . $data["amenityVal"] . '%'));
            }
        }
        
        if (trim($data["ratingVal"]) != "") {
        	$data["ratingVal"] = explode(",", $data["ratingVal"]);
        	if (count($data["ratingVal"]) > 0) {
	        	$up = 0;
	        	$low = 101;
	        	foreach($data["ratingVal"] as $_rat){
	        		if($_rat == 1) {
	        			if($low > 0) { $low = 0;}
	        			if($up < 30) { $up = 30;}
	        		} elseif($_rat == 2) {
	        			if($low > 30) { $low = 30;}
	        			if($up < 50) { $up = 50;}
	        		} elseif($_rat == 3) {
	        			if($low > 50) { $low = 50;}
	        			if($up < 70) { $up = 70;}
	        		} elseif($_rat == 4) {
	        			if($low > 70) { $low = 70;}
	        			if($up < 90) { $up = 90;}
	        		} elseif($_rat == 5) {
	        			if($low > 90) { $low = 90;}
	        			if($up < 101) { $up = 101;}
	        		}
	        	}
	        	
	        	$collection->getSelect()->where('SELECT AVG(percent_approved) as average_rating FROM rating_option_vote_aggregated WHERE  entity_pk_value = e.entity_id GROUP BY entity_pk_value having AVG(percent_approved) >= '. $low .' and AVG(percent_approved) < '. $up .'');
        		//echo $collection->getSelect(); exit;
        	}
        }
        
        if (trim($data["keywordsVal"]) != "") {
        	$data["keywordsVal"] = explode(",", $data["keywordsVal"]);
            if (count($data["keywordsVal"]) > 0) {
            	$filterOptions = array();
            	foreach($data["keywordsVal"] as $_key){
            		$filterOptions[] = array(
							        'attribute' => 'keywords',
							        'like'      => '%' . trim($_key) . '%',
							        );
            	}

            	$collection->addAttributeToFilter($filterOptions);
            }
        }
        
        if ($data["searchguest"] > 0) {
            $collection->addAttributeToFilter('accomodates',array('gteq' => (int)$data["searchguest"]) );
        }
            
            
        $productFilter = array();
        $count = 0;

        if(!empty($data["checkin"]) && !empty($data["checkout"])) {        
            if (count($collection)) {
                foreach ($collection as $_product) {
                	$block = Mage::helper('property')->checkavailAction($data["checkin"], $data["checkout"], $_product->getId());
                	//echo $_product->getId() . ' == ' . $block . '<br>';
                    if (!$block) {
                        $productFilter[$count] = $_product->getId();
                        $count++;
                    }
                }
            }
        }

        if (count($productFilter)) {
            $collection->addAttributeToFilter('entity_id', array('nin' => $productFilter));
        }

        $collection->setPage($data["pageno"], 10);
        //echo '<pre>'; print_r($collection->getData()); echo '</pre>'; exit;
        return $collection;
    }
    
    public function checkavailAction($productid, $from, $to) {
        $start = strtotime($from);
        $end = strtotime($to);
        $daysBetween = ceil(abs($end - $start) / 86400);
        
        $block = Mage::helper('property')->checkavailAction($from, $to, $productid);
        //echo $block; exit;
        if($block == 0) {
        	return 0;
        } else {
        	return 1;
        }
    }
    
    public function imageupload($FILES, $entity_id) {

        $mediagalleryId = Mage::helper('property')->getmediagallery(); //get attribute id for image
        $baseimageId = Mage::helper('property')->getbaseimage(); //get attribute id for base_image
        $smallimageId = Mage::helper('property')->getsmallimage(); //get attribute id for small_image
        $thumbimageId = Mage::helper('property')->getthumbimage(); //get attribute id for thumb_image

        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write'); //get write object
        $catalog_gallery_value_table = $resource->getTableName('catalog_product_entity_media_gallery_value');
        $catalog_gallery_table = $resource->getTableName('catalog_product_entity_media_gallery');

		//echo '<pre>'; print_r($FILES); echo '</pre>';
		//echo count($FILES['files']['name']);
		//exit;
		
		for($k=0; $k < count($FILES['files']['name']); $k++) {
			$PropertyImage = str_replace(' ', '_', strtolower($FILES['files']['name'][$k]));
			$PropertyImage = str_replace('(', '_', $PropertyImage);
			$PropertyImage = str_replace(')', '_', $PropertyImage);
			
			$splitextension = explode(".", $PropertyImage);
			$tempImageName = "";
			for ($i = 0; $i < count($splitextension) - 1; $i++) {
				$tempImageName .= $splitextension[$i];
			}
			$PropertyImage = $tempImageName . $entity_id . "_" . rand(0, 100000) . "." . $splitextension[count($splitextension) - 1];
			$magePath = str_split($PropertyImage);
			if ($magePath[1] == '') {
				$magePath[1] = '_';
			}
			$imagepath = $magePath[0] . '/' . $magePath[1] . '/' . $PropertyImage;
			
			//echo '<br>count- '. $k;
			//echo '<br>name- '.$FILES['files']['name'][$k];
			//echo '<br>path- '.$imagepath;
			//exit;
			
			if (isset($FILES['files']['name'][$k]) && $FILES['files']['name'][$k] != '') {
				$check = explode('/',$FILES['files']['type'][$k]);
				$size = $FILES['files']['size'][$k];
				$max_size = 1*1024*1024;
				if($check[0] == 'image' && $size <= $max_size){	
					try {
						//$uploader = new Varien_File_Uploader('files'); // creating the new object
						//$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); //allowed extensions for uploaded image
						//$uploader->setAllowRenameFiles(false);
						//$uploader->setFilesDispersion(false);
							
							//file path to store the property image
							$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . $magePath[0] . DS . $magePath[1] . DS;
							$pathThumbRoot = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . 'thumbs';
							$pathThumbRoot1 = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . 'thumbs' . DS . $magePath[0];
							$pathThumbRoot2 = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . 'thumbs' . DS . $magePath[0] . DS . $magePath[1] . DS;
							//$uploader->save($path, $PropertyImage);
							move_uploaded_file($FILES['files']['tmp_name'][$k], $path . $PropertyImage);
							
							$this->createThumbs($path, $pathThumb, $PropertyImage, $pathThumbRoot, $pathThumbRoot1, $pathThumbRoot2);

					} catch (Exception $e) {
						Mage::getSingleton('core/session')->addError($e->getMessage());
						return;
					}

					$write->query("Insert into $catalog_gallery_table (attribute_id,entity_id,value) values ($mediagalleryId,$entity_id,'" . $imagepath . "')");

				} else {
					Mage::getSingleton('core/session')->addError($this->__("Please upload valid image file of size upto 1mb."));
					return;
				}
			}
		}
		Mage::getSingleton('core/session')->addSuccess('Images Uploaded Successfully.');
		return;
    }
    
	
	
    public function removeImage($imageId, $entityId) {
        $resource = Mage::getSingleton('core/resource');
        $write = $resource->getConnection('core_write'); //get write object
        $catalog_product_entity_media_gallery = $resource->getTableName('catalog_product_entity_media_gallery');

        $selectResult = "SELECT `value` FROM $catalog_product_entity_media_gallery WHERE value_id='$imageId' LIMIT 1 ";
        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($selectResult);
        $imageLocation = $data[0]["value"];
        $deleteResult = $write->query("delete from $catalog_product_entity_media_gallery where value_id='$imageId' and entity_id='$entityId'  limit 1  ");
        if ($deleteResult) {
            if (is_file(Mage::getBaseDir() . DS . "media" . DS . "catalog" . DS . "product" . DS . $imageLocation)) {
                unlink(Mage::getBaseDir() . DS . "media" . DS . "catalog" . DS . "product" . DS . $imageLocation);
                unlink(Mage::getBaseDir() . DS . "media" . DS . "catalog" . DS . "product" . DS . "thumbs" . DS . $imageLocation);
            }
        }
        return;
    }
    
    public function albumupdate($post) {
        $path = $post['album_path'];
        $entity_id = $post['entity_id'];
        $storeId = Mage::app()->getStore()->getId();
        $product = Mage::getModel('catalog/product')->load($entity_id);
        $product->setStoreID($storeId);
        $product->setThumbnail($path)
                ->setImage($path)
                ->setSmallImage($path)
                ->setStatus(1)
                ->save();
               return;
    }

    public function createThumbs($pathToImages, $pathToThumbs, $fname, $pathThumbRoot, $pathThumbRoot1, $pathThumbRoot2) {
        $thumbWidth = 100;
        if (!is_dir($pathThumbRoot)) {
            mkdir($pathThumbRoot);
        }

        if (!is_dir($pathThumbRoot1)) {
            mkdir($pathThumbRoot1);
        }

        if (!is_dir($pathThumbRoot2)) {
            mkdir($pathThumbRoot2);
        }

        $dir = opendir($pathToImages);
        $info = pathinfo($pathToImages . $fname);
        if (strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg') {
            // load image and get image size
            $img = imagecreatefromjpeg("{$pathToImages}{$fname}");
        } elseif (strtolower($info['extension']) == 'png') {
            $img = imagecreatefrompng("{$pathToImages}{$fname}");
        } elseif (strtolower($info['extension']) == 'gif') {
            $img = imagecreatefromgif("{$pathToImages}{$fname}");
        }
        $width = imagesx($img);
        $height = imagesy($img);

        // calculate thumbnail size
        $new_width = $thumbWidth;
        $new_height = floor($height * ( $thumbWidth / $width ));

        // create a new temporary image
        $tmp_img = imagecreatetruecolor($new_width, $new_height);

        // copy and resize old image into new image
        imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        if (strtolower($info['extension']) == 'jpg' || strtolower($info['extension']) == 'jpeg') {
            // save thumbnail into a file
            imagejpeg($tmp_img, "{$pathThumbRoot2}{$fname}");
        } elseif (strtolower($info['extension']) == 'png') {
            imagepng($tmp_img, "{$pathThumbRoot2}{$fname}");
        } elseif (strtolower($info['extension']) == 'gif') {
            imagegif($tmp_img, "{$pathThumbRoot2}{$fname}");
        }
        //}
        // close the directory
        closedir($dir);
    }


    
    public function review($status, $reviewId) {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tPrefix = (string) Mage::getConfig()->getTablePrefix();
        $reviewtable = Mage::getSingleton('core/resource')->getTableName('review');
        // now $write is an instance of Zend_Db_Adapter_Abstract
        $write->query("update $reviewtable set status_id = '$status' where review_id = '$reviewId'  ");
        return $status;
    }

}