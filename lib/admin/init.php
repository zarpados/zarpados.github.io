<?php

if(!is_admin())
    return ;

add_action( 'admin_init'    , 'pa_admin_init' );
add_action( 'admin_menu'    , 'pa_admin_menu' );
add_action( 'init'          , 'pa_reset_all_views');

function pa_admin_menu(){
    $page = add_menu_page(__('Analytics','knowledgepress'), __('Analytics','knowledgepress'), 'manage_options', 'pa_analytics', 'pa_analytics_page','dashicons-chart-bar',63);
    
    add_action('admin_print_scripts-' . $page, 'pa_analytics_enqueue_script');
}

function pa_admin_init(){
    wp_register_script( 'pa_chart_js', get_template_directory_uri() . '/assets/js/vendor/Chart.min.js');
}

function pa_analytics_enqueue_script(){
    wp_enqueue_script('pa_chart_js');
}

function pa_get_posts_votes($args = array()){
    
    global $wpdb;
    
    $default_args = array(
        'orderby'                   => 'likes',
        'order'                     => 'DESC',
        'posts_per_page'            => 20,
        'page_no'                   => 1,
    );
    
    $args = array_merge($default_args,$args);
    
    $prefix = ($args['orderby']=='likes')?'B':'C';
    
    $qry['A']  = " SELECT COUNT(ID) as total FROM {$wpdb->posts} ";
    $qry['A'] .= " WHERE post_type = 'post' AND post_status = 'publish'";
    
    $results['A'] = $wpdb->get_row($qry['A']);
    
    $qry['B']  = " SELECT A.ID,A.post_title,B.meta_value as 'likes' , C.meta_value as 'dislikes' ";
    $qry['B'] .= " FROM {$wpdb->posts} A ";
    $qry['B'] .= " LEFT JOIN wp_postmeta B ON (B.post_id = A.ID AND B.meta_key='_votes_likes' ) ";
    $qry['B'] .= " LEFT JOIN wp_postmeta C ON (C.post_id = A.ID AND C.meta_key='_votes_dislikes' ) ";
    $qry['B'] .= " WHERE A.post_type = 'post' AND A.post_status = 'publish' "  ;
    $qry['B'] .= " ORDER BY {$prefix}.meta_value {$args['order']} ";
    $qry['B'] .= " LIMIT " . ($args['page_no']-1)*$args['posts_per_page'] . ",{$args['posts_per_page']} ";
    
    $results['B'] = $wpdb->get_results($qry['B'],ARRAY_A);
    
    for($i=0;$i<count($results['B']);$i++){
        $results['B'][$i]['likes']       =  (empty($results['B'][$i]['likes'])?0:$results['B'][$i]['likes']);
        $results['B'][$i]['dislikes']    =  (empty($results['B'][$i]['dislikes'])?0:$results['B'][$i]['dislikes']);
    }
    
    return array(
        'total'         => $results['A']->total,
        'total_pages'   => ceil($results['A']->total/$args['posts_per_page']),
        'records'       => $results['B'],
    );
    
}

function pa_get_posts_views($args){
    global $wpdb;
    
    $default_args = array(
        'order'                     => 'DESC',
        'posts_per_page'            => 30,
        'page_no'                   => 1,
    );
    
    $args = array_merge($default_args,$args);
    
    $qry['A']  = " SELECT COUNT(ID) as total FROM {$wpdb->posts} ";
    $qry['A'] .= " WHERE post_type = 'post' AND post_status = 'publish'";
    
    $results['A'] = $wpdb->get_row($qry['A']);
    
    $qry['B']  = " SELECT A.ID,A.post_title,B.all_time_stats as 'states' ";
    $qry['B'] .= " FROM {$wpdb->posts} A ";
    $qry['B'] .= " LEFT JOIN {$wpdb->prefix}most_popular B ON (B.post_id = A.ID ) ";
    $qry['B'] .= " WHERE A.post_type = 'post' AND A.post_status = 'publish' "  ;
    $qry['B'] .= " ORDER BY B.all_time_stats {$args['order']} ";
    $qry['B'] .= " LIMIT " . ($args['page_no']-1)*$args['posts_per_page'] . ",{$args['posts_per_page']} ";
    
    $results['B'] = $wpdb->get_results($qry['B'],ARRAY_A);
    
    for($i=0;$i<count($results['B']);$i++){
        $results['B'][$i]['states']       =  (empty($results['B'][$i]['states'])?0:$results['B'][$i]['states']);
    }
    
    return array(
        'total'         => $results['A']->total,
        'total_pages'   => ceil($results['A']->total/$args['posts_per_page']),
        'records'       => $results['B'],
    );
}

