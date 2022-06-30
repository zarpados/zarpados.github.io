<?php
/*
Template Name: Contact
*/
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    //var $loading = $('<div class="loading"><img src="/media/images/loading.gif" alt="" /></div>');
    $('.btn-submit').click(function(e){
        var $formId = $(this).parents('form');
        var formAction = $formId.attr('action');
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        $('div',$formId).removeClass('has-error');
        $('label.error').remove();
        $('.required',$formId).each(function(){
			var fl=1;
            var inputVal = $(this).val();
            var $parentTag = $(this).parent();
            if(inputVal == ''){
				$parentTag.children('label').hide();
                $parentTag.addClass('has-error').prepend('<label class="error">This field is required</label>');
				fl=0;
            }
			
            if($(this).hasClass('email') == true){
                if(!emailReg.test(inputVal)){
                    $parentTag.addClass('has-error').prepend('<label class="error">Enter a valid email address</label>');
					fl=0;
                }
            }
			if(fl==1){
				$parentTag.children('label').show();
			}
        });
        if ($('label.error').length == "0") {
            $formId.submit();
        }
        e.preventDefault();
    });
});
</script>
<?php 
global $post, $knowledgepress, $meta;
$meta = redux_post_meta( 'knowledgepress', get_the_ID() );

$contact_email = $meta['contact_email'];
$contact_subject = $meta['contact_subject'];

$errorMessages = array();

if(isset($_POST['submitted'])) {
		$name = trim($_POST['contactName']);
		$email = trim($_POST['email']);
		$comments = trim($_POST['comments']);
		$emailTo = $contact_email; 
		if (!isset($emailTo) || ($emailTo == '') ){
			$emailTo = get_option('admin_email');
		}
		
		if ($contact_subject) { 
			$subject = $contact_subject;
		} else {
			$subject = '[Contact Form] From '.$name;
		}
		
		$body = "Name: $name \n\nEmail: $email \n\nComments: $comments";
		$headers = 'From: '.$name.' <'.$email.'>' . "\r\n" . 'Reply-To: ' . $email;
		
		mail($emailTo, $subject, $body, $headers);
		$emailSent = true;
	}
	
?>

<?php get_template_part('templates/content', 'page'); ?>

<?php if(isset($emailSent) && $emailSent == true) { ?>
        <div class="alert alert-success">
            <?php _e('Thank you, your email was sent successfully.', 'knowledgepress'); ?>
        </div>
<?php } else { ?>

<?php if(isset($hasError) || isset($captchaError)) { ?>
    <div class="alert alert-danger">
        <?php _e('Please fill in all the fields correctly.', 'knowledgepress'); ?>
    </div>
<?php } ?>

<form class="row contact-form" action="<?php the_permalink(); ?>" method="post">
  <fieldset>
    <div class="col-sm-6 form-group">
      <input type="text" name="contactName" id="contact-form" placeholder="<?php _e( 'Name', 'knowledgepress' ); ?>" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField form-control input-lg">
    </div>
    <div class="col-sm-6 form-group">
      <input type="text" name="email" id="contact-form" placeholder="<?php _e( 'Email', 'knowledgepress' ); ?>" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email form-control input-lg">
    </div>
    <div class="col-sm-12 form-group">
      <textarea name="comments" id="contact-form" rows="7" placeholder="<?php _e( 'Message', 'knowledgepress' ); ?>" cols="30" class="required requiredField form-control"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
    </div>
    <div class="col-sm-12">
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <button class="btn btn-primary btn-submit"><?php _e('Send Email', 'knowledgepress') ?></button>
    </div>
  </fieldset>
</form>

<?php } ?>

