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
        <h3>Angemeldete Firmenmitglieder</h3><?php endif; ?>
		<ul class="tk-post-member-list">
			<?php foreach ( $post_members as $member ) {
				$user_data = get_userdata( $member );
                $bpUser = new BP_Core_User($user_data->ID);
                $bpUserProfile = $bpUser->get_profile_data();
                //print_r($bpUserProfile);
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
                            $username = '';
                            //print_r($bpUserProfile);
                            foreach ($bpUserProfile as $key => $value) {

                                if($key == 'Name') $username = $value['field_data'];
                                else if($key == 'Anrede' || $key == 'Titel' || $key == 'Vorname' || $key == 'Nachname') $anrede .= $value['field_data'].' ';
                                else if($key == 'user_login' || $key == 'user_nicename' || $key == 'user_email' || $key == 'Nutzername') continue;
                                else if($key == 'Telefonnummer' || $key == 'Fax' || $key == 'Email' || $key == 'Mobil') $fields .= '<span class="da-data '.$key.'">'.$value['field_data'].'</span><br/>';

                                else $fields .= '<label class="da-small">'.$key.': </label> <span class="da-data">'.$value['field_data'].'</span><br/>';
                            }
                            echo '<h3 class="da-post-member-headline">'.$anrede.'</h3>';
                            //echo 'Nutzername: '.$username.'<br/>';
                            if ($column != 'right') echo $fields;
                            ?>

                            <!--<div class="select2-result-user__user_position"><?php echo $bpUserProfile['Aufgabenbereiche']['field_data'] ?></div>
							<div class="select2-result-user__user_email"><?php echo $user_data->user_email ?></div>-->

							<p class="readmore">
                                    <a href="<?php echo $bpUser->user_url; ?>" data-id="<?php echo $user_data->ID ?>" class="">&gt; Profil</a>
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