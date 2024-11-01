<?php
    switch ($_GET['task']) {
        case 'player_settings':
            unset($_GET['page']);
            unset($_GET['task']);
            unset($_GET['toolshot_player_act']);
            unset($_GET['nonce_func_post_toolshot_player']);
            if(isset($_GET['ads_code']))
                $_GET['ads_code'] = preg_replace("#[\r\n\t]{2,}#mis", '', $_GET['ads_code']);
            $success = 0;
            foreach($_GET as $key => $val){
                if (toolshot_class_table_update('toolshot_player', ['set' => 'value="'.$val.'"', 'where' => 'name = "'.$key.'"']))
                    $success++;
            }
            if($success!=0) $msg = 1;
            else $msg = 'Nothing change';
            echo '<msg>'.$msg.'</msg>';
            break;
        case 'metabox_get_filter':
            if (isset($_GET['get_filter']) && preg_match_all('#([^,]+)#mis', $_GET['get_filter'], $match, PREG_SET_ORDER)) {
                $source_player = [];
                foreach ($match as $val) array_push($source_player, $val[1]);
                toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($source_player).'\'', 'where' => 'name="source_player"']);
            }
            break;
        case 'metabox_get_search':
            if (isset($_GET['get_search']) && preg_match_all('#([^,]+)#mis', $_GET['get_search'], $match, PREG_SET_ORDER)) {
                $search_upload = [];
                foreach ($match as $val) array_push($search_upload, $val[1]);
                toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($search_upload).'\'', 'where' => 'name="search_upload"']);
            }
            break;
        case 'upload_post':
            if(preg_match('#&th_title=(.+?)&th_img=(.+?)&th_url=(.+?)$#mis', preg_replace('#&th_category.*?$#mis', '', $_SERVER['REQUEST_URI']), $match))
                upload_post(urldecode($match[1]), $match[2], $match[3], $_GET['th_category']);
            break;
        case 'upload_all':
            if(preg_match_all('#&th_title\[\]=(.+?)&th_img\[\]=(.+?)&th_url\[\]=(.+?)(?=&th_title|$)#mis', $_SERVER['REQUEST_URI'], $match, PREG_SET_ORDER)){
                foreach($match as $val)
                    upload_post(urldecode($val[1]), $val[2], $val[3], $_GET['th_category']);
            }
            echo '<msg>1</msg>';
            break;
        case 'add_category_upload':
            $toolshot_player['category_upload'] = json_decode($toolshot_player['category_upload']);
            if(!in_array($_GET['url'], $toolshot_player['category_upload']) && strlen(trim($_GET['url']))!=0)
                array_unshift($toolshot_player['category_upload'], $_GET['url']);
            toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($toolshot_player['category_upload']).'\'', 'where' => 'name="category_upload"']);
            echo '<msg>1</msg>';
            break;
        case 'remove_category_upload':
            $toolshot_player['category_upload'] = json_decode($toolshot_player['category_upload']);
            $toolshot_player['category_upload'] = array_diff($toolshot_player['category_upload'], [$_GET['url']]);
            if($toolshot_player['category_upload']==null) $toolshot_player['category_upload'] = [];
            toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($toolshot_player['category_upload']).'\'', 'where' => 'name="category_upload"']);
            echo '<msg>1</msg>';
            break;
		case 'get_image_video_via_url':
			$data = [];
			foreach($_GET['url'] as $val){
				$html = toolshot_class_curl_php($val);
				if(preg_match('#<(?=[^<>]*\bproperty="og:image")meta\b[^<>]*content=\"([^\"]*)\"#mis', $html, $match))
					array_push($data, ['url'=>$val, 'img'=>$match[1]]);
				else
					array_push($data, ['url'=>$val, 'img'=>$toolshot_player['image']]);				
			}
			echo '<msg>'.json_encode($data).'</msg>';
			break;
    }
    // upload post
    function upload_post($title, $img, $url, $category){
        $new_post = array(
            'post_title'    => $title,
            'post_content'  => '[toolshot_player img="'.$toolshot_player['img'].'" url="'.$url.'"]',
            'post_status'   => 'publish',
            'post_type'	  => 'post',
            'post_author'   => get_current_user_id(),
            'post_category' => $category,
        );
        $post_id = wp_insert_post($new_post);
        wp_set_post_terms($post_id, 'video', 'post_format');

        // download image file
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($img);
        $filename = basename(preg_replace('#\W#mis', '', $img).'-'.rand(0, 999999).'.jpg');
        if(wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'] . '/' . $filename;
        else $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
        $res2= set_post_thumbnail( $post_id, $attach_id );
        update_post_meta($post_id, '_toolshot_player_url', sanitize_text_field($url));
    }

    die();
