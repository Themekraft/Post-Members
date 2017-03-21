<?php

function tk_pm_list_member_posts( $atts ) {
	global $post;
	extract( shortcode_atts( array(
		'user_id'   => '',
		'post_type' => '',
	), $atts ) );


	$args      = array(
		'post_type' => $post_type,
		'tax_query' => array(
			array(
				'taxonomy' => 'tk_pm_relation',
				'field'    => 'slug',
				'terms'    => array( $user_id ),
			),
		),
	);
	$the_query = new WP_Query( $args );

	ob_start();
	if ( $the_query->have_posts() ) {
		echo '<ul>';
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			echo '<li>' . get_the_title() . '</li>';
		}
		echo '</ul>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		echo 'No Posts Found';
	}
	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'tk_pm_list_member_posts', 'tk_pm_list_member_posts' );


function tk_pm_get_list_members( $atts = array() ) {
	global $post;
    $column = "";
	if(!is_user_logged_in()){
		return;
	}

	extract( shortcode_atts( array(
		'post_id' => '',
        'column' => ''
	), $atts ) );


	if ( empty( $post_id ) ) {
		$post_id = $post->ID;
	}

	$post_members = get_post_meta( $post_id, '_tk_post_members', true );
	$tmp = '';
	if ( isset( $post_members ) && is_array( $post_members ) ) {
		ob_start(); ?>
        <?php if ($column == 'right') : ?><hr/>
        <h3><?php __('Registered company members', 'tk_pm' ); ?></h3><?php endif; ?>
		<ul class="tk-post-member-list">
			<?php foreach ( $post_members as $member ) {
				$user_data = get_userdata( $member );
                $bpUser = new BP_Core_User($user_data->ID);
                $bpUserProfile = $bpUser->get_profile_data();
				?>
				<li id="user-<?php echo $user_data->ID ?>"
				    class="select2-results__option select2-results__option--highlighted tk-post-member-item" role="treeitem"
				    aria-selected="false">
                    <?php if ($column != 'right') : ?><div class="row select2-result-user clearfix"><?php endif; ?>
						<?php if ($column != 'right') : ?><div class="col-md-3"><?php endif; ?>
                            <a href="<?php echo $bpUser->user_url; ?>">
                            <?php echo $bpUser->avatar; ?>
                            </a>
                        <?php if ($column != 'right') : ?></div>
						<div class="col-md-9"><?php endif; ?>

                            <?php
                            $anrede = '';
                            $fields = '';

                            foreach ($bpUserProfile as $key => $value) {

                                if($key == 'Name') $username = $value['field_data'];
                                else if($key == 'Anrede' || $key == 'Titel' || $key == 'Vorname' || $key == 'Nachname') $anrede .= $value['field_data'].' ';
                                else if($key == 'user_login' || $key == 'user_nicename' || $key == 'user_email' || $key == 'Nutzername') continue;
                                else if($key == 'Telefonnummer' || $key == 'Fax' || $key == 'Email' || $key == 'Mobil') $fields .= '<span class="da-data '.$key.'">'.$value['field_data'].'</span><br/>';

                                else $fields .= '<label class="da-small">'.$key.': </label> <span class="da-data">'.$value['field_data'].'</span><br/>';
                            }
                            echo '<h3 class="da-post-member-headline">'.$anrede.'</h3>';
                            if ($column != 'right') echo $fields;
                            ?>



							<p class="readmore">
                                    <a href="<?php echo $bpUser->user_url; ?>" data-id="<?php echo $user_data->ID ?>" class="">&gt; <?php __('Profile', 'tk_pm' ); ?></a>
							</p>
                <?php if ($column != 'right') : ?></div>
					</div><?php endif; ?>
					<input type="hidden" value="<?php echo $user_data->ID ?>" name="_tk_post_members[]">
                    <?php if ($column != 'right') : ?><hr/><?php endif; ?>
                </li>

				<?php
			}
			?></ul>
		<?php
		$tmp = ob_get_clean();
	}

	return $tmp;
}

add_shortcode( 'tk_pm_list_members', 'tk_pm_get_list_members' );



