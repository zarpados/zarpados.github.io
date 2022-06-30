<?php
/*
Template Name: Knowledge Base
*/

/**
 * Return the total no of unique post in terms/Categories
 * 
 * @global type $wpdb
 * @param array $term_id
 * @return type
 */

// global $post;
global $post, $knowledgepress, $meta;
$meta = redux_post_meta( 'knowledgepress', get_the_ID() );

$show_3rd_level_cat = $meta['3rd_level_cat'];
$kb_aticles_per_cat = $meta['kb_aticles_per_cat'];
$kb_categories = 'list';
if (isset($meta['kb_categories']) && $meta['kb_categories'] != '') {
    $kb_categories = implode(",", $meta['kb_categories']);
}
$kb_columns = 3;
$col_class = 4;

if (isset($meta['kb_columns']) && $meta['kb_columns'] != '') {
    $kb_columns = $meta['kb_columns'];
    if ($kb_columns == 2) {
        $col_class = 6;
    } elseif ($kb_columns == 4) {
        $col_class = 3;
    }
} 
function get_total_cat_count($term_id = array()){
    global $wpdb;
    
    $result['A'] = 0;
    
    $qry['A']  = " SELECT DISTINCT(B.object_id) FROM {$wpdb->term_taxonomy} A , {$wpdb->term_relationships} B,{$wpdb->posts} C ";
    $qry['A'] .= " WHERE A.term_taxonomy_id=B.term_taxonomy_id AND A.term_id IN (" .  implode(",",$term_id) . ") "; 
    $qry['A'] .= " AND B.object_id = C.ID AND C.post_status='publish' AND C.post_type='post'"; 
    
    
    $result['A'] = $wpdb->get_results($qry['A']);
    return count($result['A']);
}

$categories = get_categories(array(
    'orderby'         => 'slug',
    'order'           => 'ASC',
    'hierarchical'    => true,
    'parent'          => 0,
    'hide_empty'      => false,
    'include'         => $kb_categories,
)); 

$i    = 0;
$skip = TRUE;

foreach($categories as $category) { 
    
    
    $term_id        = array();
    $sub_categories = NULL;
    $subcategories  = NULL;
    $term_id[]      = $category->term_id;
    
    $sub_categories = array_values(get_categories(array(
        'orderby'   => 'name',
        'order'     => 'ASC',
        'child_of'  => $category->cat_ID,
    )));
    
    
    if(count($sub_categories)>0) {
        for($j=0;$j<count($sub_categories);$j++){
            $subcategories[$sub_categories[$j]->term_id] = $sub_categories[$j];
        }
        
        foreach($subcategories as $sub_cat){
            if($sub_cat->parent != $category->term_id){
                if(isset($subcategories[$sub_cat->parent])){
                    $subcategories[$sub_cat->parent]->subcats[] = $sub_cat;
                    $term_id[] = $sub_cat->term_id;
                }
                unset($subcategories[$sub_cat->term_id]);
            }
            
        }
    }
    
    $cat_posts = get_posts(array(
        'numberposts'   => -1,
        'category__and'  => $category->term_id,
    ));
    
    if(count($subcategories)==0 && count($cat_posts)==0){
        continue;
    }

    if($i++%$kb_columns==0 && $skip){
        ?>
        <div class="row knowledge-base-row">
        <?php
    }
    $skip = TRUE;
    ?>
    <div class="col-sm-<?php echo $col_class; ?> kb-category">
        <h2>
            <a href="<?php echo get_category_link($category->term_id); ?>" title="<?php echo $category->name; ?>">
            <?php echo $category->name; ?>
            </a>
        </h2>
        <?php
        if(count($subcategories)>0) {
        foreach($subcategories as $sub_category) { 
            $term_id[] = $sub_category->term_id;
            ?>
        <ul class="sub-categories">
            <li class="list-post pa-post-format">
                <?php
                if(isset($sub_category->subcats)>0 && $show_3rd_level_cat ){
                    ?>
                    <?php echo category_open_icon(); ?><a href="<?php  echo get_category_link( $sub_category->term_id ) ?>" title="<?php echo $sub_category->name;  ?>"><?php echo $sub_category->name; ?></a>
                    <ul class="subcat">
                    <?php
                    foreach($sub_category->subcats as $sub_sub_cat){
                        ?>
                        <li class="list-post pa-post-format">
                            <?php echo category_icon(); ?><a href="<?php  echo get_category_link( $sub_sub_cat->term_id ) ?>" title="<?php echo $sub_sub_cat->name;  ?>"><?php echo $sub_sub_cat->name; ?></a>
                        </li>
                        <?php
                    }
                    ?>
                    </ul>
                    <?php
                }else{
                ?>
                    <?php echo category_icon(); ?><a href="<?php  echo get_category_link( $sub_category->term_id ) ?>" title="<?php echo $sub_category->name;  ?>"><?php echo $sub_category->name; ?></a>    
                <?php 
                }
                ?>
            </li>
        </ul>
            <?php 
        }
        }
        
        if(count($cat_posts)>0){
            ?>
            <ul class="category-posts">
            <?php 
            $j            = 1;
            $cat_post_num = $kb_aticles_per_cat; 
            foreach($cat_posts as $post){
                setup_postdata($post);
                ?>
                <li class="list-post pa-post-format"><?php echo post_icon(); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php
            if($j++==$cat_post_num)
                break;
            }
            ?>
            </ul>
        <?php
        }
        ?>
        <a class="view-all" href="<?php echo get_category_link( $category->term_id ) ?>" > <?php _e('View all', 'knowledgepress'); ?> <?php echo get_total_cat_count($term_id);  ?> <?php _e('articles', 'knowledgepress'); ?></a>
    </div>
    <?php		
    
    if($i%$kb_columns==0){
        ?>
        </div>
        <?php
    }
   
}
if($i%$kb_columns!=0){
        echo "</div>";
    }
wp_reset_query();