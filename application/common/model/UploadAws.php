<?php
namespace app\common\model;

use think\Model;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use think\Log;
use app\common\model\UploadAws;


class UploadAws extends Model
{
	/**
	 * 处理描述中的远程图片
	 * 
	 */
	 public function process_remote_images($products_descriptions){
		$cdnarr=explode('.',CDNDOMAIN);
		if(count($cdnarr)<2){
			return;
		}
		
	 	//$base_url = HTTPS_CATALOG_SERVER;
	 	$cdn = "/https:\/\/img\.".$cdnarr[1]."\.com/";
	 	
	 	$filepatten = "/http/";
	 	//$patten = "/" . $_SERVER ['HTTP_HOST'] . "/";
	 	if (preg_match_all ( "/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i", $products_descriptions, $arr )) {
	 		$images = $arr [2];
	 	
	 		foreach ( $images as $image ) {
	 			$image1 = html_entity_decode ( $image );
	 				
	 			if ((! preg_match ( $filepatten, $image1 )) || preg_match ( $cdn, $image1 )) {
	 				/* the picture is already on our server */
	 				continue;
	 			}
	 				
	 			$newfile = download_image ( $image1 );
	 			
	 			$key="images/" . $newfile;
	 			if ($newfile != null) {
	 	
	 				if (ENABLE_CDNFILE) {
	 					$bucket = CDNBUCKET;
	 					// $key=str_replace(DIR_FS_CATALOG, '', $dest_name);
	 					//$dest_name = DIR_FS_CATALOG . $key;
	 						
	 					$s3 = S3Client::factory ( array (
	 							'credentials' => array (
	 									'key' => AWSID,
	 									'secret' => AWSKEY
	 							),
	 							'region' => 'us-east-2',
	 							// 'signature' => 'v4',
	 							'version' => "2006-03-01"
	 					) );
	 					$dest_name=IMAGE_PATH.$newfile;
	 					if (file_exists ( $dest_name )) {
	 						
	 						try {
	 								
	 							// Upload data.
	 							$result = $s3->putObject ( array (
	 									'Bucket' => $bucket,
	 									'Key' => $key,
	 									'SourceFile' => $dest_name,
	 									'ACL' => 'public-read'
	 							) );
	 								
	 							if ($result ['ObjectURL']) { // 如果上传成功
	 								 
	 								// saveimagetobucket($key);
	 								chmod ( $dest_name, 0777 );
	 								unlink ( $dest_name );
	 								// $result['ObjectURL'];
	 								$new = CDNDOMAIN . "images/" . $newfile;
	 								$key = $new;
	 							}
	 						} catch ( S3Exception $e ) {
	 							//$time = time ();
	 							//$error_log = DIR_FS_CATALOG . 'logs/amazonuploadfile_' . $time . '.log'; // 设置错误日志
	 							// echo $e->getMessage() . "\n";
	 							Log::record( date ( 'Y-m-d H:i:s' ) . ' upload image error:' . $e->getMessage () . PHP_EOL);
	 						}
	 					}
	 				}
	 	
	 				$products_descriptions = str_ireplace ( $image, $key, $products_descriptions );
	 			}
	 		}
	 	}
	 	
	 	return $products_descriptions;
	 	 
	 }

	
}