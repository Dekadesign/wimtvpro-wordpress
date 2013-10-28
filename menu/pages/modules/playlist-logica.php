<?php
/**
 * Written by walter at 24/10/13
 */
function wimtvpro_getThumbs_playlist($list,$showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="",$playlist=FALSE) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wimtvpro_video';
	$replace_content = get_option("wp_replaceContentWimtv");
	$my_media= "";
	$response_st = "";
	$sql_where  = "  ";
	$videoList = explode (",",$list);
	if ($showtime)
		$sql_where  = "  state='showtime'";
	else
		if ($playlist)
			$sql_where  = "  1=2";
		else
			$sql_where  = "  1=1";
	if ($playlist) {
		for ($i=0;$i<count($videoList);$i++){
			if ($videoList[$i]!="")
				$sql_where .= "  OR contentidentifier='" . $videoList[$i] . "' ";
		}
		$sql_where = "AND (" . $sql_where . ")";  
	} 
	else {
		for ($i=0;$i<count($videoList);$i++){
			if ($videoList[$i]!="")
				$sql_where .= "  AND contentidentifier!='" . $videoList[$i] . "' ";
		}
		$sql_where = "AND (" . $sql_where . ")"; 
	}


 	$array_videos  = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE uid='" .  get_option("wp_userWimtv") . "' " . $sql_where);

	$array_videos_new_drupal = array();

	if ($playlist==TRUE) {

		for ($i=0;$i<count($videoList);$i++){
			foreach ($array_videos  as $record_new) {
				if ($videoList[$i] == $record_new->contentidentifier){
					array_push($array_videos_new_drupal, $record_new);	
				}
			}

		}
	} else {
		$array_videos_new_drupal = $array_videos;
	}

	//Select Showtime
	$param_st = get_option("wp_basePathWimtv") . "users/" . get_option("wp_userWimtv") . 	"/showtime?details=true";
	$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
	$ch_st = curl_init();
	curl_setopt($ch_st, CURLOPT_URL, $param_st);
	curl_setopt($ch_st, CURLOPT_VERBOSE, 0);
	curl_setopt($ch_st, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch_st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_st, CURLOPT_USERPWD, $credential);
	curl_setopt($ch_st, CURLOPT_SSL_VERIFYPEER, FALSE);
	$details_st  =curl_exec($ch_st);
	$arrayjson_st = json_decode( $details_st);
	$st_license = array();
	foreach ($arrayjson_st->items as $st){
		$st_license[$st->showtimeIdentifier] = $st->licenseType;
	}
	$position_new=1;
	//Select video with position
	if (count($array_videos_new_drupal )>0) {
		foreach ($array_videos_new_drupal  as $record_new) {
			if ($showtime) {
				if ((isset($st_license[$record_new->showtimeIdentifier])) && ($st_license[$record_new->showtimeIdentifier] !="PAYPERVIEW"))
					$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
			else {
				$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
		}
	}

	return $my_media;
}
?>