function tk_pm_get_list_members_intern( $atts = array() ) {

    $column = "";
    if(!is_user_logged_in()){
        return;
    }
    extract( shortcode_atts( array(
        'post_id' => '',
        'column' => ''
    ), $atts ) );

    global $post;
    $post_meta = get_post_meta($post->ID);
    if ( empty( $post_id ) ) {
        $post_id = $post->ID;
    }

    // Get author's display name
    $display_name = get_the_author_meta('display_name', $post->post_author);
    // If display name is not available then use nickname as display name
    if (empty($display_name)) {
        $display_name = get_the_author_meta('nickname', $post->post_author);
    }
    $bpUser = new BP_Core_User($post->post_author);
    $bpUserProfile = $bpUser->get_profile_data();
    // Get author's website URL


    // Den Author nicht ausgeben wenn er sich nicht vom Eintrag unterscheidet

    if( preg_replace('/[^A-Za-z0-9\-]/', '', html_entity_decode($bpUserProfile['Benutzername']['field_data'])) != preg_replace('/[^A-Za-z0-9\-]/', '', html_entity_decode($post->post_title))
        || $bpUserProfile['user_email'] != $post_meta['user_email'][0]) {
        $author_details = '<div class="row">';
        $author_details .= '<div class="col-lg-3 main_contact">';
        $author_details .= '<div class="author_avatar"><a href="' . bp_core_get_user_domain($post->post_author) . '">' . get_avatar(get_the_author_meta('user_email'), 246) . '</a></div>';

        $anrede = '';
        $fields = '';
        $username = '';
        $nameCheck = '';
        foreach ($bpUserProfile as $key => $value) {

            if ($value['field_id'] == 1) $username = $value['field_data'];
            else if ($value['field_id'] == 3 || $value['field_id'] == 4 || $value['field_id'] == 5) $anrede .= $value['field_data'] . ' ';
            else if ($key == 'user_login' || $key == 'user_nicename' || $key == 'user_email') continue;

            if ($value['field_id'] == 4 || $value['field_id'] == 5) $nameCheck .= $value['field_data'];

        }

        if (!empty($anrede) && $nameCheck != '') {
            $author_details .= '<h3 class="da-post-member-headline"> ' . $anrede . '</h3>';
        } else if (!empty($display_name)) {
            $author_details .= '<h3 class="da-post-member-headline"> ' . $display_name . '</h3>';
        } else
            $author_details .= '<h3 class="da-post-member-headline"> ' . $post->post_title . '</h3>';

        //print_r($bpUserProfile);
        if (!empty($bpUserProfile['Position im Unternehmen']['field_data']))
            $author_details .= '<label class="da-small">' . __('Position', 'tk_pm') . '</label><br/><span class="da-data">' . $bpUserProfile['Position im Unternehmen']['field_data'] . '</span><br/>';

        if (!empty($bpUserProfile['Aufgabenbereiche']['field_data']))
            $author_details .= '<label class="da-small">' . __('Function', 'tk_pm') . ':</label><br/><span class="da-data">' . $bpUserProfile['Aufgabenbereiche']['field_data'] . '</span><br/>';

        if (!empty($bpUserProfile['Telefonnummer']['field_data'])) {
            $author_details .= '<span class="author_data Telefonnummer"> ' . $bpUserProfile['Telefonnummer']['field_data'] . '</span>';
        } else if (!empty($display_tel)) {
            $author_details .= '<span class="author_data Telefonnummer"> ' . $display_tel . '</span>';
        } else if (!empty($post_meta['telefonnummer-geschaftlich'][0]))
            $author_details .= '<span class="author_data Telefonnummer"> ' . $post_meta['telefonnummer-geschaftlich'][0] . '</span>';

        if (!empty($bpUserProfile['Fax']['field_data'])) {
            $author_details .= '<span class="author_data Fax"> ' . $bpUserProfile['Fax']['field_data'] . '</span>';
        } else if (!empty($display_fax)) {
            $author_details .= '<span class="author_data Fax"> ' . $display_fax . '</span>';
        } else if (!empty($post_meta['fax-geschaftlich'][0]))
            $author_details .= '<span class="author_data Fax"> ' . $post_meta['fax-geschaftlich'][0] . '</span>';

        if (!empty($post_meta['Mobil'][0]))
            $author_details .= '<span class="author_data Mobil"> ' . $post_meta['Mobil'][0] . '</span>';


        if (!empty($bpUserProfile['Email']['field_data'])) {
            $author_details .= '<span class="author_data Email"><a href="mailto:' . $bpUserProfile['Email']['field_data'] . '">' . $bpUserProfile['Email']['field_data'] . '</a></span>';
        } else if (!empty($post_meta['user_email'][0]))
            $author_details .= '<span class="author_data Email"><a href="mailto:' . $post_meta['user_email'][0] . '">' . $post_meta['user_email'][0] . '</a></span>';
        $author_details .= '<p class="readmore"><a href="' . $bpUser->user_url . '" data-id="' . $post->ID . '" class="">&gt; '.__('Profile', 'tk_pm' ).'</a></p>';
        $author_details .= '</div>';

        // Den Counter für die spalten auf zwei setzen da schon ein Eintrag vorhanden
        $count = 2;
    } else {
        $author_details = '';
        $count = 1;
    }

    $post_members = get_post_meta( $post_id, '_tk_post_members', true );
    $tmp = '';

    if ( isset( $post_members ) && is_array( $post_members ) ) {
        ob_start(); ?>

            <?php foreach ( $post_members as $member ) {
                $user_data = get_userdata( $member );
                $bpUser = new BP_Core_User($user_data->ID);
                $bpUserProfile = $bpUser->get_profile_data();
                if($count%5 == 0) echo '<div class="row">';
                ?>
                    <div class="col-lg-3">
                        <div class="author_avatar"><a href="<?php echo $bpUser->user_url; ?>">
                            <?php echo $bpUser->avatar; ?>
                        </a></div>
                        <?php
                        $anrede = '';
                        $fields = '';
                        $mail = '';
                        foreach ($bpUserProfile as $key => $value) {
                            if($key == 'Titel' || $key == 'Vorname' || $key == 'Nachname') $anrede .= $value['field_data'].' ';
                            else if($key == 'Anrede' ||
                                    $key == 'user_login' ||
                                    $key == 'user_nicename' ||
                                    $key == 'user_email' ||
                                    $key == 'Nutzername' ||
                                    $key == 'Benutzername' ||
                                    $key == 'Unternehmen') continue;
                            else if($key == 'Telefonnummer' || $key == 'Fax' || $key == 'Mobil') $fields .= '<span class="author_data '.$key.'">'.$value['field_data'].'</span>';
                            else if($key == 'Email')  $mail = '<span class="author_data '.$key.'"><a href="mailto:'.$value['field_data'].'">'.$value['field_data'].'</a></span>';

                            else $fields .= '<label class="da-small">'.$key.': </label><br/><span class="da-data">'.$value['field_data'].'</span><br/>';
                        }
                        echo '<h3 class="da-post-member-headline">'.$anrede.'</h3>';
                        echo $fields.$mail;
                        ?>
                        <p class="readmore">
                            <a href="<?php echo $bpUser->user_url; ?>" data-id="<?php echo $user_data->ID ?>" class="">&gt; <?php __('Profile', 'tk_pm' ); ?></a>
                        </p>
                    </div>

            <?php
            if($count%4 == 0) echo '<!-- row zu--></div>';
            $count++;

            } ?>
        <?php
        $tmp = ob_get_clean();
    }
    $returnStr = '';
    if($author_details != '' || $tmp != '') {
        $returnStr .= '<div class="companyMembers"><div class="showCompanyMembers readmore">'.__('Registered company members', 'tk_pm' ).'</div>';
        $returnStr .= '<div class="companyMembersInner da-hidden">';
        $returnStr .= $author_details.$tmp.'<!-- row zu--></div>';
        $returnStr .= '</div></div>';
    }

    return $returnStr;
}

add_shortcode( 'tk_pm_list_members_intern', 'tk_pm_get_list_members_intern' );