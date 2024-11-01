jwplayer.key = "dWwDdbLI0ul1clbtlw+4/UHPxlYmLoE9Ii9QEw==";
/* deleteCache */
function deleteCache(url_video_md5){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if (this.responseText==1) {
            //location.reload();
            console.log('Delete cache');
        }
    };
    xhttp.open('GET', url_toolshot_player+'main/delete_cache?url='+url_video_md5, true);
    xhttp.send();
}
/* ads */
if(typeof toolshot_player != 'undefined' && toolshot_player['ads_show']=='show'){
    switch(toolshot_player['ads_type']){
        case 'banner':
            document.getElementById('toolshot_ads').setAttribute('style', 'left:calc(50% - '+(toolshot_player['ads_width'].match(/\d+/g)/2)+'px);width:'+toolshot_player['ads_width']+';height:'+toolshot_player['ads_height']+';display:block;bottom:'+toolshot_player['ads_y']+';');
            break;
        case 'banner_close_and_play':
            document.getElementById('toolshot_ads').setAttribute('style', 'left:calc(50% - '+(toolshot_player['ads_width'].match(/\d+/g)/2)+'px);top:calc(50% - '+(toolshot_player['ads_height'].match(/\d+/g)/2)+'px);width:'+toolshot_player['ads_width']+';height:'+toolshot_player['ads_height']+';display:block;');
            document.getElementsByClassName('toolshot_ads_close_')[0].setAttribute('onclick', 'document.getElementById(\'toolshot_ads\').outerHTML=\'\';jwplayer().play();');
            btn_close_and_play = document.createElement('div');
            btn_close_and_play.setAttribute('class', 'toolshot_ads_btn_close_and_play_');
            btn_close_and_play.innerHTML = '<button onclick="document.getElementById(\'toolshot_ads\').outerHTML=\'\';jwplayer().play();">Close And Play</button>';
            document.getElementById('toolshot_ads').appendChild(btn_close_and_play);
            toolshot_player['autoplay'] = 'false';
            break;
    }
}
/* player settup */
if(typeof toolshot_player_url_video_md5 != 'undefined' && typeof toolshot_player_url_video   != 'undefined' && typeof toolshot_player_image != 'undefined') {
    Object.keys(toolshot_player_url_video_md5).forEach(function (key) {
        document.getElementById(key).parentNode.setAttribute('style', 'display:none;');

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            var obj_player = {
                sources: eval(xhttp.responseText),
                allowfullscreen: true,
                autostart: (toolshot_player['autoplay'] == 'true' ? true : false),
                responsive: true,
                primary: "html5",
                width: "100%",
                height: "100%",
                skin: {name: toolshot_player['skin']},
                image: toolshot_player_image[key],
                sharing: {
                    link: document.URL,
                    sites: ['googleplus','facebook','twitter','reddit',]
                },
                abouttext: "ToolsHot Player",
                aboutlink: "http://toolshot.com/player",
            };
            if(toolshot_player['share']=='false')
                delete obj_player['sharing'];
            if(toolshot_player_subtitle[key]!=''){
                console.log(toolshot_player_subtitle[key]);
                subtitle_file = /&subtitle_file=(.+?)&subtitle_label/gim.exec(toolshot_player_subtitle[key])[1];
                subtitle_label = /&subtitle_label=(.+?)&subtitle_default/gim.exec(toolshot_player_subtitle[key])[1];
                subtitle_default = /&subtitle_default=(.+?)(&|$)/gim.exec(toolshot_player_subtitle[key])[1];
                obj_player['tracks'] = [];
                var myRegexp = /(.+?),\|,/g;
                match = myRegexp.exec(subtitle_file);
                while (match != null) {
                    obj_player['tracks'].push({file:url_toolshot_player+'option/load_file?url='+match[1]});
                    match = myRegexp.exec(subtitle_file);
                }
                match = myRegexp.exec(subtitle_label);
                i = 0;
                while (match != null) {
                    obj_player['tracks'][i]['label'] = match[1];
                    match = myRegexp.exec(subtitle_label);
                    i++;
                }
                match = myRegexp.exec(subtitle_default);
                i = 0;
                while (match != null) {
                    obj_player['tracks'][i]['default'] = (match[1]=='true'?true:false);
                    match = myRegexp.exec(subtitle_default);
                    i++;
                }
            }
            var player = jwplayer(key);
            player["setup"](obj_player);
            /* button */
            jwplayer().on('ready', function () {
                var leftGroup = document.getElementById(key).getElementsByClassName('jw-controlbar-left-group')[0];
                rewind = 10;
                if (toolshot_player['rewind'] == 'true') {
                    var myRewButton = document.createElement("div");
                    myRewButton.id = "myRewButton";
                    myRewButton.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-rewind');
                    myRewButton.setAttribute('onclick', 'jwplayer().seek(jwplayer().getPosition()-' + rewind + ')');
                    leftGroup.insertBefore(myRewButton, leftGroup.childNodes[1]);
                }
                fast_forward = 10;
                if (toolshot_player['fast_forward'] == 'true') {
                    var myFFButton = document.createElement("div");
                    myFFButton.id = "myFFButton";
                    myFFButton.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-fast-forward');
                    myFFButton.setAttribute('onclick', 'jwplayer().seek(jwplayer().getPosition()+' + fast_forward + ')');
                    leftGroup.insertBefore(myFFButton, leftGroup.childNodes[2]);
                }
                if (toolshot_player['download'] == 'true') {
                    var download_ = document.createElement("a");
                    download_.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-download3');
                    download_.setAttribute('href', ''+url_toolshot_player+'download/?' + toolshot_player_url_video[key] + '&referrer=' + window.location.href);
                    download_.setAttribute('target','_blank');
                    document.getElementById(key).getElementsByClassName('jw-group jw-controlbar-left-group jw-reset')[0].appendChild(download_);
                }
                document.getElementById(key).parentNode.setAttribute('style', 'display:block;');
            });
            /* error */
            jwplayer().on('error', function () {
                deleteCache(toolshot_player_url_video_md5[key]);
            });
        };
        xhttp.open("GET", url_toolshot_player+"json_video?" + toolshot_player_url_video[key], true);
        xhttp.send(null);
    });
}
/* logo */
if(typeof toolshot_player != 'undefined') {
    var tmp = '';
    switch (toolshot_player['logo_position']) {
        case 'topleft':
            tmp += 'top:' + toolshot_player['logo_y'].match(/\d+/g) + '%;';
            tmp += 'left:' + toolshot_player['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'topright':
            tmp += 'top:' + toolshot_player['logo_y'].match(/\d+/g) + '%;';
            tmp += 'right:' + toolshot_player['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'bottomleft':
            tmp += 'bottom:' + toolshot_player['logo_y'].match(/\d+/g) + '%;';
            tmp += 'left:' + toolshot_player['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'bottomright':
            tmp += 'bottom:' + toolshot_player['logo_y'].match(/\d+/g) + '%;';
            tmp += 'right:' + toolshot_player['logo_x'].match(/\d+/g) + '%;';
            break;
    }
    if (toolshot_player['logo'].match(/^https?\:\/\//gim)) tmp += 'width:' + toolshot_player['logo_size'].match(/\d+/g) + '%;';
    else tmp += 'font-size:' + (toolshot_player['logo_size'].match(/\d+/g) * 2 / 10) + 'em;'
    document.getElementsByClassName('toolshot_logo_')[0].setAttribute('style', tmp);
    tmp = '<a href="' + toolshot_player['logo_url'] + '">';
    tmp += (toolshot_player['logo'].match(/^https?\:\/\//gim) ? '<img src="' + toolshot_player['logo'] + '">' : '<span>' + toolshot_player['logo'] + '</span>');
    tmp += '</a>';
    document.getElementsByClassName('toolshot_logo_')[0].innerHTML = tmp;
}