function pa_get_search_views($args){
    global $wpdb;
    
    $default_args = array(
        'order'                     => 'DESC',
        'posts_per_page'            => 30,
        'page_no'                   => 1,
        'period'                    => 7,
    );
    
    $args = array_merge($default_args,$args);
    
    $qry['A']  = " SELECT count(DISTINCT(search_term)) as total FROM {$wpdb->prefix}search_terms ";
    if($args['period'] != 'all_time')
        $qry['A'] .=  " WHERE DATEDIFF(CURDATE(),search_time)<={$args['period']}";
    
    
    $results['A'] = $wpdb->get_row($qry['A']);
    
    $qry['B']  = " SELECT search_term,count(id) as total ";
    $qry['B'] .= " FROM {$wpdb->prefix}search_terms ";
    if($args['period'] != 'all_time')
        $qry['B'] .=  " WHERE DATEDIFF(CURDATE(),search_time)<={$args['period']}";
    $qry['B'] .= " GROUP BY search_term ";
    $qry['B'] .= " ORDER BY total {$args['order']} ";
    $qry['B'] .= " LIMIT " . ($args['page_no']-1)*$args['posts_per_page'] . ",{$args['posts_per_page']} ";
    
    $results['B'] = $wpdb->get_results($qry['B'],ARRAY_A);
    
    return array(
        'total'         => $results['A']->total,
        'total_pages'   => ceil($results['A']->total/$args['posts_per_page']),
        'records'       => $results['B'],
    );
}

function pa_reset_all_views(){
    global $ss_settings,$wpdb,$ss_options;
    $table = $wpdb->prefix . "most_popular";
    
    $reset_all_views = isset($ss_settings['reset_all_views'])?$ss_settings['reset_all_views']:NULL;
   
    if(empty($reset_all_views))
        return NULL;
    
    $ss_options->ReduxFramework->set('reset_all_views','');
    $qry = " DELETE FROM $table WHERE 1 = 1";
    $wpdb->query($qry);
}


function pa_get_popular_searches($days = 7,$number_of_terms = 3){
    global $wpdb;
    
    $qry  = " SELECT search_term,count(id) as total ";
    $qry .= " FROM {$wpdb->prefix}search_terms ";
    $qry .= " WHERE DATEDIFF(CURDATE(),search_time)<={$days}";
    $qry .= " GROUP BY search_term ";
    $qry .= " ORDER BY total DESC ";
    $qry .= " LIMIT 0,{$number_of_terms} ";
    
    $results = $wpdb->get_results($qry,ARRAY_A);
    
    return $results;
    
}

function pa_popular_searches($days = 7,$number_of_terms = 3){
    global $ss_settings;
    
    if(empty($ss_settings['search_analytics']))
            return ;
    
    $searches = pa_get_popular_searches($days,$number_of_terms); 
    if(count($searches)>0){
        foreach($searches as $search){
            $term[] = "<a href='" . esc_url(home_url('?s=' . $search['search_term'] )) . "'>{$search['search_term']}</a>";
        }
        echo implode(", ", $term);
    }
}

