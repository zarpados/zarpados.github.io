<?php
/**
 * Pupular posts
 */

// Setup activation
add_action('after_switch_theme', 'PA_Popular_System::install');

// Class for installation and uninstallation
class PA_Popular_System{
  public static function actions() {
    // Check for token
    if ( ! wp_verify_nonce( $_POST['token'], 'pamp_token' ) ) die();
    $track = new PAMP_track( intval( $_POST['id'] ) );
  }
  
  public static function install() {
    PAMP_setup::install();
  }
  
  public static function javascript() {
    global $wp_query;
    wp_reset_query();
    wp_print_scripts('jquery');
    $token = wp_create_nonce( 'pamp_token' );
    if ( is_single() ) {
      echo '<!-- Popular Articles --><script type="text/javascript">/* <![CDATA[ */ jQuery.post("' . admin_url('admin-ajax.php') . '", { action: "pamp_update", id: ' . $wp_query->post->ID . ', token: "' . $token . '" }); /* ]]> */</script><!-- /Popular Articles -->';
    }
  }
}

// Use ajax for tracking popular posts
add_action( 'wp_head', 'PA_Popular_System::javascript' );
add_action( 'wp_ajax_pamp_update', 'PA_Popular_System::actions' );
// Comment out to stop logging stats for admin and logged in users
add_action( 'wp_ajax_nopriv_pamp_update', 'PA_Popular_System::actions' );

function pa_get_popular( $args = array() ) {
  global $wpdb;
  
  // Default arguments
  $limit = 5;
  $post_type = array( 'post' );
  $range = 'all_time';
  
  if ( isset( $args['limit'] ) ) {
    $limit = $args['limit'];
  }
  
  if ( isset( $args['post_type'] ) ) {
    if ( is_array( $args['post_type'] ) ) {
      $post_type = $args['post_type'];
    } else {
      $post_type = array( $args['post_type'] );
    }
  }
  
  if ( isset( $args['range'] ) ) {
    $range = $args['range'];
  }
  
  switch( $range ) {
    CASE 'all_time':
      $order = "ORDER BY all_time_stats DESC";
      break;
    CASE 'monthly':
      $order = "ORDER BY 30_day_stats DESC";
      break;
    CASE 'weekly':
      $order = "ORDER BY 7_day_stats DESC";
      break;
    CASE 'daily':
      $order = "ORDER BY 1_day_stats DESC";
      break;
    DEFAULT:
      $order = "ORDER BY all_time_stats DESC";
      break;
  }

  $holder = implode( ',', array_fill( 0, count( $post_type ), '%s') );
  
  $sql = "
    SELECT
      p.*
    FROM
      {$wpdb->prefix}most_popular mp
      INNER JOIN {$wpdb->prefix}posts p ON mp.post_id = p.ID
    WHERE
      p.post_type IN ( $holder ) AND
      p.post_status = 'publish'
    {$order}
    LIMIT %d
  ";

  $result = $wpdb->get_results( $wpdb->prepare( $sql, array_merge( $post_type, array( $limit ) ) ), OBJECT );
  
  if ( ! $result) {
    return array();
  }
  
  return $result;
}

class PAMP_setup {
  public static function install() {
    // Create table
    global $wpdb;
    $table = $wpdb->prefix . "most_popular";
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
      $sql = "CREATE TABLE $table (
            id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            post_id BIGINT NOT NULL,
            last_updated DATETIME NOT NULL,
            1_day_stats MEDIUMINT NOT NULL,
            7_day_stats MEDIUMINT NOT NULL,
            30_day_stats MEDIUMINT NOT NULL,
            all_time_stats BIGINT NOT NULL,
            raw_stats text NOT NULL);
          ";
      $wpdb->query($sql);
    }
  }
}

class PAMP_track {
  private $post_id = NULL;
  
  public function __construct( $post_id ) {
    $this->post_id = $post_id;
    
    // Action to update stats
    $this->update_stats();
  }
  
  private function update_stats() {
    global $wpdb;
    
    if ( $this->post_id ) {
      // Get the existing raw stats
      $raw_stats = $wpdb->get_var( $wpdb->prepare( "SELECT raw_stats FROM {$wpdb->prefix}most_popular WHERE post_id = '%d'", array( $this->post_id ) ) );
      $date = gmdate('Y-m-d');
      
      if ( $raw_stats ) {
        $raw_stats = unserialize( $raw_stats );
      } else {
        // Create a entry for this post
        $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}most_popular (post_id, last_updated, 1_day_stats, 7_day_stats, 30_day_stats, all_time_stats, raw_stats) VALUES ('%d', NOW(), '0', '0', '0', '0', '')", array( $this->post_id ) ) );
      }
      
      $count_1 = $this->calculate_1_day_stats( $raw_stats, $date );
      $count_7 = $this->calculate_7_day_stats( $raw_stats, $date );
      $count_30 = $this->calculate_30_day_stats( $raw_stats, $date );
      
      if ( isset( $row_stats ) && count( $raw_stats ) >= 30 ) {
        array_shift( $raw_stats );
        $raw_stats[$date] = 1;
      } else {
        if ( ! isset( $raw_stats[$date] ) ) {
          $raw_stats[$date] = 1;
        } else {
          $raw_stats[$date]++;
        }
      } 
      
      // Update our table with new figures
      $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}most_popular SET 1_day_stats = '{$count_1}', 7_day_stats = '{$count_7}', 30_day_stats = '{$count_30}', all_time_stats = all_time_stats + 1, raw_stats = '%s' WHERE post_id = '%d'", array( serialize( $raw_stats ), $this->post_id ) ) );
    }
  }
  
  private function calculate_1_day_stats( $existing_stats, $date ) {
    if ( $existing_stats ) {
      if ( isset( $existing_stats[$date] ) ) {
        return $existing_stats[$date] + 1;
      }
    }
    return 1;
  }
  
  private function calculate_7_day_stats( $existing_stats, $date ) {
    if ( $existing_stats ) {
      $extra_to_add = 0;
      if ( isset( $existing_stats[$date] ) ) {
        $extra_to_add = $existing_stats[$date];
      }
      $total = 0;
      for ( $i = 1; $i < 7; $i++ ) {
        $old_date = date('Y-m-d', strtotime( "-{$i} days" ) );
        if ( isset( $existing_stats[$old_date] ) ) {
          $total += $existing_stats[$old_date];
        }
      }
      return $total + $extra_to_add + 1;
    }
    return 1;
  }
  
  private function calculate_30_day_stats( $existing_stats, $date ) {
    if ( $existing_stats ) {
      $extra_to_add = 0;
      if ( isset( $existing_stats[$date] ) ) {
        $extra_to_add = $existing_stats[$date];
      }
      $total = 0;
      for ( $i = 1; $i < 30; $i++ ) {
        $old_date = date('Y-m-d', strtotime( "-{$i} days" ) );
        if ( isset( $existing_stats[$old_date] ) ) {
          $total += $existing_stats[$old_date];
        }
      }
      return $total + $extra_to_add + 1;
    }
    return 1;
  }
}
