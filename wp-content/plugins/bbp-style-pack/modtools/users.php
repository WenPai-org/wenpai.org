<?php
class bspbbPressModToolsPlugin_Users {

	public static function init() {

		$self = new self();
		
		add_filter( 'bbp_get_dynamic_roles', array( $self, 'add_senior_moderator_role' ), 1 );

	}

	public function add_senior_moderator_role( $bbp_roles ) {

		$bbp_roles['bbp_senior_moderator'] = array(
			'name' => 'Senior Moderator',
			'capabilities' => bbp_get_caps_for_role( bbp_get_moderator_role() )
		);
		
		return $bbp_roles;

	}

}

bspbbPressModToolsPlugin_Users::init();