function pa_analytics_page(){
    global $ss_settings;
    $current_tab            = isset($_REQUEST['pa_analytics_case'])?$_REQUEST['pa_analytics_case']:'views';
    $page_no                = isset($_REQUEST['page_no'])?$_REQUEST['page_no']:1;
    $current_period         = isset($_REQUEST['pa_analytics_period'])?$_REQUEST['pa_analytics_period']:'all_time';
    $has_sufficient_data    = FALSE;
    $is_period_enable       = FALSE;
    
    $col = $labels =  $datasets = array();
    
    //if ($ss_settings['article_views'] == 1) {
        $tabs['views'] = __('Views'    ,'knowledgepress');
    //}
    if ($ss_settings['article_voting'] != 0) {
        $tabs['votes'] = __('Votes'    ,'knowledgepress');
    }
    if($ss_settings['search_analytics'] == 1){
        $tabs['searches'] = __('Searches'    ,'knowledgepress');
    }
    
    switch($current_tab){
        case 'votes':
            
            $order_options = array(
                'likes'        => __('Most Liked'      ,'knowledgepress'),
                'dislikes'     => __('Most Disliked'   ,'knowledgepress'),
            );
            
            $current_option = ((isset($_REQUEST['pa_analytics_tab_option']))?$_REQUEST['pa_analytics_tab_option']:'likes');
            $is_disliked    = ($current_option == 'dislikes')?TRUE:FALSE;
            $chart_title    = (!$is_disliked)?__('Most Liked Articles','knowledgepress'):__('Most Disliked Articles','knowledgepress');
            $args           = array(
                'orderby'   => ($is_disliked)?'dislikes':'likes',
                'page_no'   => $page_no,
            );           
            $query_args     = array(
                'page'                      => 'pa_analytics',
                'pa_analytics_tab_option'   => $current_option,
            );
            
            $datas      = pa_get_posts_votes($args);
            
            if($datas['total']>0){
            
                foreach($datas['records'] as $data){
                    $labels[]               = $data['post_title'];
                    $col[0]['data'][]       = $data['likes'];
                    $col[1]['data'][]       = $data['dislikes'];
                }

                $datasets[] = array(
                    'label'                   =>  __('Likes','knowledgepress'),
                    'fillColor'               => "#8BC34A",
                    'strokeColor'             => "#8BC34A",
                    'pointColor'              => "#8BC34A",
                    'pointStrokeColor'        => "#fff",
                    'pointHighlightFill'      => "#fff",
                    'pointHighlightStroke'    => "rgba(220,220,220,1)",
                    'data'                    => $col[0]['data'],
                );

                $datasets[] = array(
                    'label'                   =>  __('Dislikes','knowledgepress'),
                    'fillColor'               => "#D86565",
                    'strokeColor'             => "#D86565",
                    'pointColor'              => "#D86565",
                    'pointStrokeColor'        => "#fff",
                    'pointHighlightFill'      => "#fff",
                    'pointHighlightStroke'    => "rgba(151,187,205,1)",
                    'data'                    => $col[1]['data'],
                );
                $has_sufficient_data = TRUE;
            }else{
                $has_sufficient_data = FALSE;
            }
                            
            break;
        case 'views':
            
            $order_options = array(
                'ASC'        => __('Low to High'        ,'knowledgepress'),
                'DESC'       => __('High to Low'        ,'knowledgepress'),
            );
            
            $current_option     = ((isset($_REQUEST['pa_analytics_tab_option']))?$_REQUEST['pa_analytics_tab_option']:'DESC');
            $is_asc             = ($current_option == 'ASC')?TRUE:FALSE;
            $chart_title        = ($is_asc)?__('Article Views: Low to High','knowledgepress'):__('Article Views: High to Low','knowledgepress');
            $args               = array(
                'order'   => ($is_asc)?'ASC':'DESC',
                'page_no'   => $page_no,
            );  
            
            $query_args     = array(
                'page'                      => 'pa_analytics',
                'pa_analytics_case'         => 'views',
                'pa_analytics_tab_option'   => $current_option,
            );
            
            $datas  = pa_get_posts_views($args);
            
            if($datas['total']>0) {
            
                foreach($datas['records'] as $data){
                    $labels[]               = $data['post_title'];
                    $col[0]['data'][]       = $data['states'];
                }

                $datasets[] = array(
                    'label'                   =>  __('States','knowledgepress'),
                    'fillColor'               => "#00aff0",
                    'strokeColor'             => "#00aff0",
                    'pointColor'              => "#00aff0",
                    'pointStrokeColor'        => "#fff",
                    'pointHighlightFill'      => "#fff",
                    'pointHighlightStroke'    => "rgba(220,220,220,1)",
                    'data'                    => $col[0]['data'],
                );
                $has_sufficient_data = TRUE;
            }else{
                $has_sufficient_data = FALSE;
            }
            break;
        case 'searches':
            $is_period_enable   = TRUE;
            $order_options      = array(
                'ASC'        => __('Low to High'        ,'knowledgepress'),
                'DESC'       => __('High to Low'        ,'knowledgepress'),
            );
            
            $periods            = array(
                '1'         => __('Last 1 Day'   ,'knowledgepress'),
                '7'         => __('Last 7 Days'  ,'knowledgepress'),
                '30'        => __('Last 30 Days' ,'knowledgepress'),
                'all_time'  => __('All Time','knowledgepress'),
            );
            
            $current_option     = ((isset($_REQUEST['pa_analytics_tab_option']))?$_REQUEST['pa_analytics_tab_option']:'DESC');
            $is_asc             = ($current_option == 'ASC')?TRUE:FALSE;
            $chart_title        = ($is_asc)?__('Searches: Low to High for %s','knowledgepress'):__('Searches: High to Low for %s','knowledgepress');
            $args               = array(
                'order'     => ($is_asc)?'ASC':'DESC',
                'page_no'   => $page_no,
                'period'    => $current_period,
            );  
            
            $chart_title = sprintf($chart_title,$periods[$current_period]);
            
            $query_args     = array(
                'page'                      => 'pa_analytics',
                'pa_analytics_case'         => 'searches',
                'pa_analytics_tab_option'   => $current_option,
                'pa_analytics_period'       => $current_period,
            );
            
            $datas  = pa_get_search_views($args);
            if($datas['total']>0){
                foreach($datas['records'] as $data){
                    $labels[]               = $data['search_term'];
                    $col[0]['data'][]       = $data['total'];
                }

                $datasets[] = array(
                    'label'                   =>  __('States','knowledgepress'),
                    'fillColor'               => "#00aff0",
                    'strokeColor'             => "#00aff0",
                    'pointColor'              => "#00aff0",
                    'pointStrokeColor'        => "#fff",
                    'pointHighlightFill'      => "#fff",
                    'pointHighlightStroke'    => "rgba(220,220,220,1)",
                    'data'                    => $col[0]['data'],
                );
                $has_sufficient_data = TRUE;
            }else{
                $has_sufficient_data = FALSE;
            }
            
            break;

    }
    
    ?>
<script type="text/javascript">
    jQuery().ready(function(){
       <?php 
       if($has_sufficient_data) :
       ?> 
       var data = {
            labels      : <?php echo json_encode($labels); ?>,
            datasets    : <?php echo json_encode($datasets); ?>   
        };
        
        Chart.defaults.global.responsive = true;
        var ctx         = document.getElementById("myChart").getContext("2d");
        var myBarChart  = new Chart(ctx).Bar(data);
                
        
        <?php
        endif;
        ?>
        jQuery('.tab_select_opt').change(function(){
            jQuery('.tab_frm').submit();
        });
    });
</script>
<style type="text/css">
    #chat_ct,.pa_note_ct {
        text-align:center;
        padding:20px 0px; 
    }
    .tab_frm{
        float:right; 
    }
    .custom-tablenav{
        text-align:center;  
    }
    .tablenav-pages{
        float:none !important; 
    }
