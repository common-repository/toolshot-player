<style>
    .clear_{clear: both;}
    .red_{color:#f00;}
    .disable_{opacity: .5;}
    .form-wrap label{padding:5px 0;}
    input, select {padding: 5px 8px;}
    .view_player_settings_ .checkbox_ label{float:left;margin-right:20px;}
    .slider_{width:95%;margin-top:1em;}
    #custom-handle {
        width: 3em;
        height: 1.6em;
        top: 50%;
        margin-top: -.8em;
        text-align: center;
        line-height: 1.7em;
        cursor: pointer;
    }
    .two_col_ .form-field{
        float:left;
        width: 50%;
    }
    .two_col_ .form-field:first-child{
        margin-right:0%;
    }
    .two_col_ .form-field input[type=text], .two_col_ .slider_{
        width:90%;
    }
    .two_col_ .two_col_ input[type=text], .two_col_ .two_col_ .slider_{
        width:80%;
    }
    .two_col_ .two_col_ .form-field{
        margin:0;
    }
    .view_player_settings_ .dashicons{
        line-height: 28px;
    }
    .ads_{display: none;}
</style>
<div class="wrap nosubsub">
    <h1>ToolsHot Player Settings</h1>

    <div id="col-container" class="wp-clearfix">

        <div id="col-left" style="width:33%;">
            <div class="embed-responsive embed-responsive-16by9">
                <?php
                $url_video = 'p_key='.$toolshot_player['key'].'&url='.urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($toolshot_player['key']), 'https://www.youtube.com/watch?v=iNJdPyoqt8U', MCRYPT_MODE_CBC, md5(md5($toolshot_player['key'])))));
                if($toolshot_player['player'] == 'toolshot'){
                    $tmp = '';
                    foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player' || $key=='search_upload' || $key == 'category_upload'){}else $tmp .= $key.'='.$val.'&'; ?>
                    <iframe class="embed-responsive-item" src="<?=$url_toolshot_player.'?'.$tmp.$url_video?>" allowfullscreen=""></iframe>
                <?php }else{
                    $id_player = 'toolshot_player_'.rand(0, 99999);
                ?>
                    <script type="text/javascript">
                        var toolshot_player_url_video_md5 = {<?=$id_player?>:'<?=md5('https://www.youtube.com/watch?v=iNJdPyoqt8U')?>'};
                        var toolshot_player_url_video = {<?=$id_player?>:'<?='p_key='.$toolshot_player['key'].'&url='.urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($toolshot_player['key']), 'https://www.youtube.com/watch?v=iNJdPyoqt8U', MCRYPT_MODE_CBC, md5(md5($toolshot_player['key'])))))?>'};
                        var toolshot_player_image = {<?=$id_player?>:'<?=$toolshot_player['image']?>'};
                        var toolshot_player_subtitle = {<?=$id_player?>:''};
                    </script>
                    <div class="embed-responsive-item">
                        <div id="<?=$id_player?>" class="toolshot_player_">Loading the player ...</div>
                    </div>
                    <div class="toolshot_logo_"></div>
                    <?php if(isset($toolshot_player['ads_type'])){?>
                        <div id="toolshot_ads">
                            <a title="Close" class="toolshot_ads_close_" href="javascript:void(0);" onclick="document.getElementById('toolshot_ads').outerHTML='';">x</a>
                            <?=$toolshot_player['ads_code']?>
                        </div>
                    <?php }?>
                    <script type="text/javascript">
                        var toolshot_player = {
                            <?php foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player' || $key=='search_upload' || $key == 'category_upload'){}else echo "\n".$key." : '".$val."',";?>
                        };
                        var url_video_md5 = '<?=md5('https://www.youtube.com/watch?v=iNJdPyoqt8U')?>';
                        var url_video = '<?=$url_video?>';
                    </script>
                <?php }?>
            </div><!--/.embed-responsive-->
            <div class="col-wrap">
                <h2>License Player ToolsHot</h2>
                <div class="license_"></div>
                <p>
                    <a href="<?=$url_toolshot?>player?tab=server-support" target="_blank">List server support</a>
                </p>
                <p>
                    <a href="<?=get_site_url()?>/wp-admin/post-new.php#metabox_toolshot_player">Add Post With ToolsHot Player</a>
                </p>
                <!--<div class="donate_">
                    <hr>
                    <p>Current ToolsHot activities based on your funding him, if found useful toolshot please donate to us ..<br>Thank you!</p>
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="DFG36PJGSZTT6">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>
                </div>-->
            </div>
        </div><!-- /col-left -->

        <div id="col-right">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>Skin Player</h2>
                    <form method="post" action="#" class="validate view_player_settings_">
                        <div class="form-field form-required term-name-wrap">
                            <div style="float: left;margin-right:20px;">
                                <label for="player">Player</label>
                                <select name="player" id="player">
                                    <option value="toolshot" <?=$toolshot_player['player']=='toolshot'?'selected':''?>>Toolshot (free)</option>
                                    <option value="self_host" <?=$toolshot_player['player']=='self_host'?'selected':''?>>Self Host (vip)</option>
                                </select>
                            </div>
                            <div style="float: left;margin-right:20px;">
                                <label for="skin">Skin</label>
                                <select name="skin" id="skin">
                                    <option value="default" <?=$toolshot_player['skin']=='default'?'selected':''?>>default</option>
                                    <option value="five" <?=$toolshot_player['skin']=='five'?'selected':''?>>five</option>
                                    <option value="glow" <?=$toolshot_player['skin']=='glow'?'selected':''?>>glow</option>
                                    <option value="roundster" <?=$toolshot_player['skin']=='roundster'?'selected':''?>>roundster</option>
                                    <option value="six" <?=$toolshot_player['skin']=='six'?'selected':''?>>six</option>
                                    <option value="stormtrooper" <?=$toolshot_player['skin']=='stormtrooper'?'selected':''?>>stormtrooper</option>
                                    <option value="vapor" <?=$toolshot_player['skin']=='vapor'?'selected':''?>>vapor</option>
                                </select>
                            </div>
                            <label>&nbsp;</label>
                            <div class="checkbox_">
                                <label class="selectit"><input value="1" type="checkbox" name="autoplay" id="in-category-1" <?=$toolshot_player['autoplay']=='true'?'checked':''?>> Autoplay</label>
                                <label class="selectit"><input value="1" type="checkbox" name="download" id="in-category-1" <?=$toolshot_player['download']=='true'?'checked':''?>> Download Button</label>
                                <label class="selectit"><input value="1" type="checkbox" name="rewind" id="in-category-1" <?=$toolshot_player['rewind']=='true'?'checked':''?>> Rewind Button</label>
                                <label class="selectit"><input value="1" type="checkbox" name="fast_forward" id="in-category-1" <?=$toolshot_player['fast_forward']=='true'?'checked':''?>> Fast Forward Button</label>
                                <label class="selectit"><input value="1" type="checkbox" name="share" id="in-category-1" <?=$toolshot_player['share']=='true'?'checked':''?>> Share Button</label>
                            </div>
                            <div class="clear_"></div>
                        </div>
                        <div class="form-field form-required term-name-wrap">
                            <label for="image">Image Video Default</label>
                            <input name="image" id="image" type="text" value="<?=$toolshot_player['image']?>" aria-required="true" placeholder="Image Url or Empty">
                        </div>
                        <div class="two_col_">
                            <div class="form-field form-required term-name-wrap">
                                <label for="logo">Logo</label>
                                <input name="logo" id="logo" type="text" value="<?=$toolshot_player['logo']?>" size="40" aria-required="true" placeholder="Url Logo Image or Text">
                            </div>
                            <div class="form-field form-required term-name-wrap">
                                <label for="logo_url">Logo Url</label>
                                <input name="logo_url" id="logo_url" type="text" value="<?=$toolshot_player['logo_url']?>" size="40" aria-required="true" placeholder="Logo Url (Example : http://example.com)">
                            </div>
                        </div><!--.two_col_-->
                        <div class="clear_"></div>
                        <div class="form-field form-required term-name-wrap">
                            <label for="logo_size">Logo Size %</label>
                            <div class="slider_" name="logo_size" id="logo_size">
                                <div id="custom-handle" class="ui-slider-handle"></div>
                            </div>
                        </div>
                        <div class="two_col_">
                            <div class="form-field form-required term-name-wrap">
                                <label for="logo_position">Logo Position</label>
                                <div class="checkbox_">
                                    <label class="selectit"><input value="topleft" type="radio" name="logo_position" id="logo_position" <?=$toolshot_player['logo_position']=='topleft'?'checked':''?>> Top Left</label>
                                    <label class="selectit"><input value="topright" type="radio" name="logo_position" id="logo_position" <?=$toolshot_player['logo_position']=='topright'?'checked':''?>> Top Right</label>
                                    <label class="selectit"><input value="bottomleft" type="radio" name="logo_position" id="logo_position" <?=$toolshot_player['logo_position']=='bottomleft'?'checked':''?>> Bottom Left</label>
                                    <label class="selectit"><input value="bottomright" type="radio" name="logo_position" id="logo_position" <?=$toolshot_player['logo_position']=='bottomright'?'checked':''?>> Bottom Right</label>
                                </div>
                            </div>
                            <div class="form-field form-required term-name-wrap">
                                <div class="two_col_">
                                    <div class="form-field form-required term-name-wrap">
                                        <label for="logo_x">Logo X %</label>
                                        <div class="slider_" name="logo_x">
                                            <div id="custom-handle" class="ui-slider-handle"></div>
                                        </div>
                                    </div>
                                    <div class="form-field form-required term-name-wrap">
                                        <label for="logo_y">Logo Y %</label>
                                        <div class="slider_" name="logo_y">
                                            <div id="custom-handle" class="ui-slider-handle"></div>
                                        </div>
                                    </div>
                                </div><!--.two_col_-->
                            </div>
                        </div><!--.two_col_-->
                        <div class="clear_"></div>

                        <div class="clear_"></div>
                        <h2 style="color:#f00;">Advertising (vip only)</h2>
                        <div class="ads_ two_col_">
                            <div class="form-field form-required term-name-wrap">
                                <label for="ads_width">Ads Width (px)</label>
                                <div class="slider_" name="ads_width" id="ads_width">
                                    <div id="custom-handle" class="ui-slider-handle"></div>
                                </div>
                            </div>
                            <div class="form-field form-required term-name-wrap">
                                <label for="ads_height">Ads Height (px)</label>
                                <div class="slider_" name="ads_height" id="ads_height">
                                    <div id="custom-handle" class="ui-slider-handle"></div>
                                </div>
                            </div>
                        </div><!--.two_col_-->
                        <div class="ads_ two_col_">
                            <div class="form-field form-required term-name-wrap">
                                <label for="ads_type">Ads Type</label>
                                <div class="checkbox_">
                                    <label class="selectit"><input value="banner" type="radio" name="ads_type" id="ads_type" <?=!isset($toolshot_player['ads_type'])||isset($toolshot_player['ads_type']) && $toolshot_player['ads_type']=='banner'?'checked':''?>> Banner</label>
                                    <label class="selectit"><input value="banner_close_and_play" type="radio" name="ads_type" id="ads_type" <?=isset($toolshot_player['ads_type']) && $toolshot_player['ads_type']=='banner_close_and_play'?'checked':''?>> Banner Close And Play</label>
                                    <label class="selectit disable_"><input value="video_play" type="radio" name="ads_type" id="in-category-1" disabled> Video Play</label>
                                </div>
                            </div>
                            <div class="form-field form-required term-name-wrap">
                                <div class="two_col_">
                                    <div class="form-field form-required term-name-wrap disable_">
                                        <label for="ads_x">Ads X (px)</label>
                                        <div class="slider_" name="ads_x" id="ads_x">
                                            <div id="custom-handle" class="ui-slider-handle"></div>
                                        </div>
                                    </div>
                                    <div class="form-field form-required term-name-wrap">
                                        <label for="ads_y">Ads Y (px)</label>
                                        <div class="slider_" name="ads_y" id="ads_y">
                                            <div id="custom-handle" class="ui-slider-handle"></div>
                                        </div>
                                    </div>
                                </div><!--.two_col_-->
                            </div>
                        </div><!--.two_col_-->
                        <div class="ads_ clear_"></div>
                        <div class="ads_ form-field term-description-wrap">
                            <label for="ads_code">Ads Code</label>
                            <textarea name="ads_code" id="ads_code" rows="4" cols="40"><?=isset($toolshot_player['ads_code'])?$toolshot_player['ads_code']:''?></textarea>
                        </div>
                        <div class="two_col_">
                            <div class="form-field form-required term-name-wrap">
                                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save & Preview"></p>
                            </div>
                            <div class="form-field form-required term-name-wrap">
                                <p class="submit"><button style="float:right;margin-right:55px;" type="button" name="ads_button_" id="submit" class="button ads_button_"><span class="dashicons dashicons-arrow-down"></span> Settings your ads</button></p>
                            </div>
                        </div><!--.two_col_-->
                        <!--<input type="hidden" name="toolshot_player_act" value="player_settings"/>-->
                        <?php wp_nonce_field('save_func_post_toolshot_player', 'nonce_func_post_toolshot_player');?>
                    </form>
                </div>

            </div>
        </div><!-- /col-right -->

    </div><!-- /col-container -->
</div>
<script>
	var url_toolshot_player = '<?=$url_toolshot_player?>';
    function slider_(thiss, val, max){
        if(typeof max == 'undefined') max = 100;
        thiss.find('div').text(val);
        thiss.slider({
            max : max,
            value : val,
            slide: function(event, ui){
                thiss.find('div').text(ui.value);
            }
        });
    }
    slider_($('form.view_player_settings_ *[name=ads_width]'), <?=isset($toolshot_player['ads_width'])?preg_replace('#\D+#mis', '', $toolshot_player['ads_width']):'300'?>, 1000);
    slider_($('form.view_player_settings_ *[name=ads_height]'), <?=isset($toolshot_player['ads_height'])?preg_replace('#\D+#mis', '', $toolshot_player['ads_height']):'120'?>, 1000);
    slider_($('form.view_player_settings_ *[name=ads_x]'), <?=isset($toolshot_player['ads_x'])?preg_replace('#\D+#mis', '', $toolshot_player['ads_x']):'0'?>, 500);
    slider_($('form.view_player_settings_ *[name=ads_y]'), <?=isset($toolshot_player['ads_y'])?preg_replace('#\D+#mis', '', $toolshot_player['ads_y']):'60'?>, 500);

    slider_($('form.view_player_settings_ *[name=logo_size]'), <?=preg_replace('#\D+#mis', '', $toolshot_player['logo_size'])?>);
    slider_($('form.view_player_settings_ *[name=logo_x]'), <?=preg_replace('#\D+#mis', '', $toolshot_player['logo_x'])?>);
    slider_($('form.view_player_settings_ *[name=logo_y]'), <?=preg_replace('#\D+#mis', '', $toolshot_player['logo_y'])?>);
    // form submit
    $('form.view_player_settings_').submit(function(){
        thiss = $(this);
        thiss.find('*[name=submit]').attr('disabled', '');
        obj_player = {};
        // obj_player
        obj_player['player'] = thiss.find('*[name=player]').val();
        obj_player['skin'] = thiss.find('*[name=skin]').val();
        obj_player['autoplay'] = thiss.find('*[name=autoplay]').prop('checked');
        obj_player['download'] = thiss.find('*[name=download]').prop('checked');
        obj_player['rewind'] = thiss.find('*[name=rewind]').prop('checked');
        obj_player['fast_forward'] = thiss.find('*[name=fast_forward]').prop('checked');
        obj_player['share'] = thiss.find('*[name=share]').prop('checked');
        obj_player['image'] = thiss.find('*[name=image]').val();
        obj_player['logo'] = thiss.find('*[name=logo]').val();
        obj_player['logo_size'] = thiss.find('*[name=logo_size]').slider('value')+'vw';
        obj_player['logo_position'] = thiss.find('*[name=logo_position]:checked').val();
        obj_player['logo_x'] = thiss.find('*[name=logo_x]').slider('value')+'vw';
        obj_player['logo_y'] = thiss.find('*[name=logo_y]').slider('value')+'vh';
        obj_player['logo_url'] = thiss.find('*[name=logo_url]').val();
        if($('div').hasClass('ads_') && $('.ads_').css('display')=='block') {
            obj_player['ads_type'] = thiss.find('*[name=ads_type]:checked').val();
            obj_player['ads_width'] = thiss.find('*[name=ads_width]').slider('value') + 'px';
            obj_player['ads_height'] = thiss.find('*[name=ads_height]').slider('value') + 'px';
            obj_player['ads_x'] = thiss.find('*[name=ads_x]').slider('value') + 'px';
            obj_player['ads_y'] = thiss.find('*[name=ads_y]').slider('value') + 'px';
            obj_player['ads_code'] = thiss.find('*[name=ads_code]').val();
            obj_player['ads_show'] = 'show';
        }else obj_player['ads_show'] = 'hide';
        //obj_player['toolshot_player_act'] = thiss.find('*[name=toolshot_player_act]').val();
        obj_player['nonce_func_post_toolshot_player'] = thiss.find('*[name=nonce_func_post_toolshot_player]').val();
        $.get('admin.php?page=toolshot_player_view_player_settings&task=player_settings', obj_player, function(data){
            data = /<msg>([^<]+)<\/msg>/g.exec(data)[1];
            thiss.find('*[name=submit]').removeAttr('disabled');
            if(data==1){
                location.assign(window.location.href.replace(/\&.*?$/gim, '')+'&msg=Update success');
            }else notice_(thiss, data);
        });
        return false;
    });
    // ads button expan
    $('.ads_button_').click(function(){
        if($(this).find('span').hasClass('dashicons-arrow-down')){
            $(this).find('span').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
            $('.ads_').css('display', 'block');
        }else{
            $(this).find('span').removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
            $('.ads_').css('display', 'none');
        }
    });
    $('h2[style]').click(function(){
        $('.ads_button_').click();
    });

    // change ads type
    $('form.view_player_settings_ *[name=ads_x]').closest('.form-field').css('opacity', '0.5');
    $('form.view_player_settings_ *[name=ads_type]').change(function(){
        if($(this).val()=='banner'){
            $('form.view_player_settings_ *[name=ads_y]').closest('.form-field').css('opacity', '1');
        }else{
            $('form.view_player_settings_ *[name=ads_y]').closest('.form-field').css('opacity', '0.5');
        }
    });
    <?php if($toolshot_player['ads_show']=='show'){?>
        $('.ads_button_').click();
    <?php }?>
    <?=isset($_GET['msg'])?'notice_($("form.view_player_settings_"), "'.$_GET['msg'].'");':''?>
    // notice_
    function notice_(thiss, msg){
        thiss.find('.notice_').remove();
        thiss.append('<div class="notice_" style="width:95%;"><div style="clear:both;"></div><div style="background-color: #ffe8e6;color: #db2828;padding: 1em 1.5em;border-radius: .28571429rem;box-shadow: 0 0 0 1px rgba(34,36,38,.22) inset,0 0 0 0 transparent;">'+msg+'</div></div>');
    }
    $('form').on('keyup keypress blur change', function(){
        $('form').find('.notice_').remove();
    });
    // get license
    $.get('<?=$url_toolshot?>api/get_license', function(data){
        data = jQuery.parseJSON(data);
        if(data['p_type']=='Not set'){
            $('.license_').append('<p><b>Type : </b>'+data['p_type']+'</p><p><a href="<?=$url_toolshot?>player?tab=buy_vip" target="_blank" style="text-decoration:none;">Buy VIP $5/month</a></p>');
        }else{
            if(data['p_type']=='Vip')
                $('.license_').append('<p><b>Type : </b>'+data['p_type']+'</p><p><b>Date Exp (yyyy/mm/dd) : </b>'+data['p_date_exp']+'</p><p><a href="<?=$url_toolshot?>" target="_blank" style="text-decoration:none;">Buy more VIP $5/month</a></p>');
            else
                $('.license_').append('<p><b>Type : </b>'+data['p_type']+'</p><p><a href="<?=$url_toolshot?>player?tab=buy_vip" target="_blank" style="text-decoration:none;">Buy VIP $5/month</a></p>');
        }
    });
</script>