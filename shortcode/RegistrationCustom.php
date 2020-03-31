<?php
namespace WPCF\shortcode;

defined( 'ABSPATH' ) || exit;

include_once CFREG_DIR_PATH.'/../wp-crowdfunding/shortcode/Registration.php';

class RegistrationCustom extends Registration {

    function __construct() {
        add_shortcode( 'wpcf_registration_custom', array( $this, 'registration_callback' ) );
        
        add_action( 'wp_ajax_wpcf_registration_custom', array( $this, 'registration_save_action' ) );
        add_action( 'wp_ajax_nopriv_wpcf_registration_custom', array( $this, 'registration_save_action' ) );
        add_action( 'cfreg_verification_email', array( $this, 'send_verification_email' ) );
    }
  
  
    public function registration_callback() {
        ob_start();
        if ( is_user_logged_in() ) : 
            $is_verified = get_user_meta(get_user_id(), 'is_activated');
            if (! $is_verified) :
            ?>
            
            <h3 class="wpneo-center"><?php _e("We have sent you a verification e-mail. Please open it and click the link to verify your e-mail address.","wp-crowdfunding-registration"); ?></h3>

            <?php
            else : ?>

            <h3 class="wpneo-center"><?php _e("You are already logged in.","wp-crowdfunding-registration"); ?></h3>

            <?php
            endif; ?>

        <?php 
        else :
            global $reg_errors,$reg_success;
        
            if(isset($_GET['act'])){
                $data = unserialize(base64_decode($_GET['act']));
                $code = get_user_meta($data['id'], 'activation_code', true);

                // verify whether the code given is the same as ours
                if($code === $data['code']){
                    // update the user meta
                    update_user_meta($data['id'], 'is_activated', 1);
                    wc_add_notice( __( '<strong>Success:</strong> Your account has been activated! ', 'wp-crowdfunding-registration' )  );
                }
            }
          ?>
            <div class="wpneo-user-registration-wrap">
                <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="wpneo-registration" method="post">
                <?php echo wp_nonce_field( 'wpcf_form_action', 'wpcf_form_action_field', true, false ); ?>
                    <?php
                    $regisration_data = array(
                        array(
                            'id'            => 'fname',
                            'label'         => __( "First Name *" , "wp-crowdfunding" ),
                            'type'          => 'text',
                            'placeholder'   => __('Enter First Name', 'wp-crowdfunding'),
                            'value'         => '',
                            'class'         => 'required',
                            'warpclass'     => '',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'lname',
                            'label'         => __( "Last Name *" , "wp-crowdfunding" ),
                            'type'          => 'text',
                            'placeholder'   => __('Enter Last Name', 'wp-crowdfunding'),
                            'value'         => '',
                            'class'         => 'required',
                            'warpclass'     => 'wpneo-first-half',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'slname',
                            'label'         => __( "Second Last Name" , "wp-crowdfunding" ),
                            'type'          => 'text',
                            'placeholder'   => __('Enter Second Last Name', 'wp-crowdfunding'),
                            'value'         => '',
                            'class'         => '',
                            'warpclass'     => 'wpneo-second-half',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'phone',
                            'label'         => __( "Phone Number" , "wp-crowdfunding" ),
                            'type'          => 'text',
                            'placeholder'   => __('Enter Phone Number', 'wp-crowdfunding'),
                            'value'         => '',
                            'class'         => '',
                            'warpclass'     => 'wpneo-first-half',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'email',
                            'label'         => __( "Email *" , "wp-crowdfunding" ),
                            'type'          => 'text',
                            'placeholder'   => __('Enter Email', 'wp-crowdfunding'),
                            'value'         => '',
                            'warpclass'     => 'wpneo-second-half',
                            'class'         => 'required',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'password',
                            'label'         => __('Password *', 'wp-crowdfunding'),
                            'type'          => 'password',
                            'placeholder'   => __('Enter Password', 'wp-crowdfunding'),
                            'class'         => 'required',
                            'warpclass'     => '',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'rpassword',
                            'label'         => __( "Repeat Password *" , "wp-crowdfunding" ),
                            'type'          => 'password',
                            'placeholder'   => __('Repeat Password', 'wp-crowdfunding'),
                            'value'         => '',
                            'class'         => 'required',
                            'warpclass'     => '',
                            'autocomplete'  => 'off',
                        ),
                        array(
                            'id'            => 'terms_and_conditions',
                            'label'         => __( "I have read and accept the Terms & Conditions" , "wp-crowdfunding-registration" ),
                            'type'          => 'checkbox',
                            'value'         => '',
                            'warpclass'     => '',
                            'class'         => 'required',
                            'autocomplete'  => 'off',
                        ),
                    );
    
                    $regisration_meta = apply_filters('wpcf_user_registration_fields', $regisration_data );
    
                    foreach( $regisration_meta as $item ){ ?>
                        <div class="wpneo-single <?php echo (isset($item['warpclass'])? $item['warpclass'] : "" ); ?>">
                          
                            <?php if ($item['type'] === 'checkbox'): ?>
                            <div class="wpneo-name">
                              <input type="checkbox" id="<?php echo $item['id']; ?>" class="<?php echo $item['class']; ?>" name="<?php echo $item['id']; ?>">&nbsp;<?php echo $item['label']; ?>
                            </div>
                            <div class="wpneo-fields">&nbsp;</div>
                            <?php else: ?>
                            <div class="wpneo-name"><?php echo (isset($item['label'])? $item['label'] : "" ); ?></div>
                            <div class="wpneo-fields">
                                <?php
                                switch ($item['type']){
                                    case 'checkbox':
                                    echo '<label><input type="checkbox" id="'.$item['id'].'" class="'.$item['class'].'" name="'.$item['id'].'">&nbsp;'.$item['label'].'</label>';
                                        break;
                                    case 'text':
                                    echo '<input type="text" id="'.$item['id'].'" autocomplete="'.$item['autocomplete'].'" class="'.$item['class'].'" name="'.$item['id'].'" placeholder="'.$item['placeholder'].'">';
                                        break;
                                    case 'password':
                                    echo '<input type="password" id="'.$item['id'].'" autocomplete="'.$item['autocomplete'].'" class="'.$item['class'].'" name="'.$item['id'].'" placeholder="'.$item['placeholder'].'">';
                                        break;
                                    case 'textarea':
                                    echo '<textarea id="'.$item['id'].'" autocomplete="'.$item['autocomplete'].'" class="'.$item['class'].'" name="'.$item['id'].'" ></textarea>';
                                        break;
                                    case 'submit':
                                    echo '<input type="submit" id="'.$item['id'].'"  class="'.$item['class'].'" name="'.$item['id'].'" />';
                                        break;
                                    case 'shortcode':
                                    echo do_shortcode($item['shortcode']);
                                        break;
                                } ?>
                            </div>
                            <?php endif; ?>

                        </div>
                    <?php } ?>
    
                    <div class="wpneo-single wpneo-register">
                        <!--<a href="<?php echo get_home_url(); ?>" class="wpneo-cancel-campaign"><?php _e("Cancel","wp-crowdfunding"); ?></a>-->
                        <input type="hidden" name="action" value="wpcf_registration_custom" />
                        <input type="hidden" name="current_page" value="<?php echo get_the_permalink(); ?>" />
                        <input type="submit" class="wpneo-submit-campaign" id="user-registration-btn" value="<?php _e('Sign Up', 'wp-crowdfunding'); ?>" name="submits" />
                    </div>
    
                </form>
            </div>

            <section class="elementor-element elementor-element-12166f0 elementor-section-content-middle elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section" data-id="12166f0" data-element_type="section">
                <div class="elementor-container elementor-column-gap-default">
                    <div class="elementor-row">
                        <div class="elementor-element elementor-element-5c07036 elementor-column elementor-col-50 elementor-inner-column" data-id="5c07036" data-element_type="column">
                            <div class="elementor-column-wrap  elementor-element-populated">
                                <div class="elementor-widget-wrap">
                                    <div class="elementor-element elementor-element-a4dbd8f elementor-widget elementor-widget-text-editor" data-id="a4dbd8f" data-element_type="widget" data-widget_type="text-editor.default">
                                        <div class="elementor-widget-container">
                                            <div class="elementor-text-editor elementor-clearfix"><p>Already have an account?</p></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="elementor-element elementor-element-cdf342f elementor-column elementor-col-50 elementor-inner-column" data-id="cdf342f" data-element_type="column">
                            <div class="elementor-column-wrap  elementor-element-populated">
                                <div class="elementor-widget-wrap">
                                    <div class="elementor-element elementor-element-1fa3a3f elementor-align-left elementor-widget elementor-widget-button" data-id="1fa3a3f" data-element_type="widget" data-widget_type="button.default">
                                        <div class="elementor-widget-container">
                                            <div class="elementor-button-wrapper">
                                                <a href="/iniciar-sesion" class="elementor-button-link elementor-button elementor-size-sm" role="button" id="login-button">
                                                    <span class="elementor-button-content-wrapper">
                                                        <span class="elementor-button-text">Log in</span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
        endif;

        return ob_get_clean();
    }
    
