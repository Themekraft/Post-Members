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

	if(!is_user_logged_in()){
		return;
	}

	extract( shortcode_atts( array(
		'post_id' => '',
	), $atts ) );


	if ( empty( $post_id ) ) {
		$post_id = $post->ID;
	}



	$post_members = get_post_meta( $post_id, '_tk_post_members', true );
	$tmp = '';
	if ( isset( $post_members ) && is_array( $post_members ) ) {
		ob_start(); ?>
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
					<div class="select2-result-user clearfix">
						<div class="select2-result-user__avatar">
                            <a href="<?php echo $bpUser->user_url; ?>">
                            <?php echo $bpUser->avatar; ?>
                            </a>
                        </div>
						<div class="select2-result-user__meta">
							<!--<div class="select2-result-user__display_name"><?php //echo $user_data->display_name ?></div>-->

                            <?php
                            $anrede = '';
                            $fields = '';
                            //print_r($bpUserProfile);
                            foreach ($bpUserProfile as $key => $value) {
                                //if(is_array($value)) echo $value['field_data'].'<br/>';
                                //echo $key.': '.$value.'<br/>';
                                if($key == 'Name') $username = $value['field_data'];
                                else if($key == 'Anrede' || $key == 'Titel' || $key == 'Vorname' || $key == 'Nachname') $anrede .= $value['field_data'].' ';
                                else if($key == 'user_login' || $key == 'user_nicename' || $key == 'user_email') continue;
                                else $fields .= $key.': '.$value['field_data'].'<br/>';
                                //echo '<div class="select2-result-user__display">'.$field['field_data'].'</div>';
                            }
                            echo '<h3>'.$anrede.'</h3>';
                            echo 'Nutzername: <a href="'.$bpUser->user_url.'">'.$username.'</a><br/>';
                            echo $fields;
                            ?>

                            <!--<div class="select2-result-user__user_position"><?php echo $bpUserProfile['Aufgabenbereiche']['field_data'] ?></div>
							<div class="select2-result-user__user_email"><?php echo $user_data->user_email ?></div>-->

							<div class="select2-result-user__actions">
								<div class="select2-result-user__add">
                                    <!-- /nutzerverzeichnis/daadmin/messages/compose/?r=inforiesatsbaucom-2-2-2-2 -->
                                    <a href="<?php echo $bpUser->user_url; ?>" data-id="<?php echo $user_data->ID ?>" class="tk-pm-remove-member">&gt; Profil</a>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" value="<?php echo $user_data->ID ?>" name="_tk_post_members[]">
                <hr/>
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