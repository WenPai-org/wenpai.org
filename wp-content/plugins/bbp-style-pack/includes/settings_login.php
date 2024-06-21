<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


//login settings page

function bsp_login_settings() {
 ?>
			
	<h3>
		<?php _e( 'Login options' , 'bbp-style-pack' ) ; ?>
	</h3>
	<p>
		<?php _e( 'There are many ways to get users logged into forums.' , 'bbp-style-pack' ) ; ?>
	</p>
	<p/>
	<p>
		<?php _e( 'a. Remember bbPress just uses the wordpress login, so any plugin that does wordpress login will also do bbPress.There are lots out there go to' , 'bbp-style-pack' ) ; ?>
		<a href="https://wordpress.org/plugins/">https://wordpress.org/plugins</a>
	</p>
	<p>
		<?php _e( 'b. Within bbPress you can use the sidebar login widget Dashboard>appearance>widgets and look for (bbpress) Login widget' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e( 'c. Or use a page or post to display the widget using the shortcode  [bbp-login]' , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
		<?php _e( 'd. The following adds a simple login/logout to your menu and/or Register/Edit Profile' , 'bbp-style-pack' ) ; ?>
	</p>
	<p> 
		<?php _e( "This can the give you the combination of 'login/Register' and 'logout/edit profile' as a menu display, which means users only see relevant items" , 'bbp-style-pack' ) ; ?>
	</p>
	<p>
	</p>
        
	<?php 
	global $bsp_login ;
	//get menu locations
	$menu_locations = get_nav_menu_locations();
        $menus = get_terms( 'nav_menu' );
        $no_menus_text = sprintf(
                /* translators: %1$s is an opening a href HTML tag, %2$s is a closing a href HTML tag */
                __( 'No menus currently defined! If your theme supports nav-menus, you can setup them up in the %1$sAppearance > Menus%2$s page.', 'bbp-style-pack' ),
                '<a href="' . admin_url( '/nav-menus.php' ) . '" target="_blank">',
                '</a>'
        );
        $primary_menu_text = __( 'Primary Menu', 'bbp-style-pack' );
        $activate_text = __( 'Click to activate' , 'bbp-style-pack' );
	?>
        
	<form method="post" action="options.php">
	<?php wp_nonce_field( 'login', 'login-nonce' ) ?>
	<?php settings_fields( 'bsp_login' ); 
        bsp_clear_cache();
	if (!empty($bsp_login['update448'])) echo '<input type="hidden" id="bsp_login[update448]" name="bsp_login[update448]" value="1">' ; 
	?>
        <table class="form-table">
		<tr>
			<th style="text-align:center">
				<?php _e( 'Not Logged in' , 'bbp-style-pack' ) ; ?>
			</th>
			<th style="text-align:center">
				<?php _e( 'Logged in ' , 'bbp-style-pack' ) ; ?>
			</th>
		</tr>
		<tr>
		<td>
		<strong>
		<?php _e( 'Menu Items' , 'bbp-style-pack' ) ; ?>
		</strong>
		</td>
		</tr>
		<tr>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/logina.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/loginb.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
		</tr>
		<tr valign="top">
		</tr>
		<tr>
			<td>
				<strong>
				<?php _e( 'Wordpress Toolbar' , 'bbp-style-pack' ) ; ?>
				</strong>
			</td>
			<td>
				<?php echo '<img src="' . plugins_url( 'images/profile3.JPG',dirname(__FILE__)  ) . '"  > '; ?>
			</td>
		</tr>
	</table>
	
	
	
	<!-- save the options -->
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
	</p>
	<table class="form-table">

            
<!--Click to add login/logout---------------------------------------------------------------------->
		<tr>
			<th colspan="2">1. 
				<?php _e( 'Add login/logout to menu items' , 'bbp-style-pack' ) ; ?>
			</th>
			<?php
			$name = 'Add login/logout to menu items' ;
			$name1 = __('Login menu item description', 'bbp-style-pack') ;
			$name2 = __('Logout menu item description', 'bbp-style-pack') ;
			$name3 = __('Login menu item css class', 'bbp-style-pack') ;
			$name4 = __('Logout menu item css class', 'bbp-style-pack') ;
			$area1='login' ;
			$area2='logout' ;
			$area3='logincss' ;
			$area4='logoutcss' ;
			$item1="bsp_login[".$name.$area1."]" ;
			$item2="bsp_login[".$name.$area2."]" ;
			$item3="bsp_login[".$name.$area3."]" ;
			$item4="bsp_login[".$name.$area4."]" ;
			$value1 = (!empty($bsp_login[$name.$area1] ) ? $bsp_login[$name.$area1]  : '') ;
			$value2 = (!empty($bsp_login[$name.$area2] ) ? $bsp_login[$name.$area2]  : '') ;
			$value3 = (!empty($bsp_login[$name.$area3] ) ? $bsp_login[$name.$area3]  : '') ;
			$value4 = (!empty($bsp_login[$name.$area4] ) ? $bsp_login[$name.$area4]  : '') ;
			$item =  'bsp_login[add_login]' ;
			$item5 = (!empty($bsp_login['add_login']) ? $bsp_login['add_login'] : '') ;
			?>
		
			<td>
			<?php
			echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item5, false ) . ' />';
			echo $activate_text;
  			?>
		</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name1 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default "Login" Enter the words you want on the menu item eg "log in", "sign in" etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name3 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'If you need a custom css class enter this here - if you do not understand this, then just leave it blank!', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td>
				<?php echo $name2 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
				<label class="description"><?php _e( 'Default "Logout" Enter the words you want on the menu item eg "Log out", "Sign out" etc.', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
		
		<tr>
			<td>
			</td>
			<td> 
				<?php echo $name4 ; ?>
			</td>
			<td>
				<?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4).'"<br>' ; ?> 
				<label class="description"><?php _e( 'If you need a custom css class enter this here - if you do not understand this, then just leave it blank!', 'bbp-style-pack' ); ?></label><br/>
			</td>
		</tr>
	
	
<!--only show on bbpress pages ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2">
                                <?php _e( 'You can opt to only show these menu items on forum pages' , 'bbp-style-pack' ) ; ?>
                        </th>

                        <?php
                                $item =  'bsp_login[only_bbpress]' ;
                                $item1 = (!empty($bsp_login['only_bbpress']) ? $bsp_login['only_bbpress'] : '');
                        ?>
                        <td colspan="2">
                                <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                _e('Only show on forum pages' , 'bbp-style-pack' ) ;
                                ?>
                        </td>
                </tr>

	
<!--which menus? ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2">
                                <?php _e('Select which menus to show these items on :' , 'bbp-style-pack' ) ; ?>
                        </th>
                        <td colspan="2">
                        <?php 
                        if ( empty( $menus ) ) {
                                echo $no_menus_text;
                        } else {
                                foreach( $menus as $menu ) {
                                        $item =  'bsp_login[login_'.$menu->name.']';
                                        $name= 'login_'.$menu->name ;
                                        $item1 = ( ! empty( $bsp_login[$name] ) ? $bsp_login[$name] : '' );
                                        echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                        echo 'Show on '.$menu->name;
                                        if ( ! empty( $menu_locations ) && isset( $menu_locations['primary'] ) ) {
                                                if ( $menu_locations['primary'] == $menu->term_id ) echo ' (' . $primary_menu_text . ')';
                                        }
                                }
                        }
                        ?>
                        </td>
                </tr>

                
<!--login details ---------------------------------------------------------------------->
                <tr>
                        <?php 
                        $name = 'Login/logout';
                        $desc = __('Login/logout' , 'bbp-style-pack' );
                        $name1 = __('Login page', 'bbp-style-pack');
                        $name2 = __('Logout page', 'bbp-style-pack');
                        $name3 = __('Logged in text', 'bbp-style-pack');
                        $name4 = __('Logged in redirect' , 'bbp-style-pack');
                        $area1 ='Login page';
                        $area2 ='Logout page';
                        $area3 ='Logged in text';
                        $area4 ='Logged in redirect';
                        $item1 ="bsp_login[".$name.$area1."]";
                        $item2 ="bsp_login[".$name.$area2."]";
                        $item3 ="bsp_login[".$name.$area3."]";
                        $item4 ="bsp_login[".$name.$area4."]";
                        $value1 = ( ! empty( $bsp_login[$name.$area1] ) ? $bsp_login[$name.$area1]  : '' );
                        $value2 = ( ! empty( $bsp_login[$name.$area2] ) ? $bsp_login[$name.$area2]  : '' );
                        $value3 = ( ! empty( $bsp_login[$name.$area3] ) ? $bsp_login[$name.$area3]  : '' );
                        $value4 = ( ! empty( $bsp_login[$name.$area4] ) ? $bsp_login[$name.$area4]  : '' );
                        ?>

                        <th>
                                <?php echo '2.'. $desc ?>
                        </th>
                        <td>
                                <?php echo $name1; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'You should create a wordpress page with a login shortcode such as [bbp-login] and put the full url in here e.g. http://www.mysite.com/loginpage. If left blank the default wordpress login page will be used.', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php echo $name2; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'This will be the url of the page you want users sent to on logout. For example this might be the home page or forums page http://www.mysite.com/home or http://www.mysite.com/forums', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td> 
                                <?php echo $name3; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'If you are using [bbp-login], then users will get "you are already logged in" once they log in.  Maybe change this to something nicer such as "You are currently logged in" ', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr> 
                        <td>
                        </td>
                        <td>
                        <?php _e('OR' , 'bbp-style-pack' ) ; ?>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td> 
                                <?php echo $name4; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item4.'" class="large-text" name="'.$item4.'" type="text" value="'.esc_html( $value4 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'If you are using [bbp-login], then if you enter a full url of a page, users will be redirected to that page instead of getting the "you are already logged in" above', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                
<!--show register to non-logged on users ---------------------------------------------------------------------->			
                <tr>
                        <th colspan="2">3. 
                                <?php _e( "Show a menu 'Register' item to non-logged in users" , 'bbp-style-pack' ) ; ?>
                        </th>

                        <?php
                        $item =  'bsp_login[register]' ;
                        $item1 = (!empty($bsp_login['register']) ? $bsp_login['register'] : '');
                        ?>
                        <td colspan="2">
                        <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' /> ';
                                echo $activate_text;
                        ?>
                        </td>
                </tr>

                
<!--register only show on bbpress pages ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2"> 
                                <?php _e( 'You can opt to only show register menu item on forum pages' , 'bbp-style-pack' ) ; ?>
                        </th>

                        <?php
                                $item =  'bsp_login[register_only_bbpress]' ;
                                $item1 = (!empty($bsp_login['register_only_bbpress']) ? $bsp_login['register_only_bbpress'] : '');
                        ?>
                        <td colspan="2">
                        <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                _e( 'Only show on forum pages' , 'bbp-style-pack' ) ;
                        ?>
                        </td>
                </tr>
	
                
<!--which menus? ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2">
                                <?php _e( 'Select which menus to show register on :' , 'bbp-style-pack' ) ; ?>
                        </th>
                        <td colspan="2">
                        <?php 
                        if ( empty( $menus ) ) {
                                echo $no_menus_text;
                        } else {
                                foreach($menus as $menu){
                                        $item =  'bsp_login[register_'.$menu->name.']' ;
                                        $name= 'register_'.$menu->name ;
                                        $item1 = (!empty($bsp_login[$name]) ? $bsp_login[$name] : '');
                                        echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                        echo 'Show on '.$menu->name ;
                                        if ( ! empty( $menu_locations ) && isset( $menu_locations['primary'] ) ) {
                                                if ( $menu_locations['primary'] == $menu->term_id ) echo ' (' . $primary_menu_text . ')';
                                        }
                                }
                        }
                        ?>
                        </td>
                </tr>
	
		
<!--register page ---------------------------------------------------------------------->
                <tr>
                        <?php 
                        $name = 'Register Page' ;
                        $desc = __('Register Page' , 'bbp-style-pack') ;
                        $name1 = __('Register page', 'bbp-style-pack') ;
                        $name2 = __('Menu Item Description', 'bbp-style-pack') ;
                        $name3 = __('Register item css class', 'bbp-style-pack') ;
                        $area1='Register page' ;
                        $area2='Menu Item Description' ;
                        $area3='css' ;
                        $item1="bsp_login[".$name.$area1."]" ;
                        $item2="bsp_login[".$name.$area2."]" ;
                        $item3="bsp_login[".$name.$area3."]" ;
                        $value1 = (!empty($bsp_login[$name.$area1] ) ? $bsp_login[$name.$area1]  : '') ;
                        $value2 = (!empty($bsp_login[$name.$area2] ) ? $bsp_login[$name.$area2]  : '') ;
                        $value3 = (!empty($bsp_login[$name.$area3] ) ? $bsp_login[$name.$area3]  : '') ;
                        ?>

                        <th>
                                <?php echo '4.'. $desc ?>
                        </th>
                        <td>
                                <?php echo $name1 ; ?> 
                        </td>
                        <td>
                                <?php echo '<input id="'.$item1.'" class="large-text" name="'.$item1.'" type="text" value="'.esc_html( $value1 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'You should create a wordpress page with a register shortcode such as [bbp-register] and put the full url in here e.g. http://www.mysite.com/loginpage. If left blank the default wordpress login page will be used.', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td> 
                                <?php echo $name2 ; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'Default "Register" Enter the words you want on the menu item eg "Sign-up", "Join this group" etc.', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td> 
                                <?php echo $name3 ; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'If you need a custom css class enter this here - if you do not understand this, then just leave it blank!', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>
				
			
<!--show edit profile to logged on users ---------------------------------------------------------------------->			
                <tr>
                        <th colspan="2">5. 
                                <?php _e( "Show a menu 'Edit profile' item to logged in users" , 'bbp-style-pack' ) ; ?></th>
                        </th>

                        <?php
                                $item =  'bsp_login[edit_profile]' ;
                                $item1 = (!empty($bsp_login['edit_profile']) ? $bsp_login['edit_profile'] : '');
                        ?>
                        <td colspan="2">
                                <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
                                echo $activate_text;
                                ?>
                        </td>
                </tr>
	
<!--show profile rather than edit profile---------------------------------------------------------------------->
                <tr>
                        <th colspan="2"> 
                                <?php _e( 'Link to user profile rather than edit profile' , 'bbp-style-pack' ) ; ?>
                        </th>

                        <?php
                                $item =  'bsp_login[profile_not_edit]' ;
                                $item1 = (!empty($bsp_login['profile_not_edit']) ? $bsp_login['profile_not_edit'] : '');
                        ?>
                        <td colspan="2">
                        <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                _e( 'Click to link to the users profile rather than the edit section of user profile' , 'bbp-style-pack' ) ;
                        ?>
                        </td>
                </tr>
	
<!--profile only show on bbpress pages ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2"> 
                                <?php _e( 'You can opt to only show profile menu item on forum pages' , 'bbp-style-pack' ) ; ?>
                        </th>

                        <?php
                                $item =  'bsp_login[profile_only_bbpress]' ;
                                $item1 = (!empty($bsp_login['profile_only_bbpress']) ? $bsp_login['profile_only_bbpress'] : '');
                        ?>
                        <td colspan="2">
                        <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                _e( 'Only show on forum pages' , 'bbp-style-pack' ) ;
                        ?>
                        </td>
                </tr>


<!--which menus? ---------------------------------------------------------------------->
                <tr>
                        <th colspan="2">
                                <?php _e( 'Select which menus to show Profile on :' , 'bbp-style-pack' ) ; ?>
                        </th>
                        <td colspan="2">
                        <?php 
                        if ( empty( $menus ) ) {
                                echo $no_menus_text;
                        } else {
                                foreach ( $menus as $menu ) {
                                        $item =  'bsp_login[profile_'.$menu->name.']';
                                        $name = 'profile_'.$menu->name;
                                        $item1 = ( ! empty( $bsp_login[$name] ) ? $bsp_login[$name] : '');
                                        echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />' ;
                                        echo 'Show on '.$menu->name ;
                                        if ( ! empty( $menu_locations ) && isset( $menu_locations['primary'] ) ) {
                                                if ( $menu_locations['primary'] == $menu->term_id ) echo ' (' . $primary_menu_text . ')';
                                        }
                                }
                        }
                        ?>
                        </td>
                </tr>

	
	<!--'edit profile' Description ---------------------------------------------------------------------->
                <tr>
                        <?php 
                        $name = 'edit profile' ;
                        $name2 = __('Menu Item Description', 'bbp-style-pack') ;
                        $name3 = __('Profile item css class', 'bbp-style-pack') ;
                        $area2='Menu Item Description' ;
                        $area3='css' ;
                        $item2="bsp_login[".$name.$area2."]" ;
                        $item3="bsp_login[".$name.$area3."]" ;
                        $value2 = (!empty($bsp_login[$name.$area2] ) ? $bsp_login[$name.$area2]  : '') ;
                        $value3 = (!empty($bsp_login[$name.$area3] ) ? $bsp_login[$name.$area3]  : '') ;
                        ?>
                        <td>
                        </td>
                        <td>
                                <?php echo $name2 ; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item2.'" class="large-text" name="'.$item2.'" type="text" value="'.esc_html( $value2 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'Default "Edit Profile" Enter the words you want on the menu item eg "Edit your Profile", "Change your settings",  etc. ', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <td>
                        </td>
                        <td>
                                <?php echo $name3 ; ?>
                        </td>
                        <td>
                                <?php echo '<input id="'.$item3.'" class="large-text" name="'.$item3.'" type="text" value="'.esc_html( $value3 ).'"<br>' ; ?> 
                                <label class="description"><?php _e( 'If you need a custom css class enter this here - if you do not understand this, then just leave it blank!', 'bbp-style-pack' ); ?></label><br/>
                        </td>
                </tr>

                <tr>
                        <th colspan="2">6. 
                                <?php _e( "Amend to make toolbar profile go to bbpress profile" , 'bbp-style-pack' ) ; ?></th>
                        </th>

                        <?php
                                $item =  'bsp_login[toolbar_profile]' ;
                                $item1 = (!empty($bsp_login['toolbar_profile']) ? $bsp_login['toolbar_profile'] : '');
                        ?>
                        <td colspan="2">
                                <?php
                                echo '<input name="'.$item.'" id="'.$item.'" type="checkbox" value="1" class="code" ' . checked( 1,$item1, false ) . ' />';
                                echo $activate_text;
                                echo '<br>' ;
                                _e( 'If users are allowed to see the toolbar, then activating this takes them to the bbpress edit profile, replacing the wordpress one' , 'bbp-style-pack' ) ;
                                echo '<br>' ;
                                _e( 'Default wording : Edit Profile, but the Menu Item Description from item 5 above will be used if set' , 'bbp-style-pack' ) ;

                                ?>
                        </td>
                </tr>

                
        </table>
<!-- save the options -->
        <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'bbp-style-pack' ); ?>" />
        </p>
        </form>

<?php
}
