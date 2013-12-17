<?php

function wimtvpro_programming(){
	
	$view_page = wimtvpro_alert_reg();
	if (!$view_page){
		die();
	}
?>

	<div class='wrap'>
    <?php  echo  wimtvpro_link_help();
	
	$page = isset($_GET['namefunction']) ? $_GET['namefunction'] : "";
	switch ($page) {
		case "newProgramming" || "modProgramming":?>
            <h2>
            <?php 
			if ($page=="newProgramming"){
                _e("New Programming","wimtvpro");
				$nameProgramming = ""; 
			}
            else {
                _e("Modify Programming","wimtvpro"); 
				$progId = isset($_GET["progId"]) ? $_GET["progId"] : "";
            }
            ?>
    
            <a href='?page=WimVideoPro_Programming' class='add-new-h2'><?php echo __( 'Return to list', 'wimtvpro') ?></a></h2>
            
        
            <div id="progform">
                <form>
                    <label><?php _e("Give a name to this programming (not mandatory)","wimtvpro"); ?></label>
                    <input type="text" value="<?php echo $nameProgramming;?>" id="progname" />
                    <input type="submit" value="<?php _e("Send","wimtvpro");?>" class="button button-primary submitnow" />
                    <input type="submit" value="<?php _e("Skip","wimtvpro");?>" class="button submitnow" />
                </form>
            </div>
            <!-- calendar -->
            <div id="calendar"></div>
			
            <div style="display:none">
                <div class="embedded">
                    <h1>Codice Embed</h1>
                    <textarea id="progCode" onclick="this.focus(); this.select();"></textarea>
                </div>
			</div>            
		<?php
		break;

        
		default:
		
		if (isset($_GET["functionList"]) && ($_GET["functionList"]=="delete")){
			$idProgrammingDelete = isset($_GET["id"]) ? $_GET["id"] : "";
			$response = apiDeleteProgramming($idProgrammingDelete);
		}
		
	?>
        <h2> <?php _e("Programmings","wimtvpro");?> 
        <a href='<?php echo $_SERVER['REQUEST_URI'] . "&namefunction=newProgramming" ?>' class='add-new-h2'><?php echo __( 'New','wimtvpro' ) ?></a>
        </h2>
        
        <?php
		$response = apiGetProgrammings($idProgrammingDelete);
		$arrayjsonst = json_decode($response);
		?>
		<table id='tableLive' class='wp-list-table widefat fixed pages'>
		<thead>
        	<tr>
            <th><?php _e("Title","wimtvpro");?></th>
            <th><?php _e("Modify","wimtvpro");?></th>
            <th><?php _e("Remove");?></th>
            <th><?php _e("Embedded","wimtvpro");?></th>
            </tr>
         </thead>
		<tbody>
		
        <?php
		foreach ($arrayjsonst->programmings as $prog){
			if (!isset($prog->name) ) $titleProgramming = __("No title","eventissimo");
			else $titleProgramming = $prog->name;
			?>
            <tr>
				<td><?php echo $titleProgramming; ?></td>
				<td><a href='?page=WimVideoPro_Programming&namefunction=modifyProgramming&title=<?php echo $titleProgramming;?>&progId=<?php echo $prog->identifier;?>' alt='<?php _e("Modify","wimtvpro");?>' title='<?php _e("Modify","wimtvpro");?>'><img src='<?php echo get_option('wp_wimtvPluginPath');?>images/mod.png'  alt='<?php _e("Modify","wimtvpro");?>'></a>
          		</td>
				<td><a href='?page=WimVideoPro_Programming&functionList=delete&id=<?php echo $prog->identifier;?>' alt='<?php _e("Remove");?>' title='<?php _e("Remove");?>'><img src='<?php echo get_option('wp_wimtvPluginPath');?>images/remove.png'  alt='<?php _e("Remove");?>'></a>
                
                
                </td>
				<td>
              	 [wimprog id="<?php echo $prog->identifier;?>"]
                </td>
			</tr>
		<?php
        }
		echo "</tbody></table>";

		?>
        
	
<?php	
	}
	echo "</div>";
}

?>