    // register a new user
    public function registration_save_action() {
        if ( ! isset( $_POST['wpcf_form_action_field'] ) || ! wp_verify_nonce( $_POST['wpcf_form_action_field'], 'wpcf_form_action' ) ) {
            die(json_encode(array('success'=> 0, 'message' => __('Sorry, your status did not verify.', 'wp-crowdfunding'))));
            exit;
        }
    
        //Add some option
        do_action( 'wpcf_before_user_registration_action' );

        $fname = $lname = $slname = $password = $rpassword = $email = '';

        // sanitize user form input
        $password   =   sanitize_text_field($_POST['password']);
        $rpassword   =   sanitize_text_field($_POST['rpassword']);
        $email      =   sanitize_email($_POST['email']);
        $fname =   sanitize_text_field($_POST['fname']);
        $lname  =   sanitize_text_field($_POST['lname']);
        $slname  =   sanitize_text_field($_POST['slname']);
        $phone  =   sanitize_text_field($_POST['phone']);
        $terms  = sanitize_text_field($_POST['terms_and_conditions']);
        $this->custom_registration_validation( $fname , $lname, $slname, $password , $rpassword, $email, $phone, $terms );
        $this->custom_complete_registration( $fname , $lname, $slname, $password , $rpassword, $email, $phone, $terms );
    }
    
