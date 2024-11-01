<?php
    /**
     *   create table
     */
    function toolshot_class_table_create($table_name, $colums=[]){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        if($wpdb->get_var("show tables like '$table_name'") != $table_name){
            $sql = "CREATE TABLE $table_name ( ";
            foreach($colums as $key => $val) {
                $sql .= $val;
                if ($key != count($colums)-1)
                    $sql .= ", ";
            }
            $sql .= " );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    /**
     *   drop table
     */
    function toolshot_class_table_drop($table_name){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        return $wpdb->query("DROP TABLE $table_name");
    }
    /**
     *   insert table
     */
    function toolshot_class_table_insert($table_name, $colum_vals=[]){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        return $wpdb->insert($table_name, $colum_vals);
    }
    /**
     *   update table
     */
    function toolshot_class_table_update($table_name, $arr = null){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        $sql = "UPDATE $table_name";
        if($arr!=null)
            foreach ($arr as $key => $val){
                $sql .= " $key $val";
            }
        return $wpdb->query($sql);
    }
    /**
     *   delete table
     */
    function toolshot_class_table_delete($table_name, $arr = null){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        $sql = "DELETE FROM $table_name";
        if($arr!=null)
            foreach ($arr as $key => $val){
                $sql .= " $key $val";
            }
        return $wpdb->query($sql);
    }
    /**
     *   select table
     */
    function toolshot_class_table_select($table_name, $arr = null){
        global $wpdb;
        $table_name = $wpdb->prefix.$table_name;
        $sql = "SELECT {$arr['select']} FROM $table_name";
        if($arr!=null)
            foreach($arr as $key => $val){
                if($key != 'select')
                    $sql .= " $key $val";
            }
        return $wpdb->get_results($sql);
    }
    /**
     *   curl php
     */
    function toolshot_class_curl_php($url, $type='get'){
        $curl = curl_init();
        if($type=='post'){
            preg_match('#(^.*?)\?(.*?)$#mis', $url, $match);
            curl_setopt($curl, CURLOPT_URL, $match[1]);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$match[2]);
        }else{
            curl_setopt( $curl, CURLOPT_URL, $url );
        }

        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_HEADER, false);
        curl_setopt( $curl, CURLOPT_REFERER, get_site_url());
        curl_setopt( $curl, CURLOPT_ENCODING, '' );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/51.2.109 Chrome/45.2.2454.109 Safari/537.36' );
        curl_setopt( $curl, CURLOPT_AUTOREFERER, true );
        curl_setopt( $curl, CURLOPT_MAXREDIRS, 10 );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION,true);


        $html = curl_exec( $curl );
        curl_close ( $curl );
        return $html;
    }
