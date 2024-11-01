<?php
add_action('add_meta_boxes', 'metabox_toolshot_player');
function metabox_toolshot_player(){
    add_meta_box('metabox_toolshot_player', 'ToolsHot Player', 'metabox_toolshot_player_output', 'post');
}

add_action('save_post', 'metabox_toolshot_player_multi_upload_save');
function metabox_toolshot_player_multi_upload_save($post_id){
    if(!isset($_POST['nonce_metabox_toolshot_player'])) return;
    if(!wp_verify_nonce($_POST['nonce_metabox_toolshot_player'], 'save_metabox_toolshot_player')) return;
    $content = get_post_field('post_content', $post_id);
    if(empty($content)) return;

    if(preg_match('#\[toolshot_player[^\]]+url="([^"]+)#mis', $content, $match))
        update_post_meta($post_id, '_toolshot_player_url', sanitize_text_field($match[1]));
}

function html_toolshot_player($url, $image, $player=null, $subtitle=''){
    if(empty($url)) return;
    global $url_toolshot_player, $toolshot_player;
    $id_player = 'toolshot_player_'.rand(0, 99999);
    if(empty($image)) $image = $toolshot_player['image'];

    $url_video = 'p_key='.$toolshot_player['key'].'&url='.urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($toolshot_player['key']), $url, MCRYPT_MODE_CBC, md5(md5($toolshot_player['key'])))));
    $html = '';
    $html .= '<div class="embed-responsive embed-responsive-16by9">';
    if($toolshot_player['player'] == 'toolshot' || $player!=null && $player=='toolshot'){
        $tmp = 'image='.$image.'&';
        foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player' || $key=='search_upload' || $key == 'image' || $key == 'category_upload'){}else $tmp .= $key.'='.$val.'&';
        $html .= '<iframe class="embed-responsive-item" src="'.$url_toolshot_player.'?'.$tmp.$url_video.$subtitle.'" allowfullscreen=""></iframe>';
    }else{
        wp_enqueue_style("toolshot_player_skin_css", plugins_url("../assets/css/skin-player/".$toolshot_player['skin'].".css", __FILE__), FALSE);
        wp_enqueue_script("jw_player", plugins_url("../assets/js/jwplayer.js", __FILE__), FALSE);
        wp_enqueue_script("toolshot_player_js", plugins_url("../assets/js/toolshot.player3.js", __FILE__), FALSE);
        $html .= '<div class="embed-responsive-item">
                        <div id="'.$id_player.'" class="toolshot_player_">Loading the player ...</div>
                    </div>
                    <div class="toolshot_logo_"></div>';
        if($toolshot_player['ads_show']=='show'){
            $html .= '<div id="toolshot_ads">
                    <a title="Close" class="toolshot_ads_close_" href="javascript:void(0);" onclick="document.getElementById(\'toolshot_ads\').outerHTML=\'\';">x</a>
                    '.$toolshot_player['ads_code'].'
                </div>';
        }
        $html .= '<script type="text/javascript">
                    toolshot_player_url_video_md5[\''.$id_player.'\'] = \''.md5($url).'\';
                    toolshot_player_url_video[\''.$id_player.'\'] = \''.$url_video.'\';
                    toolshot_player_image[\''.$id_player.'\'] = \''.$image.'\';
                    toolshot_player_subtitle[\''.$id_player.'\'] = \''.$subtitle.'\';
                </script>';
    }
    $html .= '</div><!--/.embed-responsive-->';
    return $html;
}

add_action( 'the_content', 'metabox_toolshot_show' );
function metabox_toolshot_show($content){
    global $post, $url_toolshot_player, $toolshot_player;
    if (is_single()){
        wp_enqueue_style("jw_player", plugins_url("../assets/css/toolshot.player.css", __FILE__), FALSE);
        if($toolshot_player['player'] != 'toolshot'){
            wp_enqueue_style("toolshot_player_skin_css", plugins_url("../assets/css/skin-player/".$toolshot_player['skin'].".css", __FILE__), FALSE);
            wp_enqueue_script("jw_player", plugins_url("../assets/js/jwplayer.js", __FILE__), FALSE);
            wp_enqueue_script("toolshot_player_js", plugins_url("../assets/js/toolshot.player3.js", __FILE__), FALSE);
        }
        $content = html_toolshot_player(get_post_meta($post->ID, '_th_player_url', true), get_post_meta($post->ID, '_th_player_image', true), '').$content;
    }
    $html = '
        <script type="text/javascript">
        var url_toolshot_player = \''.$url_toolshot_player.'\';
        var toolshot_player = {';
        foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player' || $key=='search_upload' || $key == 'category_upload'){}else $html .= $key.' : \''.$val.'\',';
    $html .= '};
        var toolshot_player_url_video_md5 = {};
        var toolshot_player_url_video = {};
        var toolshot_player_image = {};
        var toolshot_player_subtitle = {};
        </script>';

    return $html.$content;
}

add_shortcode( 'toolshot_player', 'shortcode_player_toolshot' );
function shortcode_player_toolshot($args){
    global $url_toolshot_player, $toolshot_player;
    $subtitle = '';
    if(isset($args['subtitle_file'], $args['subtitle_label'], $args['subtitle_default']))
        $subtitle = '&subtitle_file='.$args['subtitle_file'].'&subtitle_label='.$args['subtitle_label'].'&subtitle_default='.$args['subtitle_default'];

    wp_enqueue_style("jw_player", plugins_url("../assets/css/toolshot.player.css", __FILE__), FALSE);
    if($toolshot_player['player'] != 'toolshot'){
        wp_enqueue_style("toolshot_player_skin_css", plugins_url("../assets/css/skin-player/".$toolshot_player['skin'].".css", __FILE__), FALSE);
        wp_enqueue_script("jw_player", plugins_url("../assets/js/jwplayer.js", __FILE__), FALSE);
        wp_enqueue_script("toolshot_player_js", plugins_url("../assets/js/toolshot.player3.js", __FILE__), FALSE);
    }
    return html_toolshot_player($args['url'], $args['image'], $args['player'], $subtitle);
}

function metabox_toolshot_player_output($wp_post){
    global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_player;
    wp_nonce_field('save_metabox_toolshot_player', 'nonce_metabox_toolshot_player');
?>
    <style>
        hr{
            border:none;
            border-top:#ddd solid 1px;
        }
        ul.th_tab_{
            list-style: none;
            padding-left:10px;
            border-bottom:#E8E8E8 solid 1px;
        }
        ul.th_tab_ li{
            float:left;
            display: block;
            margin: 0 8px 0 0;
            line-height: 35px;
        }
        ul.th_tab_ li a{
            box-shadow: none;
            padding: 8px 10px;
            text-decoration: none;
            background: #F1F1F1;
            color: #555;
            border: #ccc solid 1px;
            border-bottom: #E8E8E8 solid 1px;
            font-size: 15px;
            font-weight: 700;
            line-height: inherit;
        }
        ul.th_tab_ li a.th_active_{
            background: #fff;
            border-bottom: #fff solid 1px;
            color:#000;
        }
        .clearfix:before, .clearfix:after {
            clear: both;
            content: " ";
            display: block;
            height: 0;
            visibility: hidden;
        }
        .label_{font-weight: bold;display: block;}
        #th_hand_upload hr{
            border:none;
            border-bottom: #E8E8E8 solid 1px;
            padding: 0.5em 0;
        }
        #th_hand_upload_source, #th_category_upload_source{word-break: break-all;}
        #th_hand_upload_source p, #th_category_upload_source p, #th_auto_upload_checkbox p, #th_multi_upload_checkbox p, #th_auto_upload_category p, #th_category_upload_category p{margin: 0.6em 0;}
        #th_hand_upload_source b, #th_auto_upload_checkbox b, #th_multi_upload_checkbox b, #th_auto_upload_category b,#th_category_upload_category b, #th_category_upload_source b{text-transform: capitalize;}
        #th_auto_upload_checkbox label, #th_multi_upload_checkbox label, #th_auto_upload_category label, #th_category_upload_category label{
            margin-left: 1.1em;
            line-height: 2em;
            display: inline-block;
        }
        #th_hand_upload_source a, #th_category_upload_source a{text-decoration: none;}
        .toolshot_add_shortcode_{
            text-align: right;
            float: right;
            line-height: 23px;
            margin-top: .5em;
        }
        #th_auto_upload, #th_multi_upload, #th_category_upload{display: none;}
        input[name*=th_][type=text]{width:100%;}
        .two_col_{
            margin-top:1em;
        }
        .two_col_ .item_{
            float:left;
            width:49%;
        }
        .two_col_ .item_:first-child{
            margin-right:2%;
        }
        .two_col_ .item_{text-align: center;}
        #metabox_toolshot_player .dashicons, .toolshot_add_shortcode_ .dashicons{line-height: 28px;}
        .two_col_ .item_ button, .two_col_ .item_ input[type=text]{margin-bottom:1em;}
        .two_col_ .item_ #output canvas{max-width:100%;}
        .embed-responsive {
            position: relative;
            display: block;
            height: 0;
            padding: 0;
            overflow: hidden;
        }
        .embed-responsive.embed-responsive-16by9 {
            padding-bottom: 56.25%;
            background: #f1f1f1;
        }
        .embed-responsive .embed-responsive-item, .embed-responsive iframe, .embed-responsive embed, .embed-responsive object {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .th_list_posts_{
            min-height:500px;
            margin:0 -5px;
        }
        .th_list_posts_ .item_{
            width:16.66%;
            float:left;
        }
        .th_list_posts_ .item_ a{
            text-decoration: none;
            color:rgba(68, 68, 68, 0.78);
        }
        .th_list_posts_ .item_ a:hover{
            color:#000;
        }
        .th_list_posts_ .item_ > div{
            padding:0 5px;
            position: relative;
        }
        .quick_upload_edit_ {
            position: absolute;
            top: 0;
            left: 5px;
            bottom: 0;
            right: 5px;
            background: rgba(0, 0, 0, 0.4);
            display: none;
        }
        .th_list_posts_ .item_ div:hover .quick_upload_edit_{
            display: block;
        }
        .quick_upload_edit_ a{
            width: 50%;
            height: 100%;
            float: left;
            text-align: center;
            cursor: pointer;
            color:#fff!important;
            font-weight: bold;
        }
        .quick_upload_edit_ a:hover{
            background: rgba(0, 0, 0, 0.8);
        }

        .th_list_posts_ .item_ a div.img_{
            height:150px;
            overflow: hidden;
        }
        .th_list_posts_ .item_ a div.img_ img{
            width:100%;
        }
        .th_list_posts_ .item_ p{
            margin-top: .3em;
            text-align: center;
            height: 3em;
            overflow: hidden;
        }
        #th_multi_upload .two_col_ .item_{
            text-align: left;
        }
        @media only screen and (max-width: 1700px) {
            .th_list_posts_ .item_{
                width: 20%;
            }
        }
        @media only screen and (max-width: 1500px) {
            .th_list_posts_ .item_{
                width: 25%;
            }
        }
        @media only screen and (max-width: 1390px) {
            .th_list_posts_ .item_{
                width: 33.33%;
            }
        }
        @media only screen and (max-width: 1040px) {
            .th_list_posts_ .item_{
                width: 50%;
            }
            #th_auto_upload_checkbox label, #th_multi_upload_checkbox label{
                line-height: 3em;
            }
        }
        /* category video */
        #metabox_toolshot_player .tagchecklist span, #metabox_toolshot_player .tagchecklist span a {
            display: block;
            float: left;
            overflow: hidden;
        }
        #metabox_toolshot_player .tagchecklist span {
            margin-right: 25px;
            font-size: 13px;
            line-height: 1.8em;
            cursor: default;
            max-width: 100%;
            text-overflow: ellipsis;
        }
        #metabox_toolshot_player .tagchecklist span a {
            margin: 1px 0 0 -17px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            text-indent: 0;
            position: absolute;
        }
        #metabox_toolshot_player .tagchecklist span a:before {
            background: 0 0;
            color: #b4b9be;
            content: "\f153";
            display: block;
            font: 400 16px/20px dashicons;
            speak: none;
            height: 20px;
            text-align: center;
            width: 20px;
            -webkit-font-smoothing: antialiased;
        }
        #metabox_toolshot_player .tagchecklist span a:hover:before{
            color:#c00;
        }
    </style>
    <ul class="th_tab_ clearfix">
        <li><a href="#" class="th_active_" data-upload="hand_upload" onclick="return th_change_tab(this, 'hand_upload');">Hand Upload</a></li>
        <li><a href="#" data-upload="multi_upload" onclick="return th_change_tab(this, 'multi_upload');">Search Video</a></li>
        <li><a href="#" data-upload="auto_upload" onclick="return th_change_tab(this, 'auto_upload');">New Video</a></li>
        <li><a href="#" data-upload="category_upload" onclick="return th_change_tab(this, 'category_upload');">Category Video</a></li>
        <li><a href="#" data-upload="about" onclick="return th_change_tab(this, 'about');">About & Tutorial</a></li>
    </ul>
    <div id="th_hand_upload">
        <div id="th_hand_upload_source">
        </div>
        <hr>
		<p>
			<label class="selectit"><input onchange="player_settings()" value="1" type="checkbox" name="post_auto_bbcode" id="post_auto_bbcode" <?=$toolshot_player['post_auto_bbcode']=='true'?'checked':''?>> Auto BBcode</label>&nbsp;&nbsp;&nbsp;
			<label class="selectit"><input onchange="player_settings()" value="1" type="checkbox" name="post_auto_image_video" id="post_auto_image_video" <?=$toolshot_player['post_auto_image_video']=='true'?'checked':''?>> Auto Image Video</label>	
        </p>
        <p><label class="label_" for="th_hand_upload_input_subtitle_url">Subtitles</label></p>
        <p>
            <input type="text" name="th_hand_upload_input_subtitle_url" id="th_hand_upload_input_subtitle_url" style="width:70%;" placeholder="Subtitle URL (.vtt, .srt, .dfxp)" onkeypress="return add_remove_subtitle({act:'add', e:event})">
            <input type="text" name="th_hand_upload_input_subtitle_label" id="th_hand_upload_input_subtitle_label" style="width:20%;" placeholder="Subtitle Label" onkeypress="return add_remove_subtitle({act:'add', e:event})">
            <input type="button" class="button" value="Add" onclick="return add_remove_subtitle({act:'add'})">
        </p>
        <div id="th_hand_upload_subtitle_list"></div>
        <?php $image = get_post_meta($wp_post->ID, '_th_player_image', true);?>
        <p><label class="label_" for="th_hand_upload_input_image">Image Video (Image Url or Empty)</label></p>
        <input type="text" name="th_hand_upload_input_image" id="th_hand_upload_input_image" value="<?=!empty($image) ? $image:$toolshot_player['image']?>">

        <p><label class="label_" for="th_hand_upload_input_url">Video URL</label></p>
        <input type="text" name="th_hand_upload_input_url" id="th_hand_upload_input_url" onclick="th_get_video(this, this.value)" onkeyup="th_get_video(this, this.value)" onchange="th_get_video(this, this.value)" value="<?=get_post_meta($wp_post->ID, '_th_player_url', true)?>">
        <div class="two_col_ clearfix">
            <div class="item_">
                <button class="button" type="button" onclick="shoot();"><span class="dashicons dashicons-camera"></span> Capture Video</button>
                <div class="embed-responsive embed-responsive-16by9"></div>
            </div><!--.item_-->
            <div class="item_">
                <button class="button" type="button" onclick="document.getElementById('set-post-thumbnail').click()"><span class="dashicons dashicons-format-image"></span> Set featured image</button>
                <div id="output"></div>
            </div>
        </div><!--.two_col_-->
        <div class="toolshot_add_shortcode_">
            <button class="button button-primary" type="button" onclick="toolshot_add_shortcode()"><span class="dashicons dashicons-plus"></span> Add ShortCode To Post</button>
        </div>
        <p><label class="label_" for="th_hand_upload_short_code_generate">Short Code Generate</label></p>
        <input type="text" name="th_hand_upload_short_code_generate" id="th_hand_upload_short_code_generate" value="">
        <div style="height:1px;clear: both;"></div>
    </div><!--#th_hand_upload-->
    <div id="th_multi_upload">
        <div id="th_multi_upload_checkbox">
        </div>
        <hr>
        <div id="th_auto_upload_category">
            <p>
                <b>category</b> :
                <?php foreach(get_categories(['hide_empty'=>0]) as $val)
                    echo '<label><input type="checkbox" value="'.$val->term_id.'" onclick="return set_arr_category(this, '.$val->term_id.');">'.$val->name.'</label>';
                ?>
            </p>
        </div>
        <div class="two_col_ clearfix" style="margin:0">
            <div class="item_">
                <p><label class="label_" for="th_multi_upload_input_key">Keyword</label></p>
                <input type="text" name="th_multi_upload_input_key" id="th_multi_upload_input_key" onkeypress="return get_search(this, event, 'enter');">
            </div><!--.item_-->
            <div class="item_">
                <p><label class="label_">&nbsp;</label></p>
                <button class="button" type="button" onclick="return get_search(this, this.value, 'input');"><span class="dashicons dashicons-search"></span> Search</button>
                <button style="float:right;" class="button button-primary" type="button" onclick="return upload_all(this, 'th_multi_upload_list_posts');">Upload All</button>
            </div>
        </div><!--.two_col_-->
        <hr>
        <div id="th_multi_upload_list_posts" class="clearfix th_list_posts_">
            <div></div>
        </div><!--#th_multi_upload_list_posts-->
    </div><!--#th_multi_upload-->

    <div id="th_auto_upload">
        <div id="th_auto_upload_checkbox">
        </div>
        <hr>
        <div id="th_auto_upload_category">
            <p>
                <b>category</b> :
                <?php foreach(get_categories(['hide_empty'=>0]) as $val)
                    echo '<label><input type="checkbox" value="'.$val->term_id.'" onclick="return set_arr_category(this, '.$val->term_id.');">'.$val->name.'</label>';
                ?>
            </p>
            <p><button style="float:right;" class="button button-primary" type="button" onclick="return upload_all(this, 'th_auto_upload_list_posts');">Upload All</button></p>
            <div style="height: 1px;clear: both;"></div>
        </div>
        <hr>
        <div id="th_auto_upload_list_posts" class="clearfix th_list_posts_">
            <div></div>
        </div><!--#th_auto_upload_list_posts-->
        <?php wp_nonce_field('save_func_post_toolshot_player', 'nonce_func_post_toolshot_player');?>
    </div><!--#th_auto_upload-->

    <div id="th_category_upload">
        <div id="th_category_upload_source">
        </div>
        <hr>
        <p><label class="label_" for="th_category_upload_input">URL Category</label></p>
        <p>
            <input type="text" name="th_category_upload_input" id="th_category_upload_input" style="width:90%;" onkeydown="return add_category_upload(event)">
            <input type="button" class="button" value="Add" onclick="return add_category_upload()">
        </p>
        <div class="tagchecklist" id="th_category_upload_list">
            <script>
                var arr_category_upload = [];
            </script>
            <?php foreach(json_decode($toolshot_player['category_upload']) as $val){?>
                <span><a class="ntdelbutton" tabindex="0" onclick="return remove_category_upload(this, '<?=$val?>')">X</a>&nbsp;<?=$val?></span>
                <script>arr_category_upload.push('<?=$val?>');</script>
            <?php }?>
        </div><!--#th_category_upload_list-->
        <hr>
        <div id="th_category_upload_category">
            <p>
                <b>category</b> :
                <?php foreach(get_categories(['hide_empty'=>0]) as $val)
                    echo '<label><input type="checkbox" value="'.$val->term_id.'" onclick="return set_arr_category(this, '.$val->term_id.');">'.$val->name.'</label>';
                ?>
            </p>
            <p><button style="float:right;" class="button button-primary" type="button" onclick="return upload_all(this, 'th_category_upload_list_posts');">Upload All</button></p>
            <div style="height: 1px;clear: both;"></div>
        </div>
        <hr>
        <div id="th_category_upload_list_posts" class="clearfix th_list_posts_">
            <div></div>
        </div><!--#th_category_upload_list_posts-->
    </div><!--#th_category_upload-->

    <div id="th_about" style="display: none;">
        <div class="two_col_ clearfix">
            <div class="item_" style="text-align: left;">
                <p>This plugin is 100% free. Thank you for supporting those days I believed that this plugin will be well known and trusted ...</p>
                <p>After many suggestions update your sincerity, we have built is almost complete ...</p>
                <p>If you find this plugin useful and help you, in the possibility of support for our budget so that we are motivated to develop and regularly update plugins, plugin make better and contribute your success on it? Thank you!</p>
                <hr>
                <p>
                    <b>Facebook Support</b> : <a href="https://www.facebook.com/ad.toolshot" target="_blank">https://www.facebook.com/ad.toolshot</a>
                </p>
                <p>
                    <b>Email Support</b> : ad.toolshot@gmail.com
                </p>
                <p>
                    <b>List Source Support</b> : <a href="http://toolshot.com/player?tab=server-support" target="_blank">http://toolshot.com/player?tab=server-support</a>
                </p>
                <p>
                    <a style="float:right" class="button button-primary" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M5FPZRYSV45QE" target="_blank">Donate To Better</a>
                </p>
            </div>
            <div class="item_" style="text-align: left;">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" width="640" height="360" src="https://www.youtube.com/watch?v=PCB1kMs371s" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div><!--.two_col_-->
    </div><!--#th_about-->
    <script>
        var url_toolshot_player = '<?=$url_toolshot_player?>';
        var http = new XMLHttpRequest();
        var tmp_change_tab = false;
        var tmp_change_tab_category = false;
        var _th_player_url = [];
        var get_source_support = {};
        var arr_category = [];
        var arr_upload_all = [];
        var arr_toolshot_player_url = [];
        var arr_th_hand_upload_subtitle = {};
		var re_add_bbcode = '';
        <?php foreach(toolshot_class_table_select('postmeta', ['select' => 'meta_value', 'where'=>'meta_key="_toolshot_player_url" and meta_value!=""', 'group by'=>'meta_value']) as $val){?>
            _th_player_url.push('<?=$val->meta_value?>');
        <?php }?>
        // th_change_tab
        function th_change_tab(thiss, val){
            arr_category = [];
            elements = document.getElementById('th_auto_upload_category').querySelectorAll('input[type=checkbox]');
            for(i=0;i<elements.length;i++) elements[i].checked = false;

            document.getElementsByClassName('th_active_')[0].setAttribute('class', '');
            thiss.setAttribute('class', 'th_active_');
            document.getElementById('th_hand_upload').setAttribute('style', 'display:none');
            document.getElementById('th_auto_upload').setAttribute('style', 'display:none');
            document.getElementById('th_multi_upload').setAttribute('style', 'display:none');
            document.getElementById('th_category_upload').setAttribute('style', 'display:none');
            document.getElementById('th_about').setAttribute('style', 'display:none');
            if(val=='auto_upload'){
                document.getElementById('th_auto_upload').setAttribute('style', 'display:block');
                if(!tmp_change_tab) get_filter();
                tmp_change_tab = true;
            }else if(val=='category_upload'){
                document.getElementById('th_'+val).setAttribute('style', 'display:block');
                if(!tmp_change_tab_category) get_category();
                tmp_change_tab_category = true;
            }else{
                document.getElementById('th_'+val).setAttribute('style', 'display:block');
            }
            return false;
        }
        // get source support
        var source_player = [<?php foreach (json_decode($toolshot_player['source_player']) as $val) echo '"'.$val.'",';?>];
        var multi_upload = [<?php foreach (json_decode($toolshot_player['search_upload']) as $val) echo '"'.$val.'",';?>];
        http.open("GET", url_toolshot_player+'get_source_support', true);
        http.onreadystatechange = function(){
            obj = JSON.parse(http.responseText);
            get_source_support = obj;
            th_hand_upload_source = '';
            th_auto_upload_checkbox = '';
            th_multi_upload_checkbox = '';
            th_category_upload_source = '';
            for (key in obj){
                th_hand_upload_source += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                if(key!='iframe_only'){
                    th_auto_upload_checkbox += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                    th_multi_upload_checkbox += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                    th_category_upload_source += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                }
                for(key1 in obj[key]){
					re_add_bbcode += key1.replace(/(\W)/gim, '\\\$1')+'|';
                    th_hand_upload_source += '<a href="'+obj[key][key1]['_link']+'" target="_blank">'+key1+'</a>,&nbsp;&nbsp;';
                    if(typeof obj[key][key1]['_filter'] != 'undefined'){
                        th_auto_upload_checkbox += '<label><input ';
                        if(source_player.indexOf(obj[key][key1]['_filter'])>=0) th_auto_upload_checkbox += 'checked ';
                        th_auto_upload_checkbox += 'type="checkbox" class="get_filter_" value="'+obj[key][key1]['_filter']+'" onclick="get_filter(this, this.value)">'+key1+'</label>';
                    }
                    if(typeof obj[key][key1]['_search'] != 'undefined' && obj[key][key1]['_search'] != ''){

                        th_multi_upload_checkbox += '<label><input ';
                        if(multi_upload.indexOf(obj[key][key1]['_search'])>=0) th_multi_upload_checkbox += 'checked ';
                        th_multi_upload_checkbox += 'type="checkbox" class="get_search_" value="'+obj[key][key1]['_search']+'" onclick="get_search(this, this.value, \'checkbox\')">'+key1+'</label>';
                    }
                    if(typeof obj[key][key1]['_filter'] != 'undefined' || typeof obj[key][key1]['_search'] != 'undefined' && obj[key][key1]['_search'] != ''){
                        th_category_upload_source += '<a href="'+obj[key][key1]['_link']+'" target="_blank">'+key1+'</a>,&nbsp;&nbsp;';
                    }
                }
                th_hand_upload_source += '</p>';
                th_auto_upload_checkbox += '</p>';
                th_multi_upload_checkbox += '</p>';
                th_category_upload_source += '</p>';
            }
            document.getElementById('th_hand_upload_source').innerHTML = th_hand_upload_source;
            document.getElementById('th_auto_upload_checkbox').innerHTML = th_auto_upload_checkbox;
            document.getElementById('th_multi_upload_checkbox').innerHTML = th_multi_upload_checkbox;
            document.getElementById('th_category_upload_source').innerHTML = th_category_upload_source;
        }
        http.send(null);
        // get video
        var th_url_video = '';
        function th_get_video(thiss, val){
            if(typeof val != 'undefined' && th_url_video != val.trim() && val.match(/^https?:\/\//gim)){
                th_url_video = val.trim();
                http.open("GET", url_toolshot_player+"json_video?url="+th_url_video, true);
                http.onreadystatechange = function(){
                    obj = eval(http.responseText);
                    document.getElementsByClassName('embed-responsive')[0].innerHTML = '<video poster="'+document.getElementById('th_hand_upload_input_image').value+'" id="my-video_html5_api" autoplay controls class="embed-responsive-item"><source src="'+obj[0]['file'].replace(/\"/gim, '&#34;')+'" type="video/mp4"></video>';
                    /* delete cache */
                    for(var i = 0; i<obj.length; i++)
                        if(typeof obj[i]['cache']!= 'undefined')
                            cache = obj[i]['cache'];
                    document.getElementById('my-video_html5_api').addEventListener('error', function(event) {
                        http.open("GET", url_toolshot_player+"main/delete_cache?url="+cache, true);
                        http.onreadystatechange = function(){
                            if(http.responseText==1)
                                return th_get_video(thiss, val);
                        }
                        http.send(null);
                    }, true);
                };
                http.send(null);
            }
        }
        // toolshot_add_shortcode
        function toolshot_add_shortcode(){
            if(document.getElementById('th_hand_upload_input_url').value=='') alert('Error url video not empty');
            else{
                url = document.getElementById('th_hand_upload_input_url').value;
                html = '[toolshot_player image="'+document.getElementById('th_hand_upload_input_image').value+'" url="'+url+'"';
                for(var key in get_source_support['iframe_only']) {
                    var regex = new RegExp(key.replace(/\./g, '\\.'), 'gim');
                    if(url.match(regex)) html += ' player="toolshot"';
                }
                subtitle_file = '';
                subtitle_label = '';
                subtitle_default = '';
                for (var key in arr_th_hand_upload_subtitle){
                    subtitle_file += arr_th_hand_upload_subtitle[key]['file']+',|,';
                    subtitle_label += arr_th_hand_upload_subtitle[key]['label']+',|,';
                    subtitle_default += (arr_th_hand_upload_subtitle[key]['default']?'true':'false')+',|,';
                }
                document.getElementById('th_hand_upload_subtitle_list').innerHTML = '';
                arr_th_hand_upload_subtitle = {};

                html += (subtitle_file?' subtitle_file="'+subtitle_file+'"':'')+(subtitle_label?' subtitle_label="'+subtitle_label+'"':'')+(subtitle_default?' subtitle_default="'+subtitle_default+'"':'')+']';
                document.getElementById('th_hand_upload_short_code_generate').value = html;
                tinyMCE.activeEditor.selection.setContent(html);
                tinyMCE.activeEditor.focus();
                document.getElementById('th_hand_upload_input_image').value = '<?=$toolshot_player['image']?>';
                document.getElementById('th_hand_upload_input_url').value = '';
                document.getElementsByClassName('embed-responsive')[0].innerHTML = '';
                window.scrollTo(0, 0);
            }
        }
        // capture video
        var videoId = 'my-video_html5_api';
        var canvas = '';
        function capture(video) {
            var w = video.videoWidth;
            var h = video.videoHeight;
            var canvas = document.createElement('canvas');
            canvas.width  = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, w, h);
            return canvas;
        }
        function shoot(){
            var video  = document.getElementById(videoId);
            var output = document.getElementById('output');
            canvas = capture(video);
            output.innerHTML  = '';
            output.appendChild(canvas);
            var canvas = document.getElementsByTagName('canvas');
        }
        // get filter
        function get_filter(thiss, val){
            var arr = [], i = 0;
            var elements = document.getElementsByClassName('get_filter_');
            for(var i = 0; i < elements.length; i++){
                if(elements[i].checked) arr.push(elements[i].value);
            }
            params = 'nonce_func_post_toolshot_player='+document.getElementById('nonce_func_post_toolshot_player').value+'&task=metabox_get_filter&get_filter='+arr;
            http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
            http.onreadystatechange = function(){}
            http.send(null);
            if(typeof thiss == 'undefined' && typeof val == 'undefined'){
                arr_upload_all = [];
                if(arr.length>0) get_list_posts('th_auto_upload_list_posts', arr[arr.length-1], arr, arr.length-1);
            }else{
                if(thiss.checked){
                    get_list_posts('th_auto_upload_list_posts', val);
                }else{
                    var reg = new RegExp('<data data-url="'+val.replace(/(\W)/gim, '\\$1')+'.+?</data>', "gim");
                    document.getElementById('th_auto_upload_list_posts').innerHTML = document.getElementById('th_auto_upload_list_posts').innerHTML.replace(reg, '');
                }
            }
        }
        // get search
        function get_search(thiss, val, type){
            var arr = [], i = 0;
            var elements = document.getElementsByClassName('get_search_');
            var th_multi_upload_input_key = document.getElementById('th_multi_upload_input_key').value;
            for(var i = 0; i < elements.length; i++)
                if(elements[i].checked) arr.push(elements[i].value+th_multi_upload_input_key);
            if(type=='checkbox') {
                params = 'nonce_func_post_toolshot_player=' + document.getElementById('nonce_func_post_toolshot_player').value + '&task=metabox_get_search&get_search=' + arr;
                http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&' + params, true);
                http.onreadystatechange = function () {}
                http.send(null);
            }
            if(th_multi_upload_input_key != ''){
                if(type=='checkbox'){
                    if(thiss.checked){
                        get_list_posts('th_multi_upload_list_posts', val+th_multi_upload_input_key);
                    }else{
                        var reg = new RegExp('<data data-url="'+val.replace(/(\W)/gim, '\\$1')+'.+?</data>', "gim");
                        document.getElementById('th_multi_upload_list_posts').innerHTML = document.getElementById('th_multi_upload_list_posts').innerHTML.replace(reg, '');
                    }
                }else{
                    if(type=='enter' && val.keyCode!=13) return true;
                    if(arr.length>0){
                        arr_upload_all = [];
                        document.getElementById('th_multi_upload_list_posts').innerHTML = '<div></div>';
                        get_list_posts('th_multi_upload_list_posts', arr[arr.length-1], arr, arr.length-1);
                    }
                    /*
                    for(var i = 0; i < elements.length; i++)
                        if(elements[i].checked) get_list_posts('th_multi_upload_list_posts', elements[i].value+th_multi_upload_input_key);
                    */
                    return false;
                }
            }
            //return false;
        }
        // get category
        function get_category(act, val){
            var arr = [];
            if(typeof act == 'undefined'){
                arr = arr_category_upload;
                arr_upload_all = [];
                if(arr.length>0) get_list_posts('th_category_upload_list_posts', arr[arr.length-1], arr, arr.length-1);
            }else if(act=='remove'){
                var reg = new RegExp('<data data-url="'+val.replace(/(\W)/gim, '\\$1')+'.+?</data>', "gim");
                document.getElementById('th_category_upload_list_posts').innerHTML = document.getElementById('th_category_upload_list_posts').innerHTML.replace(reg, '');
            }else if(act=='add'){
                get_list_posts('th_category_upload_list_posts', val);
            }
        }
        // get list posts
        function get_list_posts(element_id, data_url, arr, i){
            if(typeof i != 'undefined' && i<0) return;

            th_auto_upload_list_posts = document.createElement('data');
            th_auto_upload_list_posts.setAttribute('data-url', data_url);
            http = new XMLHttpRequest();
            http.open('GET', url_toolshot_player+'filter?url='+data_url, true);
            http.onreadystatechange = function(){
                obj = JSON.parse(http.responseText);
                tmp = '';
                for(key in obj)
                    if(_th_player_url.indexOf(obj[key]['url'])==-1){
                        tmp += '<div class="item_"><div><a href="'+obj[key]['url']+'"><div class="img_"><img src="'+obj[key]['img']+'"></div><p>'+obj[key]['title']+'</p></a><div class="quick_upload_edit_"><a onclick="return quick_upload_edit({method:\'upload\', title:\''+obj[key]['title']+'\', img:\''+obj[key]['img']+'\', url:\''+obj[key]['url']+'\', thiss:this});" href="'+obj[key]['url']+'"><br><br><br><br><span class="dashicons dashicons-plus"></span></span>Upload</a><a href="'+obj[key]['url']+'" onclick="return quick_upload_edit({method:\'edit\', title:\''+obj[key]['title']+'\', img:\''+obj[key]['img']+'\', url:\''+obj[key]['url']+'\', thiss:this});"><br><br><br><br><span class="dashicons dashicons-edit"></span>Edit</a></div></div></div>';
                        arr_upload_all.push({title:obj[key]['title'].replace(/\'/gim, ''), img:obj[key]['img'], url:obj[key]['url']});
                    }
                th_auto_upload_list_posts.innerHTML = tmp;
                //console.log(th_auto_upload_list_posts);
                document.getElementById(element_id).insertBefore(th_auto_upload_list_posts, document.getElementById(element_id).childNodes[0]);
                if(typeof i!= 'undefined'){
                    i--;
                    return get_list_posts(element_id, arr[i], arr, i);
                }
            };
            http.send(null);
        }
        //quick_upload_edit
        function quick_upload_edit(obj){
            if(obj['method']=='edit'){
                document.querySelectorAll('[data-upload="hand_upload"]')[0].click();
                document.getElementById('th_hand_upload_input_url').value = obj['url'];
                th_get_video(document.getElementById('th_hand_upload_input_url'), document.getElementById('th_hand_upload_input_url').value);
                obj['thiss'].parentElement.parentElement.setAttribute('style', 'opacity:0.3');
            }else{
                tmp_category = '';
                for (i = 0; i < arr_category.length; i++) tmp_category += '&th_category[]='+arr_category[i];
                params = 'task=upload_post&th_title='+obj['title']+'&th_img='+obj['img']+'&th_url='+obj['url']+tmp_category;
                http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
                http.onreadystatechange = function(){};
                http.send(null);
                obj['thiss'].parentElement.parentElement.setAttribute('style', 'display:none');
            }
            return false;
        }
        // upload_all
        function upload_all(thiss, element_id){
            thiss.setAttribute('disabled', '');
            if(arr_upload_all.length==0) return false;
            tmp_category = '';
            for(i = 0; i < arr_category.length; i++) tmp_category += '&th_category[]='+arr_category[i];
            tmp = '';
            arr_tmp = arr_upload_all;
            for(i=0; i < arr_tmp.length; i++)
                if(tmp.length<1745){
                    tmp += '&th_title[]='+arr_tmp[i]['title']+'&th_img[]='+arr_tmp[i]['img']+'&th_url[]='+arr_tmp[i]['url'];
                    arr_upload_all.splice(i, 1);
                    document.getElementById(element_id).getElementsByClassName('item_')[i].remove();
                }
            params = 'task=upload_all'+tmp_category+tmp;
            //console.log(encodeURI(params));
            http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
            http.onreadystatechange = function(){
                if(/<msg>1<\/msg>/g.exec(http.responseText))
                    return upload_all(thiss, element_id);
            };
            http.send(null);
            return false;
        }
        // set_arr_category
        function set_arr_category(thiss, id){
            if(thiss.checked) arr_category.push(id);
            else if(arr_category.indexOf(id) > -1)
                    arr_category.splice(arr_category.indexOf(id), 1);
            return true;
        }
        // add_category_upload
        function add_category_upload(e){
            if(typeof e != 'undefined' && e.keyCode == 13 || typeof e == 'undefined'){
                params = 'task=add_category_upload&url='+document.getElementById('th_category_upload_input').value;
                http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
                http.onreadystatechange = function(){};
                http.send(null);
                arr_category_upload.unshift(document.getElementById('th_category_upload_input').value);
                get_category('add', document.getElementById('th_category_upload_input').value);
                document.getElementById('th_category_upload_list').innerHTML = '<span><a class="ntdelbutton" tabindex="0" onclick="return remove_category_upload(this, \''+document.getElementById('th_category_upload_input').value+'\')">X</a>&nbsp;'+document.getElementById('th_category_upload_input').value+'</span>'+document.getElementById('th_category_upload_list').innerHTML;
                document.getElementById('th_category_upload_input').value = '';
                return false;
            }
        }
        // remove_category_upload
        function remove_category_upload(thiss, url){
            params = 'task=remove_category_upload&url='+url;
            http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
            get_category('remove', url);
            http.onreadystatechange = function(){
                if(/<msg>1<\/msg>/g.exec(http.responseText)){
                    thiss.parentNode.setAttribute('style', 'display:none');
                    arr_category_upload.splice(arr_category_upload.indexOf(url), 1);
                }
            };
            http.send(null);
            return false;
        }
        // add remove subtitle
        function add_remove_subtitle(obj){
            if(obj['act'] == 'add' && document.getElementById('th_hand_upload_input_subtitle_label').value.length>0 && document.getElementById('th_hand_upload_input_subtitle_url').value.length>0){
                if(typeof obj['e'] == 'undefined' || typeof obj['e'] != 'undefined' && obj['e'].keyCode == 13){
                    if(!document.getElementById('th_hand_upload_input_subtitle_url').value.match(/^https?:\/\/.+?\.(vtt|srt|dfxp)$/gim)){
                        alert('Subtitle file not valid');
                        return false;
                    }
                    if(Object.keys(arr_th_hand_upload_subtitle).length == 0)
                        arr_th_hand_upload_subtitle[document.getElementById('th_hand_upload_input_subtitle_label').value] = {file:document.getElementById('th_hand_upload_input_subtitle_url').value, label:document.getElementById('th_hand_upload_input_subtitle_label').value, default:true};
                    else
                        arr_th_hand_upload_subtitle[document.getElementById('th_hand_upload_input_subtitle_label').value] = {file:document.getElementById('th_hand_upload_input_subtitle_url').value, label:document.getElementById('th_hand_upload_input_subtitle_label').value, default:false};
                    html = '<p><input type="text" style="width:70%;" value="'+document.getElementById('th_hand_upload_input_subtitle_url').value+'" disabled>&nbsp;<input type="text" style="width:20%;" value="'+document.getElementById('th_hand_upload_input_subtitle_label').value+'" disabled>&nbsp;<button title="Set Default" type="button" class="arr_th_hand_upload_subtitle_label_ button '+(arr_th_hand_upload_subtitle[document.getElementById('th_hand_upload_input_subtitle_label').value]['default']?'button-primary':'')+'" onclick="return add_remove_subtitle({act:\'default\', thiss:this, label:\''+document.getElementById('th_hand_upload_input_subtitle_label').value+'\'})"><span class="dashicons dashicons-yes"></span></button>&nbsp;<button title="Remove Subtitle" type="button" class="button" onclick="return add_remove_subtitle({act:\'remove\', thiss:this, label:\''+document.getElementById('th_hand_upload_input_subtitle_label').value+'\'})"><span class="dashicons dashicons-no-alt"></span></button></p>';
                    document.getElementById('th_hand_upload_subtitle_list').innerHTML = html + document.getElementById('th_hand_upload_subtitle_list').innerHTML;
                    document.getElementById('th_hand_upload_input_subtitle_label').value = '';
                    document.getElementById('th_hand_upload_input_subtitle_url').value = '';
                }
            }
            if(obj['act'] == 'remove'){
                obj['thiss'].parentNode.setAttribute('style', 'display:none');
                delete arr_th_hand_upload_subtitle[obj['label']];
            }
            if(obj['act'] == 'default'){
                var tmp_class = document.getElementsByClassName('arr_th_hand_upload_subtitle_label_');
                for (var i = 0; i < tmp_class.length; ++i) {
                    var item = tmp_class[i];
                    item.className = 'arr_th_hand_upload_subtitle_label_ button';
                }
                obj['thiss'].className = 'arr_th_hand_upload_subtitle_label_ button button-primary';

                for (var key in arr_th_hand_upload_subtitle)
                    arr_th_hand_upload_subtitle[key]['default'] = false;

                arr_th_hand_upload_subtitle[obj['label']]['default'] = true;
            }
            if(typeof obj['e'] != 'undefined' && obj['e'].keyCode != 13) return true;
            //console.log(arr_th_hand_upload_subtitle);
            return false;
        }
		// add bbcode
		setInterval(function(){
			if(document.getElementById('post_auto_bbcode').checked){
				html = tinyMCE.activeEditor.getContent();
				html1 = html;
				re = new RegExp('([^"])(https?:\/\/(www\.|web\.|mobile\.|m\.)?('+re_add_bbcode.replace(/\|$/gim, '')+')[^ <]+)', 'gim');
				html = html.replace(re, '$1[toolshot_player image="'+(document.getElementById('post_auto_image_video').checked?'':document.getElementById('th_hand_upload_input_image').value)+'" url="$2"]');
				if(html!=html1){
					tinyMCE.activeEditor.setContent(html);
					if(document.getElementById('post_auto_image_video').checked) get_image_video_via_url(html);
				}
			}
		}, 1000);
		
		// player_settings
		function player_settings(){
            http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&task=player_settings&post_auto_bbcode='+(document.getElementById('post_auto_bbcode').checked?'true':'false')+'&post_auto_image_video='+(document.getElementById('post_auto_image_video').checked?'true':'false'), true);
            http.send(null);
		}
		// get_image_video_via_url
		function get_image_video_via_url(html){
			var re = /\[toolshot_player[^\]]+image=""[^\]]+url="([^"]+)/g, m, params='';
			do {
				m = re.exec(html);
				if(m){
					params += '&url[]='+m[1];			
				}
			} while(m);
			http.open('GET', 'admin.php?page=toolshot_player_view_player_settings&task=get_image_video_via_url'+params, true);
			http.onreadystatechange = function(){
				match = /<msg>([^<]+)<\/msg>/gim.exec(http.responseText);
				data = JSON.parse(match[1]);
				for (var key in data){
					re = new RegExp('(\\[toolshot_player[^\\]]+image=")("[^\\]]+url="'+data[key]['url'].replace(/(\W)/gim, '\\\$1')+'[^\\]]+\\])', 'gim');
					html = html.replace(re, '$1'+data[key]['img']+'$2');
				}
				tinyMCE.activeEditor.setContent(html);		
			};
			http.send(null);
		}
		
        /*// autoupload handupload
        function autoupload_handupload(thiss){
            document.querySelectorAll('[data-upload="hand_upload"]')[0].click();
            document.getElementById('th_hand_upload_input_url').value = thiss.getAttribute('href');
            th_get_video(document.getElementById('th_hand_upload_input_url'), document.getElementById('th_hand_upload_input_url').value);
            thiss.parentElement.setAttribute('style', 'opacity:0.3');
            //document.getElementById('th_hand_upload_input_url').click();
            return false;
        }*/
    </script>
<?php }?>