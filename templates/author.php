    <div id="author-box" class="clearfix">
        <div class="author-box-image">
            <?php echo get_avatar( get_the_author_meta('ID'), 72 ); ?>
        </div><!-- /.author-box-image -->
        
        <h4 class="author-box-name"><?php the_author_meta( 'display_name' ); ?></h4>
        <?php //if( strlen( trim( the_author_meta( 'description' ) ) > 0 ) ) { ?>
            <div class="author-box-description">
                <p><?php the_author_meta( 'description' ); ?></p>
            </div><!-- /.author-box-description -->
        <?php //} // end if ?>
        <p class="author-links">
            <i class="icon-user"></i> <a class="author-link author-posts-url" href="<?php echo trailingslashit( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo get_the_author_meta( 'display_name' ); ?> <?php _e( 'Posts', 'knowledgepress' ); ?>"><?php _e( 'Posts', 'knowledgepress' ); ?></a>
            
        <?php if( strlen( trim( get_the_author_meta( 'user_url' ) ) ) > 0 ) { ?>
            &nbsp;<i class="icon-globe"></i> <a class="author-link author-url" href="<?php echo trailingslashit( the_author_meta( 'user_url' ) ); ?>" title="<?php _e( 'Website', 'knowledgepress' ); ?>" target="_blank" rel="author"><?php _e( 'Website', 'knowledgepress' ); ?></a>
        <?php } // end if ?>
        
        <?php if( strlen( trim( get_user_meta( get_the_author_meta( 'ID' ), 'user_tw', true ) ) ) > 0 ) { ?>
            &nbsp;<i class="icon-twitter-sign"></i> <a class="author-link icn-twitter" href="<?php echo trailingslashit( get_user_meta( get_the_author_meta( 'ID' ), 'user_tw', true ) ); ?>" title="<?php _e( 'Twitter', 'knowledgepress' ); ?>" target="_blank"><?php _e( 'Twitter', 'knowledgepress' ); ?></a>
        <?php } // end if ?>

        <?php if( strlen( trim( get_user_meta( get_the_author_meta( 'ID' ), 'user_fb', true ) ) ) > 0 ) { ?>
            &nbsp;<i class="icon-facebook-sign"></i> <a class="author-link icn-facebook" href="<?php echo trailingslashit( get_user_meta( get_the_author_meta( 'ID' ), 'user_fb', true ) ); ?>" title="<?php _e( 'Facebook', 'knowledgepress' ); ?>" target="_blank"><?php _e( 'Facebook', 'knowledgepress' ); ?></a>
        <?php } // end if ?>
        
        <?php if( strlen( trim( get_user_meta( get_the_author_meta( 'ID' ), 'google_profile', true ) ) ) > 0 ) { ?>
            &nbsp;<i class="icon-google-plus-sign"></i> <a class="author-link icn-gplus" href="<?php echo trailingslashit( get_user_meta( get_the_author_meta( 'ID' ), 'google_profile', true ) ); ?>" title="<?php _e( 'Google+', 'knowledgepress' ); ?>" target="_blank"><?php _e( 'Google+', 'knowledgepress' ); ?></a>
        <?php } // end if ?>
        
        </p>
    </div><!-- /.author-box -->		
