<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\cirkle;

if ( post_password_required() ) {
    return;
}
?>
<div id="comments" class="contact-form comments-area">
    <?php if ( have_comments() ): ?>
    <div class="blog-comment">
        <?php
        $rdtheme_comment_count = get_comments_number();
        $rdtheme_comments_text = number_format_i18n( $rdtheme_comment_count ) ;
        if ( $rdtheme_comment_count > 1 ) {
            $rdtheme_comments_text .= esc_html__( ' Comments', 'cirkle' );
        }
        else{
            $rdtheme_comments_text .= esc_html__( ' Comment', 'cirkle' );
        }
        ?>
        <h3 class="comment-title"><?php echo esc_html( $rdtheme_comments_text );?></h3>
        <?php
        $rdtheme_avatar = get_option( 'show_avatars' );
        ?>
        <ul class="comment-list<?php echo empty( $rdtheme_avatar ) ? ' avatar-disabled' : '';?>">
            <?php
                wp_list_comments(
                    array(
                        'style'        => 'ul',
                        'callback'     => 'radiustheme\cirkle\Helper::comments_callback',
                        'reply_text'   => esc_html__( 'Reply', 'cirkle' ),
                        'avatar_size'  => 90,
                        'format'       => 'html5'
                    ) 
                );
            ?>
        </ul>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :?>
            <nav class="pagination-area comment-pagination">
                <ul>
                    <li class="older-comments"><?php previous_comments_link( esc_html__( 'Older Comments', 'cirkle' ) ); ?></li>
                    <li class="newer-comments"><?php next_comments_link( esc_html__( 'Newer Comments', 'cirkle' ) ); ?></li>
                </ul>
            </nav>
        <?php endif;?>
    </div>
    <?php endif;?>
    
    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="comments-closed"><?php esc_html_e( 'Comments are closed.', 'cirkle' ); ?></p>
    <?php endif; ?>

    <?php
    // Start displaying Comment Form
    $rdtheme_commenter = wp_get_current_commenter();		
    $rdtheme_req = get_option( 'require_name_email' );
    $rdtheme_aria_req = ( $rdtheme_req ? " required" : '' );


    $rdtheme_fields =  array(
        '<div class="row">
            <div class="col-sm-4"><div class="form-group">',
                'author' => '
                <input id="author" class="form-control" name="author" value="' . esc_attr( $rdtheme_commenter['comment_author'] ) . '" type="text" placeholder="'.esc_attr__( 'Name', 'cirkle' ).'" size="30"' . $rdtheme_aria_req . '/>
            </div></div>
            <div class="col-sm-4"><div class="form-group">',
                'email'  => '
                <input id="email" class="form-control" name="email" value="' . esc_attr(  $rdtheme_commenter['comment_author_email'] ) . '" type="email" placeholder="'.esc_attr__( 'E-mail', 'cirkle' ).'" size="30"' . $rdtheme_aria_req . '/>
            </div></div>
            <div class="col-sm-4"><div class="form-group">',
                'website'  => '
                <input id="website" class="form-control" name="website" value="' . esc_attr( $rdtheme_commenter['comment_author_url'] ) . '" type="text" placeholder="'.esc_attr__( 'Website', 'cirkle' ).'" size="30"' . $rdtheme_aria_req . '/>
            </div></div>
        </div>',
    );

    $rdtheme_args = array(
        'submit_field'  => '<div class="form-submit">%1$s %2$s</div>',
        'title_reply'   => esc_html__( 'Comment', 'cirkle' ),
        'submit_button' => '<div class="form-group"><button type="submit" class="submit-btn">'.esc_attr__( 'Post Comment', 'cirkle' ).'</button></div>',
        'comment_field' =>  '<div class="form-group comment-form-comment"><textarea id="comment" name="comment" required placeholder="'.esc_attr__( 'Your Comment *', 'cirkle' ).'" class="textarea form-control" rows="6" cols="20"></textarea></div>',
        'fields' => apply_filters( 'comment_form_default_fields', $rdtheme_fields ),
    );
    ?>
    <div class="reply-separator"></div>
    <?php comment_form( $rdtheme_args );?>
</div>