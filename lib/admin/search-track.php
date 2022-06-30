<?php

class PA_Search_Track {
    
    function __construct() {
        global $ss_settings;
        add_action('after_switch_theme',array($this,'install')); // Install the Table
        if(empty($ss_settings['search_analytics']))
            return ;
        add_action('wp_footer'                            ,array($this,'tracking_script'));
        add_action('wp_ajax_pamp_search_analytics'        ,array($this,'record_track'));
        add_action('wp_ajax_nopriv_pamp_search_analytics' ,array($this,'record_track'));
        add_action('init'                                 ,array($this,'reset_all_searches'));
    }
    
    function record_track(){
        global $wpdb;
        
        if ( ! wp_verify_nonce( $_POST['token'], 'pamp_search_token' ) ) {
            die();
        }
        
        $search_term     =  $_REQUEST['term'];
        $wpdb->insert("{$wpdb->prefix}search_terms",array(
            'search_term' => $search_term,
        ),array(
            '%s',
        ));
    }
    
    function tracking_script(){
        $token          = wp_create_nonce( 'pamp_search_token' );
        $search_term    =  get_search_query();
        if (is_search()) {
            echo '<!-- Search Analytics --><script type="text/javascript">/* <![CDATA[ */ jQuery.post("' . admin_url('admin-ajax.php') . '", { action: "pamp_search_analytics", term: "' . $search_term . '", token: "' . $token . '" }); /* ]]> */</script><!-- /Search Analytics -->';
        }
    }
    /**
     * Check and create the search Terms Table in case it doesn't exist
     * 
     * @global type $wpdb
     */
    public function install(){
        global $wpdb;
        
        $table = $wpdb->prefix . "search_terms";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            /**
             * incase Table Doesn't Exist than Create the New Table
             */
            $sql  = " CREATE TABLE $table ( ";
            $sql .= " id bigint(20) NOT NULL AUTO_INCREMENT, ";
            $sql .= " search_term varchar(256) NOT NULL, ";
            $sql .= " search_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
            $sql .= " PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";
          
            $wpdb->query($sql);
        }
    }


    function reset_all_searches(){
        global $ss_settings,$wpdb,$ss_options;
        $table = $wpdb->prefix . "search_terms";
        
        $reset_all_searches = isset($ss_settings['reset_all_searches'])?$ss_settings['reset_all_searches']:NULL;
       
        if(empty($reset_all_searches))
            return NULL;
        
        $ss_options->ReduxFramework->set('reset_all_searches','');
        $qry = " DELETE FROM $table WHERE 1 = 1";
        $wpdb->query($qry);
    }

}

new PA_Search_Track();