<?php

/* =============================================================================
   Process form post data
   ========================================================================== */

function theme_contactform_handler() {
    if( strtolower($_SERVER['REQUEST_METHOD'])=='post' && isset($_POST['cfd']) && $_POST['cfd']=='1' ) {

        // success
        $cfs = false;
        // errors
        $cfe = array();

        // contact form data
        $cfd = array(
            'name'     => isset($_POST['cfd_name'])    ? trim($_POST['cfd_name'])    : '',
            'email'    => isset($_POST['cfd_email'])   ? trim($_POST['cfd_email'])   : '',
            'phone'    => isset($_POST['cfd_phone'])   ? trim($_POST['cfd_phone'])   : '',
            'subject'  => isset($_POST['cfd_subject']) ? trim($_POST['cfd_subject']) : '',
            'msg'      => isset($_POST['cfd_msg'])     ? trim($_POST['cfd_msg'])     : '',
            'sendcopy' => isset($_POST['cfd_sendcopy']) && $_POST['cfd_sendcopy']=='1' ? true : false,
            'test'     => isset($_POST['cfd_test']) ? trim($_POST['cfd_test']) : false
        );

        $str_filter = array( &$cfd['name'], &$cfd['phone'], &$cfd['subject'] );
        foreach( $str_filter as $k => $v )
            $str_filter[$k] = trim(preg_replace( '/[\n\r,;]+/', '', $v ));

        if( !$cfd['test'] && !empty($cfd['name']) && is_email($cfd['email']) && !empty($cfd['msg']) ) {
            $site_name = parse_url(get_permalink(),PHP_URL_HOST);
            $site_url = get_permalink();
            $recipient = $site_name." <".get_bloginfo('admin_email').">";
            $sender = $cfd['name']." <".$cfd['email'].">";
            $subject = '['.$site_name.'] '.( !empty($cfd['subject']) ? utf8_decode($cfd['subject']) : '(kein Betreff)' );
            $header = "From: ".$sender.
                "\nReply-To: ".$sender.
                ( $cfd['sendcopy'] ? "\nCC: ".$sender : '' ).
                "\nX-Mailer: ".$site_name." (".$site_url.") [PHP ".phpversion()."]".
                "\nX-Priority: 3 (Normal)".
                "\nMIME-Version: 1.0".
                "\nContent-Type: text/plain; charset=iso-8859-1".
                "\nContent-Transfer-Encoding: 8bit".
                "\n\n".utf8_decode($cfd['msg']).
                "\n\n--".
                "\nName: ".utf8_decode($cfd['name']).
                "\nE-Mail: ".utf8_decode($cfd['email']).
                ( !empty($cfd['phone']) ? "\nTelefon: ".utf8_decode($cfd['phone']) : '' );
            // if( mail( $recipient, $subject, '', $header ) )
            if( true )
                $cfs = '<strong>Deine Nachricht wurde erfolgreich gesendet.</strong>'.( $cfd['sendcopy'] ? '<br>Eine Kopie wurde an dein Postfach gesendet.' : '' );
            else
                array_push( $cfe, array( '#', '<strong>Es ist ein unerwarteter Fehler aufgetreten.</strong><br>Die Nachricht konnte leider nicht gesendet werden.' ) );
        }
        else {
            if( empty($cfd['name']) && ( !$cfd['test'] || $cfd['test']=='cfd_name' ) )
                array_push( $cfe, array( 'name', 'Wie lautet dein Name?' ) );
            if( empty($cfd['email']) && ( !$cfd['test'] || $cfd['test']=='cfd_email' ) )
                array_push( $cfe, array( 'email', 'Wie lautet deine E-Mailadresse?' ) );
            elseif( !is_email($cfd['email']) && ( !$cfd['test'] || $cfd['test']=='cfd_email' ) )
                array_push( $cfe, array( 'email', 'Das ist keine E-Mailadresse.' ) );
            if( empty($cfd['msg']) && ( !$cfd['test'] || $cfd['test']=='cfd_msg' ) )
                array_push( $cfe, array( 'msg', 'Was m&ouml;chtest du mitteilen?' ) );
        }

        $json = array(
            'success' => $cfs,
            'errors' => $cfe,
            'test' => $cfd['test']
        );
        header("Content-type: text/plain");
        echo json_encode($json);
        die();
    }
}
add_action( 'wp', 'theme_contactform_handler' );

/* =============================================================================
   SHORTCODE: Contact Form
   ========================================================================== */

function theme_shortcode_contactform( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'subject' => '',
        'class' => ''
    ), $atts ) );
    $form_url = get_home_url('/');
    $form_class = 'form-contact '.( $class!=='' ? ' '.$class : '' );
    $html = <<<EOT
<form action="$form_url" method="post" role="form" class="$form_class">
    <div class="row">
        <div class="col col-sm-6">
            <div class="form-group">
                <label for="cfd_name">Dein Name</label>
                <input type="text" class="form-control" name="cfd_name" id="cfd_name" placeholder="Dein Name" aria-required="true" value="">
            </div>
        </div>
        <div class="col col-sm-6">
            <div class="form-group">
                <label for="cfd_email">Deine E-Mailadresse</label>
                <input type="email" class="form-control" name="cfd_email" id="cfd_email" placeholder="Deine E-Mailadresse" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-sm-6">
            <div class="form-group">
                <label for="cfd_subject">Betreff</label>
                <input type="text" class="form-control" name="cfd_subject" id="cfd_subject" placeholder="Betreff" value="">
            </div>
        </div>
        <div class="col col-sm-6">
            <div class="form-group">
                <label for="cfd_phone">Deine Telefonnummer</label>
                <input type="tel" class="form-control" name="cfd_phone" id="cfd_phone" placeholder="Deine Telefonnummer" value="">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="cfd_msg">Deine Nachricht</label>
        <textarea class="form-control" rows="5" name="cfd_msg" id="cfd_msg" placeholder="Deine Nachricht"></textarea>
    </div>
    <div class="row">
        <div class="col col-sm-6 col-sm-push-6">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="cfd_sendcopy" id="cfd_sendcopy" checked="checked" value="1"> eine Kopie an dein Postfach senden
                    </label>
                </div>
            </div>
        </div>
        <div class="col col-sm-6 col-sm-pull-6">
            <div class="form-group has-feedback">
                <button type="submit" class="btn btn-transparent-black" name="cfd_submit" id="cfd_submit"><span class="fa fa-send"></span>E-Mail senden</button>
                <span class="loading-indicator"></span>
            </div>
        </div>
    </div>
</form>
EOT;
    return $html;
}
add_shortcode( 'contactform', 'theme_shortcode_contactform' );