    public function custom_complete_registration( $fname , $lname, $slname, $password , $rpassword, $email, $phone, $terms ) {
        global $reg_errors;
        if ( count($reg_errors->get_error_messages()) < 1 ) {
            $login = strtolower($fname) . '-' . strtolower($lname) . rand(1, 10000);
            $userdata = array(
                'user_login'    =>  $login,
                'user_email'    =>  $email,
                'user_pass'     =>  $password,
                'user_url'      =>  '',
                'first_name'    =>  $fname,
                'last_name'     =>  $lname . ' ' . $slname,
                'nickname'      =>  $login,
                'description'   =>  $phone
            );
            $user_id = wp_insert_user( $userdata );
    
            //On success
            if ( !is_wp_error( $user_id ) ) {
                WC()->mailer(); // load email classes
                do_action( 'wpcf_after_registration', $user_id );
                do_action( 'cfreg_verification_email', $user_id );
    
                $saved_redirect_uri = get_option('wpcf_user_reg_success_redirect_uri');
                $redirect = $saved_redirect_uri ? $saved_redirect_uri : esc_url( home_url( '/iniciar-sesion' ) );
                die(json_encode(array('success'=> 1, 'message' => __('Registration complete.', 'wp-crowdfunding'), 'redirect' => $redirect )));
            } else {
                $errors = '';
                if ( is_wp_error( $reg_errors ) ) {
                    foreach ( $reg_errors->get_error_messages() as $error ) {
                        $errors .= '<strong>'.__('ERROR','wp-crowdfunding').'</strong>:'.$error.'<br />';
                    }
                }
                die(json_encode(array('success'=> 0, 'message' => $errors )));
            }
        } else {
            $errors = '';
            if ( is_wp_error( $reg_errors ) ) {
                foreach ( $reg_errors->get_error_messages() as $error ) {
                    $errors .= '<strong>'.__('ERROR','wp-crowdfunding').'</strong>:'.$error.'<br />';
                }
            }
            die(json_encode(array('success'=> 0, 'message' => $errors )));
        }
    }
    
    public function custom_registration_validation( $fname , $lname, $slname, $password , $rpassword, $email, $phone, $terms ) {
        global $reg_errors;
        $reg_errors = new \WP_Error;
    
        if ( empty( $rpassword ) || empty( $password ) || empty( $email ) || empty( $fname ) || empty( $lname ) ) {
            $reg_errors->add('field', __('Required form field is missing','wp-crowdfunding'));
        }
      
        if ( empty( $terms )) {
            $reg_errors->add('field', __('Please, accept the Terms & Conditions','wp-crowdfunding-registration'));
        }
    
        if ( strlen( $password ) < 6 ) {
            $reg_errors->add('password', __('Password length must be greater than 6','wp-crowdfunding'));
        }
        if ( $password !== $rpassword ) {
            $reg_errors->add('password', __('Password and password confirmation must be identical','wp-crowdfunding'));
        }
    
        if ( !is_email( $email ) ) {
            $reg_errors->add('email_invalid', __('Email is not valid','wp-crowdfunding'));
        }
    
        if ( email_exists( $email ) ) {
            $reg_errors->add('email', __('Email Already in use','wp-crowdfunding'));
        }
    }
  
    public function send_verification_email($user_id) {
        // get user data
        $user_info = get_userdata($user_id);
        // create md5 code to verify later
        $code = md5(time());
        // make it into a code to send it to user via email
        $string = array('id'=>$user_id, 'code'=>$code);
        // create the activation code and activation status
        update_user_meta($user_id, 'account_activated', 0);
        update_user_meta($user_id, 'activation_code', $code);
        // create the url
        $url = get_site_url(). '/registration-form/?act=' .base64_encode( serialize($string));
        // basically we will edit here to make this nicer
        $html = '
            Welcome to MundoRaiz!<br/><br/>
            Please click the following link to proceed with the verification of your e-mail address:
            <a href="'.$url.'">'.$url.'</a>
        ';

        // send an email out to user
        wp_mail( $user_info->user_email, __('Confirma su registracion con MundoRaiz','wp-crowdfunding-registration') , $html);
    }
}