</style>
<div class="wrap">
    <h2><?php _e('Analytics','knowledgepress'); ?></h2>
    <h2 class="nav-tab-wrapper">
        <?php
        foreach($tabs as $tab_key => $tab){
            ?>
            <a href="<?php echo admin_url("admin.php?page=pa_analytics&pa_analytics_case={$tab_key}"); ?>" class="nav-tab <?php echo (($current_tab == $tab_key)?'nav-tab-active':''); ?>">
                <?php echo $tab?>
            </a>
            <?php
        }
        ?>
    </h2>
    <div id="chat_ct">
        <h3><?php echo $chart_title; ?></h3>
        <form method="GET" class="tab_frm">
            <input type="hidden" name="page" value="pa_analytics" />
            <input type="hidden" name="pa_analytics_case" value="<?php echo $current_tab; ?>" />
            <?php
            if($is_period_enable) :
                ?>
                <label for="pa_analytics_period"><?php _e('Filter by period:','knowledgepress'); ?></label>
                <select name="pa_analytics_period" class="tab_select_opt">
                <?php
                foreach ($periods as $period => $label){
                    echo "<option value=\"{$period}\"" . (($current_period==$period)?'selected="selected"':'') . ">{$label}</option>";
                }
                ?>
                </select>
                <?php
            endif;
            ?>
            <label for="pa_analytics_tab_option"><?php _e('Order:','knowledgepress'); ?></label>
            <select name="pa_analytics_tab_option" class="tab_select_opt">
                <?php 
                foreach($order_options as $key => $label){
                    echo "<option value=\"{$key}\" " . (($current_option == $key)?'selected="selected"':'') . ">{$label}</option>";
                }
                ?>
            </select>
        </form>
        <br/>
        <?php 
        if($has_sufficient_data):    
            ?><canvas id="myChart" width="700" height="400"></canvas><?php
        else:
            ?>
            <div class="pa_note_ct">
                <h2><?php _e('Sorry Sufficient Data is Not Available To generate the Graph','knowledgepress'); ?></h2>
            </div>
                <?php
        endif;
        ?>
        
        <div class="tablenav custom-tablenav">
        <div class="tablenav-pages">
        <span class="pagination-links">
            <?php
            echo paginate_links(array(
                'base'      => admin_url('admin.php') . '%_%',
                'format'    => '?page_no=%#%',
                'total'     => $datas['total_pages'],
                'add_args'  => $query_args,
                'current'   => $page_no,
            ));
            ?>
        </span>
        </div>
        </div>
    </div>
</div>
    <?php
}

