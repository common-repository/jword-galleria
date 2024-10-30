<?php
/*
Plugin Name: jWordGalleria Widget
Plugin URI: http://smfarookstudios.com/home/?page_id=21
Description: jWordGalleria Widget
Author: Rashid Farook
Version: 1.1.3
Fix Log:
- 1.1:upload directory path made dynamic by Rashid Farook
- 1.1.1:h1 tag theme issue fixed by Glenda Macawile
- 1.1.2:label updated, control panel added by Rashid Farook
- 1.1.3:"select album/folder" functionality added by Rashid Farook

Author URI: http://rashidfarook.wordpress.com
*/

/*utility methods*/
//function to display Folder tree
//NOTE: we do not display the prefix '/' in the listbox display to look nicer
function listFolders($basedir,$seldir){
	$dirPath = dir($basedir);
	while (($file = $dirPath->read()) !== false){
    	if ($file != "." && $file != "..") {
        	if (is_dir("$basedir/$file")) {
				$folders[$cnt]=$file; $cnt++;
				if($seldir == '/'.$file)
					echo '<option value="/'.$file.'" selected>'.$file.'</option>';
				else
					echo '<option value="/'.$file.'">'.$file.'</option>';
				$subdirPath = dir($basedir.'/'.$file);
				while (($file2 = $subdirPath->read()) !== false){
    				if ($file2 != "." && $file2 != "..") {
        				if (is_dir($basedir.'/'.$file.'/'.$file2)) {
							$folders[$cnt]=$file.'/'.$file2; $cnt++;
						if($seldir == '/'.$file.'/'.$file2)
							echo '<option value="/'.$file.'/'.$file2.'" selected>'.$file.'/'.$file2.'</option>';
						else
							echo '<option value="/'.$file.'/'.$file2.'">'.$file.'/'.$file2.'</option>';
						}
					}
				}
				$subdirPath->close();
			}
		}
	}
	$dirPath->close();
}

/* application methods*/
function WJGWidget($wjg_root_url,$wjg_imgurl,$wjg_imgpath){
	echo '
	<script src="'.$wjg_root_url.'/src/jquery-1.4.2.js"></script>
    <script src="'.$wjg_root_url.'/src/galleria.js"></script>
        <style>
            html,body{}
            .content{}
            h1{}
            #galleria{
                height:200px;
                background:#222;
                background:-moz-radial-gradient(center 45deg, circle closest-side, #222 40%, #000 100%);
                background:-webkit-gradient(
                    radial, center 50%, 20, center 50%, 250, from(#333), to(#000)
                )
            }
        </style>
    <div class="content">
        <div id="galleria">
        ';

	$dirPath = dir($wjg_imgpath);
	while (($file = $dirPath->read()) !== false){
  		if ((substr($file, -3)=="gif") || (substr($file, -3)=="jpg") || (substr($file, -3)=="png")){
     		echo '<img src="'.$wjg_imgurl.'/'.trim($file).'">';
  		}
	}
	$dirPath->close();

	echo '
        </div>
    </div>
    <script>
    Galleria.loadTheme(\''.$wjg_root_url.'/src/themes/dots/galleria.dots.js\');
    $(\'#galleria\').galleria();
    </script>
	';
}

function widget_myWJGWidget($args) {
	//initialize
	//get options
 	extract($args);
	$options = get_option("widget_myWJG");
  	if (!is_array( $options )){
		$options = array('title' => 'Photo Galleria', 'folder' => $uploads['subdir']);
  	}

	//set album properties
	$wjg_root_url=get_bloginfo('wpurl').'/wp-content/plugins/wjg';
	$uploads = wp_upload_dir();
	$wjg_folder = $options['folder'];
	$wjg_imgurl = ($uploads['baseurl'] . $wjg_folder);
	$wjg_imgpath = ($uploads['basedir'] . $wjg_folder);

	//print widget
	echo $before_widget;
  	echo $before_title.$options['title'].$after_title; //print title
  	WJGWidget($wjg_root_url,$wjg_imgurl,$wjg_imgpath); //print content
  	echo $after_widget;
}


function myWJG_control(){
	//get options
	$uploads = wp_upload_dir();
	$options = get_option("widget_myWJG");
  	if (!is_array( $options )){
		$options = array('title' => 'Photo Galleria', 'folder' => $uploads['subdir']);
  	}

	//update options
  	if ($_POST['myWJG-Submit']){
    	$options['title'] = htmlspecialchars($_POST['myWJG-WidgetTitle']);
    	$options['folder'] = htmlspecialchars($_POST['myWJG-WidgetFolder']);
    	update_option("widget_myWJG", $options);
  	}

	//generate controls
?>
	<p>
    <label for="myWJG-WidgetTitle">Title: </label>
    <input type="text" id="myWJG-WidgetTitle" name="myWJG-WidgetTitle" value="<?php echo $options['title'];?>" /><br/>
    <label for="myWJG-WidgetFolder">Folder: </label>
    <select id="myWJG-WidgetFolder" name="myWJG-WidgetFolder" size="1">
	<?php
		listFolders($uploads['basedir'],$options['folder']);
	?>
	</select>
    <input type="hidden" id="myWJG-Submit" name="myWJG-Submit" value="1" />
  	</p>
<?php
}


function myWJGWidget_init(){
	register_sidebar_widget(__('jWordGalleria'), 'widget_myWJGWidget');
	register_widget_control(   'jWordGalleria', 'myWJG_control', 300, 200 );
}

add_action("plugins_loaded", "myWJGWidget_init");

?>
