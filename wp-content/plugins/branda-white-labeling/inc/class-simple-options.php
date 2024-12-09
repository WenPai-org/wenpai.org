<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Simple_Options' ) ) {

	class Simple_Options {

		/**
		 * Are some modules loaded? Helper semaphore array.
		 *
		 * @since 2.1.0
		 */
		private $loaded = array();

		/**
		 * Is Network Admin
		 *
		 * @since 3.0.0
		 */
		private $is_network = false;

		public function __construct() {
			global $branda_network;
			$this->is_network = $branda_network;
			/**
			 * Handle AJAX calls
			 */
			add_action( 'wp_ajax_simple_option', array( $this, 'ajax' ) );
			add_action( 'wp_ajax_simple_option_reset_section', array( $this, 'ajax_reset_section' ) );
		}

		/**
		 * Build options boxes.
		 *
		 * @param array  $options Options.
		 * @param array  $input Data.
		 * @param string $module Current module name.
		 */
		public function build_options( $options, $input, $module ) {
			if ( empty( $options ) ) {
				return;
			}
			$content       = '';
			$section_title = '';
			$content_tabs  = array();
			$show_as       = 'default';
			// If sub sections are being used.
			$section_inside = false;
			/**
			 * Prefetch masters value
			 *
			 * @since 2.0.0
			 */
			$masters = array();
			foreach ( $options as $section_key => $option ) {
				if ( ! isset( $option['fields'] ) || empty( $option['fields'] ) ) {
					continue;
				}
				$masters[ $section_key ] = array();
				foreach ( $option['fields'] as $id => $data ) {
					if ( isset( $data['slave-class'] ) ) {
						$value                           = array(
							'value'       => $this->get_single_value( $options, $input, $section_key, $id ),
							'slave-class' => $data['slave-class'],
							'type'        => isset( $data['type'] ) ? $data['type'] : 'text',
						);
						$masters[ $section_key ][ $id ]  = $value;
						$masters[ $data['slave-class'] ] = $value;
					}
				}
			}
			/**
			 * Produce!
			 */
			foreach ( $options as $section_key => $option ) {
				/**
				 * Section for network only
				 */
				if (
					isset( $option['network-only'] )
					&& true === $option['network-only']
				) {
					if ( $this->is_network ) {
						if ( ! is_network_admin() ) {
							$subsite = apply_filters( 'branda_module_check_for_subsite', false, $module, null );
							if ( $subsite ) {
								continue;
							}
						}
					} else {
						continue;
					}
				}
				// Make sure we are using title from first sub section.
				if ( ! empty( $option['title'] ) ) {
					$section_title = $option['title'];
				}
				$content_tabs[ $section_key ] = array();
				$classes                      = $section_inside ? array() : array(
					'sui-box-settings-row',
					'branda-section-' . $section_key,
				);
				if ( ! isset( $option['fields'] ) ) {
					if ( isset( $option['description'] ) || isset( $option['content'] ) ) {
						$content .= sprintf( '<div class="%s" id="%s">', esc_attr( implode( ' ', $classes ) ), esc_attr( $section_key ) );
						$content .= isset( $option['description'] ) ? $this->box_title( $option, 2 ) : $option['content'];
						$content .= '</div>';
					}
					continue;
				}
				if ( ! is_array( $option['fields'] ) ) {
					continue;
				}
				if ( empty( $option['fields'] ) ) {
					continue;
				}
				/**
				 * extra & classes
				 */
				$extra = array();
				if ( isset( $option['master'] ) ) {
					$master_fields = array(
						'section' => '',
						'field'   => '',
						'value'   => '',
					);
					foreach ( $master_fields as $master_field => $master_value ) {
						if ( isset( $option['master'][ $master_field ] ) ) {
							$master_value = $option['master'][ $master_field ];
						}
						$master_fields[ $master_field ] = $master_value;
						$extra[]                        = sprintf( 'data-master-%s="%s"', $master_field, esc_attr( $master_value ) );
					}
					$value     = $this->get_single_value( $options, $input, $master_fields['section'], $master_fields['field'] );
					$classes[] = 'section-is-slave';
					if ( $master_fields['value'] != $value ) {
						$classes[] = 'hidden';
					}
				}
				/**
				 * set
				 */
				$sortable = false;
				/**
				 * display type
				 */
				$show_as = isset( $option['show-as'] ) ? $option['show-as'] : 'default';
				/**
				 * postbox
				 */
				if ( preg_match( '/^(default|accordion)$/', $show_as ) ) {
					$classes = array_unique( $classes );
					if ( ! $section_inside ) {
						if ( isset( $option['no-sui-columns'] ) && true === $option['no-sui-columns'] ) {
							$classes[] = 'branda-no-sui-columns';
						}
						if ( isset( $option['classes'] ) ) {
							if ( is_string( $option['classes'] ) ) {
								$classesp[] = $option['classes'];
							} elseif ( is_array( $option['classes'] ) ) {
								$classes = array_merge( $classes, $option['classes'] );
							}
						}
						$content .= sprintf(
							'<div class="%s" id="%s" %s>',
							esc_attr( implode( ' ', $classes ) ),
							esc_attr( $section_key ),
							implode( ' ', $extra )
						);
						/**
						 * Box title
						 */
						$content .= $this->box_title( $option );
						/**
						 * open
						 */
						if ( ! isset( $option['no-sui-columns'] ) || ! $option['no-sui-columns'] ) {
							$classes = array(
								'sui-box-settings-col-2',
							);
						}
						$sortable = isset( $option['sortable'] ) && $option['sortable'];
						if ( $sortable ) {
							$classes[] = 'sortable';
						}
						$content .= sprintf(
							'<div class="%s">',
							esc_attr( implode( ' ', $classes ) )
						);
					}
					if ( 'accordion' === $show_as ) {
						// Custom classes for accordion.
						$accordion_classes = isset( $option['accordion']['classes'] ) ? (array) $option['accordion']['classes'] : array();
						$content          .= sprintf( '<div class="sui-accordion %s">', esc_attr( implode( ' ', $accordion_classes ) ) );
					}
				}
				/**
				 * vertical tabs with panes
				 */
				$sui_tabs = '';
				/**
				 * FIELDS
				 */
				foreach ( $option['fields'] as $id => $data ) {
					/**
					 * fields for network only
					 */
					if (
						isset( $data['network-only'] )
						&& true === $data['network-only']
						&& ( true !== $this->is_network || ! is_network_admin() )
					) {
						continue;
					}
					/**
					 * accordion
					 */
					$is_sui_accordion_item = false;
					if ( 'accordion' === $show_as && isset( $data['accordion'] ) ) {
						if ( isset( $data['accordion']['begin'] ) && $data['accordion']['begin'] ) {
							$accordion_item_classes = array(
								'sui-accordion-item',
								sprintf( 'branda-accordion-item-%s-%s', $module, $section_key ),
							);
							if (
								isset( $data['accordion']['item'] )
								&& isset( $data['accordion']['item']['classes'] )
								&& is_array( $data['accordion']['item']['classes'] )
							) {
								$accordion_item_classes = array_merge(
									$accordion_item_classes,
									$data['accordion']['item']['classes']
								);
							}
							$content .= sprintf( '<div class="%s">', implode( ' ', $accordion_item_classes ) );
							$content .= '<div class="sui-accordion-item-header">';
							$content .= sprintf(
								'<div class="sui-accordion-item-title">%s</div>',
								isset( $data['accordion']['title'] ) ? $data['accordion']['title'] : __( 'No title', 'ub' )
							);
							$content .= $this->sui_accordion_indicator();
							$content .= '</div>';
							$classes  = array(
								'sui-accordion-item-body',
							);
							if (
								isset( $data['accordion']['classes']['body'] )
								&& is_array( $data['accordion']['classes']['body'] )
							) {
								$classes = array_merge( $classes, $data['accordion']['classes']['body'] );
							}
							$content .= sprintf(
								'<div class="%s">',
								esc_attr( implode( ' ', $classes ) )
							);
							// Show accordion content inside box body.
							if ( ! isset( $data['accordion']['box'] ) || $data['accordion']['box'] ) {
								$accordion_sufix = '';
								if (
									isset( $data['accordion']['sufix'] )
									&& is_string( $data['accordion']['sufix'] )
									&& ! empty( $data['accordion']['sufix'] )
								) {
									$accordion_sufix = sprintf( '-%s', esc_attr( $data['accordion']['sufix'] ) );
								}
								$content .= sprintf( '<div class="sui-box%s">', $accordion_sufix );
								$content .= sprintf( '<div class="sui-box%s-body">', $accordion_sufix );
							}
						}
					}
					/**
					 * group
					 */
					if (
						isset( $data['group'] )
						&& isset( $data['group']['begin'] )
						&& true === $data['group']['begin']
					) {
						$group_classes = array();
						if ( isset( $data['master'] ) && is_string( $data['master'] ) ) {
							$group_classes[] = $data['master'];
							if (
								isset( $masters[ $data['master'] ] )
								&& 'checkbox' === $masters[ $data['master'] ]['type']
								&& 'off' === $masters[ $data['master'] ]['value']
							) {
								$group_classes[] = 'hidden';
							}
						}
						// Add group classes.
						if ( ! empty( $data['group']['classes'] ) ) {
							$group_classes = array_merge( $group_classes, (array) $data['group']['classes'] );
						}
						/**
						 * group label
						 */
						if ( isset( $data['group']['label'] ) ) {
							$group_label_classes = array( 'sui-label' );
							/**
							 * group after label
							 */
							$group_label_after = '';
							if ( isset( $data['group']['after_label'] ) ) {
								$group_label_after     = sprintf(
									'<span class="sui-actions-right">%s</span>',
									$data['group']['after_label']
								);
								$group_label_classes[] = 'branda-has-actions';
							}
							/**
							 * units on group
							 */
							$units = '';
							if (
								isset( $data['units'] )
								&& isset( $data['units']['position'] )
								&& 'group' === $data['units']['position']
							) {
								$units                 = $this->add_units( $data, $input, $section_key, $id );
								$group_label_classes[] = 'branda-has-actions';
							}
							$content .= sprintf(
								'<span class="%s">%s%s%s</span>',
								implode( ' ', $group_label_classes ),
								esc_html( $data['group']['label'] ),
								$group_label_after,
								$units
							);
						}
						$content .= sprintf(
							'<div class="%s">',
							esc_attr( implode( ' ', $group_classes ) )
						);
					}
					/**
					 * vertical tabs with panes
					 */
					if ( isset( $data['panes'] ) ) {
						$data['panes']['active'] = '';
						if (
							isset( $data['panes']['begin'] )
							&& $data['panes']['begin']
						) {
							$sui_tabs_classes        = array(
								'sui-tabs',
							);
							$data['panes']['active'] = 'active';
							$content                .= sprintf( '<div class="%s">', implode( ' ', $sui_tabs_classes ) );
							$content                .= '<div data-tabs="true">%sui-tabs-panes-tabs%</div>';
							$content                .= '<div data-panes="true">';
						}
						if (
							isset( $data['panes']['begin_pane'] )
							&& $data['panes']['begin_pane']
						) {
							$sui_tabs .= sprintf(
								'<div class="%s">%s</div>',
								esc_attr( $data['panes']['active'] ),
								isset( $data['panes']['title'] ) ? $data['panes']['title'] : __( '[no title]', 'ub' )
							);
							$content  .= sprintf( '<div class="%s">', esc_attr( $data['panes']['active'] ) );
						}
					}
					/**
					 * Content as tab: begin
					 */
					if ( isset( $data['display'] ) && 'sui-tab-content' === $data['display'] ) {
						$content_orginal = $content;
						$content         = '';
					}
					/**
					 * field ID
					 */
					$html_id = isset( $data['id'] ) ? $data['id'] : 'simple_options_' . $section_key . '_' . $id;
					/**
					 * default type
					 */
					if ( ! isset( $data['type'] ) ) {
						$data['type'] = 'text';
					}
					/**
					 * default classes
					 */
					if ( ! isset( $data['classes'] ) ) {
						$data['classes'] = array();
					} elseif ( ! is_array( $data['classes'] ) ) {
						$data['classes'] = array( $data['classes'] );
					}
					/**
					 * default class for text field
					 */
					if ( preg_match( '/^(text(area)?|email|password|number|email|url|date|time|select)$/', $data['type'] ) ) {
						$data['classes'][] = 'sui-form-control';
						$data['classes']   = array_unique( $data['classes'] );
					}
					/**
					 * html5.data
					 */
					$extra = array();
					if ( isset( $data['data'] ) ) {
						foreach ( $data['data'] as $data_key => $data_value ) {
							$extra[] = sprintf( 'data-%s="%s"', esc_html( $data_key ), esc_attr( $data_value ) );
						}
					}
					/**
					 * placeholder
					 */
					if ( isset( $data['placeholder'] ) ) {
						$extra[] = sprintf( 'placeholder="%s"', esc_attr( $data['placeholder'] ) );
					}
					/**
					 * style
					 */
					if ( isset( $data['style'] ) && is_string( $data['style'] ) ) {
						$extra[] = sprintf( 'style="%s"', esc_attr( $data['style'] ) );
					}
					/**
					 * begin table row
					 */
					if ( 'hidden' !== $data['type'] ) {
						$local = array(
							'title' => isset( $data['label'] ) ? $data['label'] : '',
						);
						if ( 'boxes' === $show_as ) {
							if ( isset( $data['description-extra'] ) ) {
								$local['description'] = $data['description-extra'];
							}
							$classes  = array(
								'sui-box-settings-row',
								'branda-section-' . $section_key,
							);
							$content .= sprintf( '<div class="%s">', esc_attr( implode( ' ', $classes ) ) );
							$content .= $this->box_title( $local );
							$content .= '<div class="sui-box-settings-col-2">';
						}
						$master_data = '';
						$master      = isset( $data['master'] ) ? $data['master'] : '';
						if ( is_array( $master ) ) {
							foreach ( $master as $master_field => $master_value ) {
								$master_data .= sprintf( ' data-master-%s="%s"', $master_field, esc_attr( $master_value ) );
							}
							$simple_master = '';
							if ( isset( $master['master'] ) ) {
								$simple_master = ' ' . $master['master'];
							}
							$master = 'simple-option-complex-master' . $simple_master;
						} elseif ( is_string( $master ) ) {
							if (
								isset( $masters[ $master ] )
								&& 'checkbox' === $masters[ $master ]['type']
								&& 'off' === $masters[ $master ]['value']
							) {
								$master .= ' hidden';
							}
						}
						/**
						 * before field
						 */
						if ( isset( $data['before_field'] ) ) {
							$content .= $data['before_field'];
						}
						/**
						 * begin
						 */
						$classes = array(
							'sui-form-field',
							'simple-option',
							sprintf( 'simple-option-%s', $data['type'] ),
							$master,
						);
						/**
						 * Add classes to container.
						 */
						if ( isset( $data['container-classes'] ) ) {
							if ( is_array( $data['container-classes'] ) ) {
								$classes = array_merge( $classes, $data['container-classes'] );
							} else {
								$classes[] = $data['container-classes'];
							}
						}
						/**
						 * remove sui-form-field from sui-tab container
						 */
						if ( 'sui-tab' === $data['type'] ) {
							$del_val = 'sui-form-field';
							if ( false !== ( $key = array_search( $del_val, $classes ) ) ) {
								unset( $classes[ $key ] );
							}
						}
						$content .= sprintf(
							'<div class="%s" %s>',
							esc_attr( implode( ' ', $classes ) ),
							$master_data
						);
						if ( $is_sui_accordion_item ) {
							$content .= '<div class="sui-accordion-item-header">';
						}
						/**
						 * Field label for 'flat'
						 */
						if ( 'flat' === $show_as ) {
							$content .= sprintf(
								'<label for="%s" class="sui-label">%s</label>',
								esc_attr( $html_id ),
								esc_html( $local['title'] )
							);
						}
						/**
						 * sortable
						 */
						if ( $sortable ) {
							$content .= '<span class="dashicons dashicons-move"></span>';
						}
						/**
						 * TH
						 */
						if ( preg_match( '/^(default|accordion)$/', $show_as ) ) {
							$show = true;
							if ( isset( $option['hide-th'] ) && true === $option['hide-th'] ) {
								$show = false;
							}
							if ( isset( $data['hide-th'] ) && true === $data['hide-th'] ) {
								$show = false;
							}
							if ( $show ) {
								if ( isset( $data['label'] ) || isset( $data['label_rich'] ) ) {
									$tag     = 'label';
									$classes = array( 'sui-label' );
									if ( $is_sui_accordion_item ) {
										$tag       = 'div';
										$classes[] = 'sui-accordion-item-title';
									}
									/**
									 * Set label
									 */
									$label = '&nbsp;';
									if ( isset( $data['label'] ) && ! empty( $data['label'] ) ) {
										$label = esc_html( $data['label'] );
									} elseif ( isset( $data['label_rich'] ) && ! empty( $data['label_rich'] ) ) {
										$label = $data['label_rich'];
									}
									/**
									 * after label
									 */
									if ( isset( $data['after_label'] ) ) {
										$label .= sprintf(
											'<span class="sui-actions-right">%s</span>',
											$data['after_label']
										);
									}
									/**
									 * units on field
									 */
									$units = '';
									if (
										isset( $data['units'] )
										&& isset( $data['units']['position'] )
										&& 'field' === $data['units']['position']
									) {
										$units = $this->add_units( $data, $input, $section_key, $id );
										if ( ! empty( $units ) ) {
											$tag = 'div';
										}
									}
									/**
									 * produce
									 */
									$content .= sprintf(
										'<%s for="%s" class="%s">%s%s</%s>',
										$tag,
										esc_attr( $html_id ),
										esc_attr( implode( ' ', $classes ) ),
										$label,
										$units,
										$tag
									);
									/**
									 * before indicator
									 */
									if ( isset( $data['before_indicator'] ) ) {
										$content .= $data['before_indicator'];
									}
									if ( $is_sui_accordion_item ) {
										$content .= $this->sui_accordion_indicator();
									}
								}
							}
						}
					}
					/**
					 * field name
					 */
					$field_name = sprintf( 'simple_options[%s][%s]', $section_key, $id );
					if ( isset( $data['multiple'] ) && $data['multiple'] ) {
						$field_name .= '[]';
					}
					if ( isset( $data['name'] ) ) {
						$field_name = $data['name'];
					}
					/**
					 * value
					 */
					$value = $this->get_single_value( $options, $input, $section_key, $id );
					/**
					 * before
					 */
					if ( isset( $data['before'] ) ) {
						$content .= $data['before'];
					}
					/**
					 * description
					 */
					if ( isset( $data['description'] ) ) {
						if ( is_string( $data['description'] ) ) {
							$data['description'] = array(
								'content' => $data['description'],
								'classes' => array(),
							);
						}
					} else {
							$data['description'] = array();
					}
					/**
					 * sanitize
					 */
					if ( ! isset( $data['description']['content'] ) ) {
						$data['description']['content'] = '';
					}
					if ( ! isset( $data['description']['position'] ) ) {
						$data['description']['position'] = 'top';
					}
					if ( ! isset( $data['description']['classes'] ) ) {
						$data['description']['classes'] = array();
					}
					/**
					 * Description: position top
					 */
					if ( 'top' === $data['description']['position'] && 'textarea' !== $data['type'] ) {
						$content .= $this->add_description( $data );
					}
					if ( $is_sui_accordion_item ) {
						$content .= '</div><div class="sui-accordion-item-body">';
						if ( isset( $data['accordion']['before_field'] ) ) {
							$content .= $data['accordion']['before_field'];
						}
					}
					/**
					 * produce
					 */
					switch ( $data['type'] ) {

						case 'description':
							if ( empty( $data['classes'] ) ) {
								$content .= sprintf(
									'<span class="sui-description">%s</span>',
									$data['value']
								);
							} else {
								$content .= sprintf( '<div class="%s"><p>%s</p></div>', esc_attr( implode( ' ', $data['classes'] ) ), $data['value'] );
							}
							break;

						case 'gallery':
						case 'media':
							if ( ! isset( $this->loaded['media'] ) ) {
								$this->loaded['media'] = true;
								wp_enqueue_media();
								add_action( 'admin_footer', array( $this, 'add_media_template' ) );
							}
							if ( ! is_array( $value ) ) {
								$params = array( 'value' => $value );
								$meta   = $this->get_single_value( $options, $input, $section_key, $id . '_meta' );
								if ( $meta ) {
									$params['meta'] = $meta;
								}
								$value = array( $params );
							}
							$content .= $this->image( $section_key, $id, $value, $data['type'], $module );
							break;

						case 'color':
							$name     = sprintf(
								'simple_options[%s][%s]',
								esc_attr( $section_key ),
								esc_attr( $id )
							);
							$content .= $this->sui_colorpicker( $html_id, $value, $name, 'true' );
							break;

						case 'radio':
							$content .= '<ul>';
							foreach ( $data['options'] as $radio_value => $radio_label ) {
								$content .= sprintf(
									'<li><label class="sui-radio"><input type="%s" name="simple_options[%s][%s]" %s value="%s" class="%s" /><span aria-hidden="true"></span><span>%s</span></label></li>',
									esc_attr( $data['type'] ),
									esc_attr( $section_key ),
									esc_attr( $id ),
									checked( $value, $radio_value, false ),
									esc_attr( $radio_value ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									esc_html( $radio_label )
								);
							}
							$content .= '</ul>';
							break;

						case 'checkboxes':
							if ( empty( $value ) || ! is_array( $value ) ) {
								$value = array();
							}
							$columns = isset( $data['columns'] ) ? $data['columns'] : 1;
							if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
								$counter = 0;
								if ( 1 === $columns ) {
									$content .= '<ul>';
								}
								foreach ( $data['options'] as $checkbox_value => $checkbox_label ) {
									if ( 1 < $columns ) {
										if ( 0 === $counter % $columns ) {
											$content .= '<div class="sui-row">';
										}
										$content .= sprintf(
											'<div class="sui-col-md-%d">',
											12 / $columns
										);
									}
									if ( 1 === $columns ) {
										$content .= '<li>';
									}
									$content .= '<label class="sui-checkbox">';
									$content .= sprintf(
										'<input type="checkbox" id="%s_%s" name="simple_options[%s][%s][%s]" value="1" class="%s" %s %s />',
										esc_attr( $html_id ),
										esc_attr( $checkbox_value ),
										esc_attr( $section_key ),
										esc_attr( $id ),
										esc_attr( $checkbox_value ),
										isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
										isset( $data['disabled'] ) ? disabled( 1, in_array( $checkbox_value, $data['disabled'], true ), false ) : '',
										checked(
											1,
											array_key_exists( $checkbox_value, $value ) ||
											isset( $data['always_selected'] ) && in_array( $checkbox_value, $data['always_selected'] ),
											false
										)
									);
									$content .= '<span></span>';
									$content .= sprintf(
										'<span class="sui-description">%s</span>',
										esc_html( $checkbox_label )
									);
									$content .= '</label>';
									if ( 1 === $columns ) {
										$content .= '</li>';
									}
									if ( 1 < $columns ) {
										$content .= '</div>';
										$counter++;
										if ( 0 === $counter % $columns ) {
											$content .= '</div>';
										}
									}
								}
								if ( 1 === $columns ) {
									$content .= '</ul>';
								} elseif ( 1 < $columns && 0 !== $counter % $columns ) {
									$content .= '</div>';
								}
							}
							break;

						case 'checkboxes':
							$content .= '<ul>';
							foreach ( $data['options'] as $checkbox_value => $checkbox_label ) {
								$checked  = is_array( $value ) && array_key_exists( $checkbox_value, $value );
								$content .= sprintf(
									'<li><label><input type="checkbox" id="%s_%s" name="simple_options[%s][%s][%s]" value="1" class="%s" %s /> %s</label></li>',
									esc_attr( $html_id ),
									esc_attr( $checkbox_value ),
									esc_attr( $section_key ),
									esc_attr( $id ),
									esc_attr( $checkbox_value ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									checked( 1, $checked, false ),
									esc_html( $checkbox_label )
								);
							}
							$content .= '</ul>';
							break;

						case 'checkbox':
							$slave = '';
							if ( isset( $data['slave-class'] ) ) {
								$slave = sprintf( 'data-slave="%s"', esc_attr( $data['slave-class'] ) );
								if ( isset( $data['classes'] ) ) {
									$data['classes'][] = 'master-field';
								} else {
									$data['classes'] = array( 'master-field' );
								}
							}
							if (
								is_array( $data['classes'] )
								&& in_array( 'switch-button', $data['classes'] )
							) {
								if ( 'on' == $value ) {
									$value = 1;
								}
								$content .= '<label class="sui-toggle">';
								$content .= sprintf(
									'<input type="%s" id="%s" name="simple_options[%s][%s]" value="1" class="%s" %s %s />',
									esc_attr( $data['type'] ),
									esc_attr( $html_id ),
									esc_attr( $section_key ),
									esc_attr( $id ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									checked( 1, $value, false ),
									$slave
								);
								$content .= '<span class="sui-toggle-slider"></span></label>';
								if ( isset( $data['checkbox_label'] ) ) {
									$content .= sprintf(
										'<label for="%s">%s</label>',
										esc_attr( $html_id ),
										esc_html( $data['checkbox_label'] )
									);
								}
							} else {
								$content .= '<label class="sui-checkbox">';
								$content .= sprintf(
									'<input type="%s" id="%s" name="simple_options[%s][%s]" value="1" class="%s" %s %s /><span aria-hidden="true"></span><span>%s</span>',
									esc_attr( $data['type'] ),
									esc_attr( $html_id ),
									esc_attr( $section_key ),
									esc_attr( $id ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									checked( 1, $value, false ),
									$slave,
									esc_html( isset( $data['checkbox_label'] ) ? $data['checkbox_label'] : '' )
								);
								$content .= '</label>';
							}
							break;

						case 'textarea':
							$data['classes'][] = 'sui-form-control';
							if ( ! is_string( $value ) ) {
								$value = '';
							}
							$editor_id = $this->get_editor_id( $module );
							$content  .= sprintf(
								'<textarea id="%s" name="simple_options[%s][%s]" class="%s" id="%s"%s>%s</textarea>',
								esc_attr( $editor_id ),
								esc_attr( $section_key ),
								esc_attr( $id ),
								isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
								esc_attr( $html_id ),
								implode( ' ', $extra ),
								esc_attr( stripslashes( $value ) )
							);
							break;

						case 'wp_editor':
							if ( ! isset( $this->loaded['teeny_mce_before_init'] ) ) {
								add_filter( 'teeny_mce_before_init', array( $this, 'add_teeny_mce_placeholder_plugin' ), 10, 2 );
								$this->loaded['teeny_mce_before_init'] = true;
							}
							if ( ! is_string( $value ) ) {
								$value = '';
							}
							/**
							 * Editor ID
							 */
							$editor_id = $this->get_editor_id( $module );
							/**
							 * Disable MarketPress media buttons to editors.
							 *
							 * @since 3.1.0
							 */
							add_filter( 'mp_media_buttons', '__return_false' );
							/**
							 * Set tinymce options
							 */
							$args = array(
								'textarea_name' => $field_name,
								'textarea_rows' => 9,
								'teeny'         => true,
							);
							if ( isset( $data['editor'] ) ) {
								$args = wp_parse_args( $data['editor'], $args );
							}
							/**
							 * Add placeholder
							 */
							if ( isset( $data['placeholder'] ) ) {
								$content .= sprintf(
									'<div class="branda-editor-placeholder hidden" aria-hidden="true">%s</div>',
									esc_html( $data['placeholder'] )
								);
							}
							ob_start();
							wp_editor( stripslashes( $value ), $editor_id, $args );
							$content .= ob_get_contents();
							ob_end_clean();
							break;

						/**
						 * select && select2
						 *
						 * @since 1.9.4
						 */
						case 'select':
						case 'select2':
						case 'select2-ajax':
							if ( isset( $data['multiple'] ) && $data['multiple'] ) {
								$extra[] = 'multiple="multiple"';
							}
							// SUI select2.
							if ( 'select2-ajax' === $data['type'] ) {
								$data['classes'][] = 'sui-select';
								$data['classes'][] = 'sui-select-ajax';
							} elseif ( 'select2' === $data['type'] ) {
								$data['classes'][] = 'sui-select';
							}
							// If small select.
							if ( ! empty( $data['small-select'] ) ) {
								$data['classes'][] = 'sui-select-sm';
							}
							$select_options = '';
							if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
								foreach ( $data['options'] as $option_value => $option_label ) {
									$selected = false;
									if ( is_array( $value ) ) {
										$selected = in_array( $option_value, $value );
									} elseif ( $value === $option_value ) {
										$selected = true;
									} else {
										$temp1    = (string) $value;
										$temp2    = (string) $option_value;
										$selected = $temp1 === $temp2;
									}
									$select_options .= sprintf(
										'<option value="%s" %s>%s</option>',
										esc_attr( $option_value ),
										selected( $selected, true, false ),
										esc_html( $option_label )
									);
								}
							}
							$content .= sprintf(
								'<select id="%s" name="%s" class="%s" %s>%s</select>',
								esc_attr( $html_id ),
								esc_attr( $field_name ),
								isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
								implode( ' ', $extra ),
								$select_options
							);
							break;

						case 'select2':
							if ( isset( $data['multiple'] ) && $data['multiple'] ) {
								$extra[] = 'multiple="multiple"';
							}
							$data['classes'][] = 'sui-form-control';
							$select_options    = '';
							if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
								foreach ( $data['options'] as $option_value => $option_label ) {
									$selected = false;
									if ( is_array( $value ) ) {
										$selected = in_array( $option_value, $value );
									} elseif ( $value === $option_value ) {
										$selected = true;
									}
									$select_options .= sprintf(
										'<option value="%s" %s>%s</option>',
										esc_attr( $option_value ),
										selected( $selected, true, false ),
										esc_html( $option_label )
									);
								}
							}
							$content          .= '<div class="sui-insert-variables">';
							$content          .= sprintf(
								'<input type="text"  id="%s" name="%s" class="%s" placeholder="%s" />',
								esc_attr( $html_id ),
								esc_attr( $field_name ),
								isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
								isset( $data['placeholder'] ) ? esc_attr( $data['placeholder'] ) : ''
							);
							$classes[]         = 'sui-variables';
							$data['classes'][] = 'sui-select';
							// If small select.
							if ( ! empty( $data['small-select'] ) ) {
								$data['classes'][] = 'sui-select-sm';
							}
							$content .= sprintf(
								'<select id="%s-select" class="%s" %s></select>',
								esc_attr( $html_id ),
								esc_attr( implode( ' ', $data['classes'] ) ),
								implode( ' ', $extra )
							);
							$content .= '</div>';
							break;

							/**
							 * CSS & HTML Editor
							 *
							 * @since 1.1.0
							 */

						case 'html_editor':
						case 'css_editor':
							if ( ! is_string( $value ) ) {
								$value = '';
							}
							$data['classes'][] = 'ub_' . $data['type'];
							// Add ace selectors.
							if ( ! empty( $data['ace_selectors'] ) ) {
								foreach ( $data['ace_selectors'] as $selectors ) {
									// Set sub title only if not empty.
									if ( ! empty( $selectors['title'] ) ) {
										$content .= sprintf( '<label class="sui-label">%s</label>', $selectors['title'] );
									}
									// Now set the selectors.
									if ( ! empty( $selectors['selectors'] ) ) {
										$content .= '<div class="sui-ace-selectors">';
										foreach ( $selectors['selectors'] as $ace_key => $ace_label ) {
											$content .= sprintf(
												'<a href="#" class="sui-selector" data-selector="%s">%s</a>',
												$ace_key,
												$ace_label
											);
										}
										$content .= '</div>';
									}
								}
							}
							$editor_id = $this->get_editor_id( $module, 'ace' );
							$extra[]   = sprintf( 'data-id="%s"', esc_attr( $editor_id ) );
							$content  .= sprintf(
								'<div id="%s-editor" class="%s sui-ace-editor" %s>%s</div>',
								esc_attr( $editor_id ),
								isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
								implode( ' ', $extra ),
								esc_attr( stripslashes( $value ) )
							);
							$content  .= sprintf(
								'<textarea name="%s" id="%s" class="sui-hidden">%s</textarea>',
								esc_attr( $field_name ),
								esc_attr( $editor_id ),
								esc_attr( stripslashes( $value ) )
							);
							break;

						/**
						 * SUI tab
						 */
						case 'sui-tab':
						case 'sui-tab-icon':
							$content .= '<div class="sui-side-tabs">';
							$content .= '<div class="sui-tabs-menu">';
							foreach ( $data['options'] as $radio_value => $radio_data ) {
								$radio_label   = $radio_data;
								$after         = $extra = '';
								$classes       = array(
									'sui-tab-item',
									sprintf(
										'branda-%s-%s',
										$id,
										$radio_value
									),
								);
								$data_tab_menu = sprintf(
									'%s-%s',
									isset( $data['slave-class'] ) ? esc_attr( $data['slave-class'] ) : '',
									$radio_value
								);
								if ( is_array( $radio_data ) ) {
									$radio_label = $radio_data['label'];
									if ( isset( $radio_data['tooltip'] ) ) {
										$extra    .= sprintf(
											' data-tooltip="%s"',
											esc_attr( $radio_data['tooltip'] )
										);
										$classes[] = 'sui-tooltip';
									}
									if ( isset( $radio_data['tab-menu'] ) ) {
										$data_tab_menu = sprintf(
											'%s-%s',
											isset( $data['slave-class'] ) ? esc_attr( $data['slave-class'] ) : '',
											$radio_data['tab-menu']
										);
									}
									if ( isset( $radio_data['classes'] ) ) {
										$classes = array_merge( $classes, $radio_data['classes'] );
									}
									if ( isset( $radio_data['after'] ) ) {
										$after = $radio_data['after'];
									}
								}
								if ( $value === $radio_value ) {
									$classes[] = 'active';
								}
								$content .= sprintf(
									'<label class="%s" %s>',
									implode( ' ', $classes ),
									$extra
								);
								$content .= sprintf(
									'<input type="radio" name="%s" value="%s" %s data-tab-menu="%s" class="ub-radio %s" />',
									esc_attr( $field_name ),
									esc_attr( $radio_value ),
									checked( $value, $radio_value, false ),
									esc_attr( $data_tab_menu ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : ''
								);
								if (
									'sui-tab' === $data['type']
									|| (
										is_array( $radio_data )
										&& isset( $radio_data['type'] )
										&& 'text' === $radio_data['type']
									)
								) {
									$content .= esc_html( $radio_label );
								} else {
									$use_file = false;
									if (
										isset( $radio_data['classes'] )
										&& is_array( $radio_data['classes'] )
										&& in_array( 'branda-icon', $radio_data['classes'] )
									) {
										$file  = branda_dir( 'assets/images/icons/' );
										$file .= sprintf( '%s.svg', $radio_data['label'] );
										if ( is_file( $file ) ) {
											$content .= file_get_contents( $file );
											$use_file = true;
										}
									}
									if ( ! $use_file ) {
										$content .= sprintf(
											'<i class="sui-icon-%s" aria-hidden="true"></i>',
											esc_attr( $radio_label )
										);
									}
								}
								$content .= '</label>';
								$content .= $after;
							}
							$content .= '</div>';
							if (
								isset( $data['slave-class'] )
								&& isset( $content_tabs[ $section_key ][ $data['slave-class'] ] )
							) {
								$content .= '<div class="sui-tabs-content">';
								foreach ( $content_tabs[ $section_key ][ $data['slave-class'] ] as $slave_id => $slave ) {
									/**
									 * focal exception
									 */
									$local_value = $value;
									if ( 'focal' === $slave['value'] ) {
										switch ( $value ) {
											case 'cover':
											case 'contain':
											case 'fill':
												$local_value = 'focal';
												break;
											default:
												$local_value = $value;
										}
									}
									$content .= sprintf(
										'<div class="%s%s branda-tab-%s-%s" data-tab-content="%s">',
										$slave['wrap'] ? 'sui-tab-boxed' : '',
										$local_value === $slave['value'] ? ' active' : '',
										esc_attr( $section_key ),
										esc_attr( $id ),
										esc_attr( $slave_id )
									);
									$content .= $slave['html'];
									$content .= '</div>';
								}
								$content .= '</div>';
							}
							$content .= '</div>';
							break;

							/**
							 * Special type callback.
							 *
							 * @since 2.0.0
							 */
						case 'callback':
							if ( isset( $data['callback'] ) && is_callable( $data['callback'] ) ) {
								$content .= call_user_func( $data['callback'], $data );
							} else {
								$content .= __( 'Something went wrong!', 'ub' );
							}
							break;

							/**
							 * Raw
							 *
							 * @since 2.0.0
							 */
						case 'raw':
							$content .= $data['content'];
							break;

							/**
							 * SUI file
							 *
							 * @since 2.0.0
							 */
						case 'file':
							$template = 'admin/common/options/file';
							$args     = array(
								'field_name' => $field_name,
								'value'      => $value,
							);
							$content .= $this->render( $template, $args, true );
							break;

						default:
							switch ( $data['type'] ) {
								case 'date':
									$data['type']      = 'text';
									$data['classes'][] = 'datepicker';
									if ( ! isset( $this->loaded['ui-datepicker'] ) ) {
										$this->loaded['ui-datepicker'] = true;
										wp_enqueue_script( 'jquery-ui-datepicker' );
										wp_localize_jquery_ui_datepicker();
										$this->enqueue_jquery_style();
									}
									if ( ! isset( $data['data'] ) ) {
										$data['data'] = array();
									}
									$alt     = 'datepicker-' . crc32( serialize( $data ) );
									$extra[] = sprintf( 'data-alt="%s"', esc_attr( $alt ) );
									if ( ! isset( $data['after'] ) ) {
										$data['after'] = '';
									}
									$alt_value = '';
									if ( is_array( $value ) ) {
										if ( isset( $value['alt'] ) ) {
											$alt_value = $value['alt'];
											$value     = date_i18n( get_option( 'date_format' ), strtotime( $value['alt'] ) );
										} else {
											$value = '';
										}
									}
									$data['after']       .= sprintf(
										'<input type="hidden" name="%s[alt]" id="%s" value="%s" />',
										esc_attr( $field_name ),
										esc_attr( $alt ),
										esc_attr( $alt_value )
									);
									$field_name          .= '[human]';
									$data['field_before'] = '<div class="sui-date"><i class="sui-icon-calendar" aria-hidden="true"></i>';
									$data['field_after']  = '</div>';
									break;

								case 'time':
									$data['classes'][]    = 'branda-input-time';
									$data['field_before'] = '<div class="sui-date"><i class="sui-icon-clock" aria-hidden="true"></i>';
									$data['field_after']  = '</div>';

									break;

								case 'number':
									if ( isset( $data['min'] ) ) {
										$extra[] = sprintf( 'min="%d"', $data['min'] );
									}
									if ( isset( $data['max'] ) ) {
										$extra[] = sprintf( 'max="%d"', $data['max'] );
									}
									$value = intval( $value );
									break;

								case 'button':
								case 'submit':
									if ( isset( $data['sui'] ) ) {
										if ( is_string( $data['sui'] ) ) {
											$data['sui'] = array( $data['sui'] );
										}
										foreach ( $data['sui'] as $sui_sufix ) {
											$data['classes'][] = sprintf( 'sui-button-%s', $sui_sufix );
										}
									}
									$data['classes'][] = 'sui-button';
									if ( isset( $data['value'] ) ) {
										$value = $data['value'];
									}
									if ( isset( $data['disabled'] ) && $data['disabled'] ) {
										$extra[] = 'disabled="disabled"';
									}
									break;
								default:
									if ( isset( $data['value'] ) && empty( $value ) ) {
										$value = $data['value'];
									}
							}
							/**
							 * readonly?
							 */
							if ( isset( $data['readonly'] ) && $data['readonly'] ) {
								$extra[] = 'readonly="readonly"';
							}
							/**
							 * add
							 */
							if ( 'link' === $data['type'] ) {
								/**
								 * target?
								 *
								 * @since 3.1.0
								 */
								if ( isset( $data['target'] ) && $data['target'] ) {
									$extra[] = sprintf( 'target="%s"', esc_attr( $data['target'] ) );
								}
								/**
								 * since 1.2.1
								 */
								$content .= sprintf(
									'<a href="%s" id="%s" name="%s" class="%s" id="%s" %s >%s%s</a>',
									isset( $data['href'] ) ? esc_attr( $data['href'] ) : '',
									esc_attr( $html_id ),
									esc_attr( $field_name ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									esc_attr( $html_id ),
									implode( ' ', $extra ),
									$this->get_sui_icon( $data ),
									esc_html( stripslashes( $value ) )
								);
							} elseif ( 'focal' === $data['type'] ) {
								$url = '';
								if (
									isset( $input )
									&& is_array( $input )
									&& isset( $input['content'] )
									&& isset( $input['content']['content_background'] )
									&& isset( $input['content']['content_background'][0] )
									&& isset( $input['content']['content_background'][0]['meta'] )
									&& isset( $input['content']['content_background'][0]['meta'][0] )
								) {
									$url = $input['content']['content_background'][0]['meta'][0];
								}
								$args = array(
									'html_id'          => $html_id,
									'field_name'       => $field_name,
									'value_x'          => 50,
									'value_y'          => 50,
									'background_image' => $url,
								);
								if ( is_array( $value ) ) {
									if ( isset( $value['x'] ) ) {
										$args['value_x'] = $value['x'];
									}
									if ( isset( $value['y'] ) ) {
										$args['value_y'] = $value['y'];
									}
								}
								$template = 'admin/common/options/focal';
								$content .= $this->render( $template, $args, true );
							} elseif ( preg_match( '/^(button|submit)$/', $data['type'] ) ) {
								$content .= sprintf(
									'<button type="%s" id="%s" name="%s" class="%s" id="%s" %s />%s%s</button>',
									esc_attr( $data['type'] ),
									esc_attr( $html_id ),
									esc_attr( $field_name ),
									isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
									esc_attr( $html_id ),
									implode( ' ', $extra ),
									$this->get_sui_icon( $data ),
									$value
								);
							} else {
								/**
								 * field before
								 */
								$field = '';
								if ( isset( $data['field_before'] ) ) {
									$field .= $data['field_before'];
								}

								/**
								 * field field_protection
								 */
								if ( ! empty( $data['field_protection'] ) && ! empty( $value ) ) {
									$show_message = ! empty( $data['field_protection_show_message'] ) ? esc_html( $data['field_protection_show_message'] ) : esc_html__( 'Show Password', 'ub' );
									$cancel_message = ! empty( $data['field_protection_cancel_message'] ) ? esc_html( $data['field_protection_cancel_message'] ) : esc_html__( 'Cancel', 'ub' );
									
									$content .= '<button type="button" class="sui-button ub-button-field_protection  ub-button-field_protection-show" data-show-msg="\'' . $show_message . '\'" data-cancel-msg="\'' . $cancel_message . '\'">';
									$content .= $show_message;
									$content .= '</button>';
								}

								if ( empty( $data['field_protection'] ) ) {
									$field .= sprintf(
										'<input type="%s" id="%s" name="%s" value="%s" class="%s" id="%s" %s />',
										esc_attr( $data['type'] ),
										esc_attr( $html_id ),
										esc_attr( $field_name ),
										! empty( $value ) ? esc_attr( stripslashes( $value ) ) : '',
										isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
										esc_attr( $html_id ),
										implode( ' ', $extra )
									);
								} else {
									$field .= sprintf(
										'<input type="%s" id="%s" name="%s" class="%s" id="%s" %s />',
										esc_attr( $data['type'] ),
										esc_attr( $html_id ),
										esc_attr( $field_name ),
										isset( $data['classes'] ) ? esc_attr( implode( ' ', $data['classes'] ) ) : '',
										esc_attr( $html_id ),
										implode( ' ', $extra )
									);
								}
								
								/**
								 * field after
								 */
								if ( isset( $data['field_after'] ) ) {
									$field .= $data['field_after'];
								}
								if ( 'password' === $data['type'] ) {
									$pass_filed = '<div class="sui-with-button sui-with-button-icon">';
									$pass_filed .= $field;
									$pass_filed .= '<button type="button" class="sui-button-icon">';
									$pass_filed .= '<i aria-hidden="true" class="sui-icon-eye"></i>';
									$pass_filed .= sprintf(
										'<span class="sui-password-text sui-screen-reader-text">%s</span>',
										esc_html__( 'Show Password', 'ub' )
									);
									$pass_filed .= sprintf(
										'<span class="sui-password-text sui-screen-reader-text sui-hidden">%s</span>',
										esc_html__( 'Hide Password', 'ub' )
									);
									$pass_filed .= '</button>';
									$pass_filed .= '</div>';

									if ( ! empty( $data['field_protection'] ) && ! empty( $value ) ) {
										$content .= '<div class="sui-row ub-field_protection-field-wrap sui-hidden">';
										$content .= '<div class="sui-col-md-9">';
										$content .= $pass_filed;
										$content .= '</div>';

										$content .= '<div class="sui-col-md-3">';
										$content .= '<button type="button" class="sui-button sui-button-ghost ub-button-field_protection ub-button-field_protection-cancel" data-show-msg="\'' . $cancel_message . '\'" data-cancel-msg="\'' . $cancel_message . '\'">';
										$content .= $cancel_message;
										$content .= '</button>';
										$content .= '</div>';
										$content .= '</div>';
									} else {
										$content .= $pass_filed;
									}
									
								} else {
									$content .= $field;
								}
							}
							break;
					}
					/**
					 * description
					 */
					if ( 'textarea' === $data['type'] || 'bottom' === $data['description']['position'] ) {
						$content .= $this->add_description( $data );
					}
					if ( $is_sui_accordion_item ) {
						$content .= '</div>';
					}
					/**
					 * after
					 */
					if ( isset( $data['after'] ) ) {
						$content .= $data['after'];
					}
					if (
						is_array( $data['classes'] )
						&& in_array( 'ui-slider', $data['classes'] )
					) {
						$ui_slider_data = array(
							'data-target-id' => esc_attr( $html_id ),
						);
						foreach ( array( 'min', 'max' ) as $tmp_key ) {
							if ( isset( $data[ $tmp_key ] ) ) {
								$ui_slider_data[ 'data-' . $tmp_key ] = $data[ $tmp_key ];
							}
						}
						$ui_slider_data_string = '';
						foreach ( $ui_slider_data as $k => $v ) {
							$ui_slider_data_string .= sprintf( ' %s="%s"', $k, esc_attr( $v ) );
						}
						$content .= sprintf( '<div class="ui-slider" %s></div>', $ui_slider_data_string );
						if ( ! isset( $this->loaded['ui-slider'] ) ) {
							$this->loaded['ui-slider'] = true;
							wp_enqueue_script( 'jquery-ui-slider' );
							$this->enqueue_jquery_style();
						}
					}
					/**
					 * Do not show defaults!
					 *
					 * @since 2.0.0
					 */
					if ( false && isset( $data['default'] ) ) {
						$show = true && ! is_array( $data['default'] );
						if ( isset( $data['default_hide'] ) && $data['default_hide'] ) {
							$show = false;
						}
						if ( $show ) {
							$default = $data['default'];
							if ( isset( $data['options'] ) && isset( $data['options'][ $default ] ) ) {
								$default = $data['options'][ $data['default'] ];
							}
							if ( ! empty( $default ) || is_numeric( $default ) ) {
								$message = sprintf(
									__( 'Default is: <code><strong>%s</strong></code>', 'ub' ),
									$default
								);
								if ( 'color' == $data['type'] ) {
									$message = sprintf(
										__( 'Default color is: <code><strong>%s</strong></code>', 'ub' ),
										$data['default']
									);
								}
								$content .= sprintf( '<p class="description description-default">%s</p>', $message );
							}
						}
					}
					if ( 'hidden' !== $data['type'] ) {
						$content .= '</div>';
						if ( 'boxes' === $show_as ) {
							$content .= '</div>';
							$content .= '</div>';
						}
					}
					/**
					 * after field
					 */
					if ( isset( $data['after_field'] ) ) {
						$content .= $data['after_field'];
					}
					/**
					 * Content as tab: end
					 */
					if ( isset( $data['display'] ) && 'sui-tab-content' === $data['display'] ) {
						if ( ! isset( $content_tabs[ $section_key ][ $data['master'] ] ) ) {
							$content_tabs[ $section_key ][ $data['master'] ] = array();
						}
						$master_key_with_value = sprintf( '%s-%s', $data['master'], $data['master-value'] );
						if ( ! isset( $content_tabs[ $section_key ][ $data['master'] ][ $master_key_with_value ] ) ) {
							$content_tabs[ $section_key ][ $data['master'] ][ $master_key_with_value ] = array(
								'value' => $data['master-value'],
								'html'  => '',
								'wrap'  => ! isset( $data['wrap'] ) || false !== $data['wrap'],
							);
						}
						$content_tabs[ $section_key ][ $data['master'] ][ $master_key_with_value ]['html'] .= $content;
						$content = $content_orginal;
					}
					/**
					 * vertical tabs with panes
					 */
					if ( isset( $data['panes'] ) ) {
						if ( isset( $data['panes']['end_pane'] ) ) {
							$content .= '</div>';
						}
						if (
							isset( $data['panes']['end'] )
							&& $data['panes']['end']
						) {
							$content .= '</div>';
							$content .= '</div>';
							$content  = preg_replace( '/%sui-tabs-panes-tabs%/', $sui_tabs, $content );
							$sui_tabs = '';
						}
					}
					/**
					 * group
					 */
					if (
						isset( $data['group'] )
						&& isset( $data['group']['end'] )
						&& true === $data['group']['end']
					) {
							$content .= '</div>';
						if (
							isset( $data['group']['double-end'] )
							&& true === $data['group']['double-end']
						) {
							$content .= '</div>';
						}
					}
					/**
					 * accordion
					 */
					if ( 'accordion' === $show_as && isset( $data['accordion'] ) ) {
						if ( isset( $data['accordion']['end'] ) && $data['accordion']['end'] ) {
							// Close box body.
							if ( ! isset( $data['accordion']['box'] ) || $data['accordion']['box'] ) {
								$content .= '</div>';
								$content .= '</div>';
							}
							// Close accordion.
							$content .= '</div>';
							$content .= '</div>';
						}
					}
				}
				/**
				 * Default or accordion ending.
				 */
				if ( preg_match( '/^(default|accordion)$/', $show_as ) ) {
					/**
					 * section options, table foot
					 */
					$table_footer = '';
					/**
					 * add reset
					 */
					$show_reset_button = apply_filters( 'ultimate_branding_options_show_reset', false );
					if ( isset( $option['hide-reset'] ) && true === $option['hide-reset'] ) {
						$show_reset_button = false;
					}
					if ( isset( $option['show-reset'] ) && true === $option['show-reset'] ) {
						$show_reset_button = true;
					}
					if ( $show_reset_button ) {
						if ( 'accordion' === $show_as ) {
							$table_footer .= '<div class="sui-box-body">';
						} else {
							$table_footer .= '<span class="simple-option-reset-section">';
						}
						$uba           = branda_get_uba_object();
						$args          = array(
							'text'  => __( 'Reset', 'ub' ),
							'data'  => array(
								'module'  => $module,
								'nonce'   => wp_create_nonce( $this->get_section_reset_nonce_name( $module, $section_key ) ),
								'section' => $section_key,
								'title'   => $section_title,
								'network' => is_network_admin(),
							),
							'sui'   => 'ghost',
							'class' => 'branda-reset-section',
						);
						$table_footer .= $uba->button( $args );
						if ( 'accordion' === $show_as ) {
							$table_footer .= '</div>';
						} else {
							$table_footer .= '</span>';
						}
					}
					/**
					 * table foot content filter
					 *
					 * @since 2.3.0
					 *
					 * @param string $table_footer Current footer.
					 * @param string $module Current module.
					 * @param string $section_key Current section.
					 */
					$table_footer = apply_filters( 'ultimate_branding_options_footer', $table_footer, $module, $section_key );
					/**
					 * check and show options table section footer
					 */
					if ( ! empty( $table_footer ) ) {
						$content .= $table_footer;
					}
					// Make sure to close section if the end.
					if ( isset( $option['sub_section'] ) && 'start' === $option['sub_section'] ) {
						$section_inside = true;
					} elseif ( isset( $option['sub_section'] ) && 'end' === $option['sub_section'] ) {
						$section_inside = false;
					}
					// Close the section.
					if ( ! $section_inside ) {
						// section options table end.
						$content .= '</div>';
						$content .= '</div>';
					}
					if ( 'accordion' === $show_as ) {
						$content .= '</div>';
					}
				}
			}
			/**
			 * why it is empty?
			 */
			if ( empty( $content ) ) {
				$content = sprintf(
					'<div class="notice notice-error inline"><p>%s</p></div>',
					__( 'There is no defined options here.', 'ub' )
				);
			} else {
				/**
				 * add reset whole module button
				 */
				$footer = '';
				if ( isset( $options['reset-module'] ) && true === $options['reset-module'] ) {
					$uba                      = branda_get_uba_object();
					$show_reset_module_button = apply_filters( 'branda_options_show_reset_module_button', true, $module );
					if ( $show_reset_module_button ) {
						$footer .= '<div class="sui-box-settings-row">';
						$args    = array(
							'text'  => __( 'Reset to Default', 'ub' ),
							'data'  => array(
								'module' => $module,
								'nonce'  => wp_create_nonce( 'reset-module-' . $module ),
							),
							'icon'  => 'undo',
							'sui'   => 'ghost',
							'class' => 'branda-reset-module',
						);
						$footer .= $uba->button( $args );
						$footer .= '</div>';
						if ( ! empty( $footer ) ) {
							$footer = sprintf( '<div class="sui-box-footer">%s</div>', $footer );
						}
					}
				}
				/**
				 * wrap Options
				 */
				if ( 'flat' !== $show_as && ( empty( $options['global-show-as'] ) || 'flat' !== $options['global-show-as'] ) ) {
					$content = sprintf( '<div class="sui-box-body">%s</div>', $content );
				}
				/**
				 * Add footer
				 */
				$content .= $footer;
				/**
				 * Filter content
				 */
				$content .= apply_filters( 'branda_options_after', '', $module );
			}
			return $content;
		}

		/**
		 * Handle admin AJAX requests
		 *
		 * @since 1.8.5
		 */
		public function ajax() {
			/**
			 * actions with "do"
			 */

			if ( isset( $_REQUEST['do'] ) && isset( $_REQUEST['nonce'] ) ) {
				switch ( $_REQUEST['do'] ) {
					case 'copy':
						$data = $this->helper_ajax_copy();
						break;
					default:
						wp_send_json_error( array( 'message' => __( 'Wrong action!', 'ub' ) ) );
				}
			}
			wp_send_json_error( array( 'message' => __( 'Something went wrong!', 'ub' ) ) );
		}

		/**
		 * Get section reset nonce name
		 *
		 * @since 3.0.0
		 *
		 * @param string $module Module name.
		 * @param string $section Section name.
		 *
		 * @return string $nonce_name Nonce name.
		 */
		private function get_section_reset_nonce_name( $module, $section ) {
			$nonce_name = sprintf(
				'branda-%s-reset-%s',
				$module,
				$section
			);
			return $nonce_name;
		}

		/**
		 * Handle admin AJAX Section reset
		 *
		 * @since 3.0.0
		 */
		public function ajax_reset_section() {
			$section     = ! empty( $_POST['section'] ) ? sanitize_text_field( $_POST['section'] ) : '';
			$module      = ! empty( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
			$nonce       = ! empty( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			$option_name = branda_get_option_name_by_module( $module );

			if (
				empty( $section )
				|| empty( $module )
				|| empty( $nonce )
				|| empty( $option_name )
			) {
				wp_send_json_error( array( 'message' => __( 'Missing required parameters.', 'ub' ) ) );
			}
			$nonce_name = $this->get_section_reset_nonce_name( $module, $section );
			if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
				wp_send_json_error( array( 'message' => __( 'Nope! Security check failed!', 'ub' ) ) );
			}
			$success = false;
			if ( 'unknown' == $option_name ) {
				$success = apply_filters( 'ultimatebranding_reset_section', $success, $module, $section );
			} else {
				$reset_section = apply_filters( 'ultimatebranding_reset_section_' . $module, false, $option_name, $section );
				$value         = branda_get_option( $option_name );
				if ( $reset_section ) { // Specific resetting section
					$success = true;
				} elseif ( isset( $value[ $section ] ) ) {
					unset( $value[ $section ] );
					branda_update_option( $option_name, $value );
					$success = true;
				} else {
					wp_send_json_error( array( 'message' => __( 'Section is already in default state!', 'ub' ) ) );
				}
			}
			if ( $success ) {
				$uba     = branda_get_uba_object();
				$message = array(
					'type'    => 'info',
					'message' => __( 'Section data was reset.', 'ub' ),
				);
				$uba->add_message( $message );
				wp_send_json_success();
			}
			wp_send_json_error( array( 'message' => __( 'Something went wrong!', 'ub' ) ) );
		}

		/**
		 * Shared UI Colorpicker
		 *
		 * @since 3.0
		 */
		public function sui_colorpicker( $id, $value, $name, $alpha = 'false' ) {
			$args     = array(
				'id'     => $id,
				'value'  => $value,
				'name'   => $name,
				'alpha'  => $alpha,
				'button' => __( 'Select', 'ub' ),
			);
			$template = 'admin/common/options/sui-colorpicker';
			return $this->render( $template, $args, true );
		}

		/**
		 * Helper AJAX copy
		 *
		 * @since 2.3.0
		 */
		private function helper_ajax_copy() {
			if (
				! isset( $_REQUEST['module'] )
				|| ! isset( $_REQUEST['section'] )
				|| ! isset( $_REQUEST['from'] )
			) {
				wp_send_json_error( array( 'message' => __( 'Missing required parameters.', 'ub' ) ) );
			}
			if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'ub-copy-section-settings-' . $_REQUEST['module'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Nope! Security check failed!', 'ub' ) ) );
			}
			$result = $this->copy_settings( $_REQUEST['module'], $_REQUEST['section'], $_REQUEST['from'] );
			if ( $result ) {
				wp_send_json_success();
			}
		}

		/**
		 * Copy settings
		 *
		 * @since 2.3.0
		 */
		private function copy_settings( $to, $section, $from ) {
			$from_data = branda_get_option( $from );
			if ( ! isset( $from_data[ $section ] ) ) {
				wp_send_json_error( array( 'message' => __( 'Source section is not saved yet and there is nothing to copy!', 'ub' ) ) );
			}
			$to_data = branda_get_option( $to );
			if ( ! is_array( $to_data ) ) {
				$to_data = array();
			}
			$to_data[ $section ] = $from_data[ $section ];
			/**
			 * social media exception
			 */
			if ( 'social_media' === $section ) {
				if ( isset( $from_data['_social_media_sortable'] ) ) {
					$to_data['_social_media_sortable'] = $from_data['_social_media_sortable'];
				}
			}
			$result = branda_update_option( $to, $to_data );
			if ( $result ) {
				$uba     = branda_get_uba_object();
				$message = array(
					'type'    => 'info',
					'message' => __( 'Section data was copied successfully.', 'ub' ),
				);
				$uba->add_message( $message );
			}
			return $result;
		}

		/**
		 * get value of specyfic key
		 *
		 * @since 1.9.4
		 */
		private function get_single_value( $options, $input, $section, $field ) {
			$value = null;
			if ( isset( $input[ $section ] ) && isset( $input[ $section ][ $field ] ) ) {
				$value = $input[ $section ][ $field ];
			} elseif (
				isset( $options[ $section ] )
				&& isset( $options[ $section ]['fields'] )
				&& isset( $options[ $section ]['fields'][ $field ] )
			) {
				if ( isset( $options[ $section ]['fields'][ $field ]['value'] ) ) {
					$value = $options[ $section ]['fields'][ $field ]['value'];
				} elseif ( isset( $options[ $section ]['fields'][ $field ]['default'] ) ) {
					$value = $options[ $section ]['fields'][ $field ]['default'];
				}
			}
			/**
			 * skip value
			 */
			if (
				isset( $options[ $section ]['fields'][ $field ]['skip_value'] )
				&& $options[ $section ]['fields'][ $field ]['skip_value']
			) {
				$value = '';
			}
			if ( isset( $options[ $section ]['fields'][ $field ]['const_value'] ) ) {
				$value = $options[ $section ]['fields'][ $field ]['const_value'];
			}
			return $value;
		}

		/**
		 * Enqueue custom jQuery UI css
		 */
		private function enqueue_jquery_style() {
			$key = 'ub-jquery-ui';
			if ( isset( $this->loaded[ $key ] ) ) {
				return;
			}
			wp_enqueue_style( $key, branda_url( 'assets/css/vendor/jquery-ui.min.css' ), array(), '1.12.1' );
			$this->loaded[ $key ] = true;
		}

		/**
		 * Image helper
		 */
		private function image( $section_key, $id, $images, $type, $module ) {
			$output  = array(
				'type'   => $type,
				'images' => array(),
			);
			$add     = empty( $images );
			$content = '';
			foreach ( $images as $data ) {
				$image    = isset( $data['value'] ) ? $data['value'] : null;
				$image_id = $image_src = $disabled = '';
				if ( ! empty( $image ) ) {
					$status = get_post_status( $image );
					if ( false === $status ) {
						$image = null;
					} else {
						$image_src = $image;
						$image_id  = 'file-' . crc32( $image );
						$add       = true;
					}
				}
				if ( ! empty( $image ) && preg_match( '/^\d+$/', $image ) ) {
					$image_id  = 'attachment-id-' . $image;
					$image_src = wp_get_attachment_image_url( $image, 'full' );
					$add       = true;
				}
				if ( empty( $image_src ) ) {
					$url = isset( $data['meta'] ) ? $data['meta'][0] : $data['value'];
					if ( ! empty( $url ) ) {
						$image_id = 'file-' . crc32( serialize( $data ) );
						$image    = $image_src = $url;
						$add      = true;
					} else {
						$image_id = 'time-' . time();
						$disabled = 'disabled';
						$add      = false;
					}
				}
				$output['images'][] = array(
					'id'              => $id,
					'image_id'        => $image_id,
					'section_key'     => $section_key,
					'value'           => $image,
					'image_src'       => $image_src,
					'file_name'       => basename( $image_src ),
					'disabled'        => $disabled,
					'container_class' => $image ? 'sui-has_file' : '',
				);
			}
			if ( $add && 'gallery' === $type ) {
				$output['images'][] = array(
					'id'          => $id,
					'section_key' => $section_key,
					'disabled'    => 'disabled',
				);
			}
			$content   .= '<script type="text/javascript">';
			$element_id = sprintf(
				'branda_option_media_%s_%s_%s',
				Branda_Helper::hyphen_to_underscore( sanitize_title( $module ) ),
				sanitize_title( $section_key ),
				sanitize_title( $id )
			);
			$content   .= sprintf( '_%s=', $element_id );
			$content   .= json_encode( $output );
			$content   .= ';</script>';
			$content   .= sprintf( '<div class="images" id="%s"></div>', esc_attr( $element_id ) );
			return $content;
		}

		/**
		 * Add media template.
		 *
		 * This template is used to choose media files.
		 */
		public function add_media_template() {
			$args     = array(
				'button' => __( 'Upload image', 'ub' ),
				'label'  => __( 'Remove file', 'ub' ),
			);
			$template = 'admin/common/options/media';
			$this->render( $template, $args );
		}

		/**
		 * box title
		 *
		 * @since 2.1.0
		 */
		public function box_title( $option, $columns = 1 ) {
			$content = '';
			/**
			 * No title - no box
			 */
			if ( ! isset( $option['title'] ) ) {
				return $content;
			}
			if ( ! isset( $option['no-sui-columns'] ) || ! $option['no-sui-columns'] ) {
				$content .= sprintf( '<div class="sui-box-settings-col-%d">', $columns );
			}
			$content .= sprintf(
				'<span class="sui-settings-label">%s</span>',
				$option['title']
			);
			if ( isset( $option['description'] ) ) {
				$description = $option['description'];
				if ( is_array( $description ) ) {
					$description = implode( '<br /><br />', $description );
				}
				$content .= sprintf(
					'<span class="sui-description">%s</span>',
					$description
				);
			}
			if ( ! isset( $option['no-sui-columns'] ) || ! $option['no-sui-columns'] ) {
				$content .= '</div>';
			}
			return $content;
		}

		private function sui_accordion_indicator() {
			$content  = '<div class="sui-accordion-col-auto">';
			$content .= sprintf(
				'<button type="button" class="sui-button-icon sui-accordion-open-indicator" aria-label="%s"><i class="sui-icon-chevron-down" aria-hidden="true"></i></button>',
				esc_attr__( 'Open item', 'ub' )
			);
			$content .= '</div>';
			return $content;
		}

		/**
		 * Add unit selector
		 *
		 * @since 3.0.0
		 */
		private function add_units( $data, $input, $section_key, $id ) {
			$content = '';
			$units   = $available_units = array( 'px', '%', 'em', 'rem' );
			if ( isset( $data['units']['units'] ) ) {
				$units = array_intersect( $available_units, $data['units']['units'] );
			}
			if ( 0 === sizeof( $units ) ) {
				return $content;
			}
			/**
			 * field name
			 */
			$field_name = sprintf( '%s_units', $id );
			if ( isset( $data['units']['name'] ) ) {
				$field_name = $data['units']['name'];
			}
			$value = $this->get_single_value( array(), $input, $section_key, $field_name );
			if (
				! is_array( $units )
				|| ! in_array( $value, $units )
			) {
				$value = false;
			}
			if (
				empty( $value )
				&& isset( $data['units']['default'] )
				&& is_array( $units )
				&& in_array( $data['units']['default'], $units )
			) {
				$value = $data['units']['default'];
			}
			$field_name = sprintf( 'simple_options[%s][%s]', $section_key, $field_name );
			if ( 1 === sizeof( $units ) ) {
				$content = sprintf(
					'<input type="hidden" name="%s" value="%s" />%s',
					esc_attr( $field_name ),
					esc_attr( $units[0] ),
					esc_html( $units[0] )
				);
			} else {
				$content .= sprintf(
					'<select class="sui-select-sm branda-units" name="%s">',
					esc_attr( $field_name )
				);
				foreach ( $units as $unit ) {
					$content .= sprintf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $unit ),
						selected( $value, $unit, false ),
						esc_html( $unit )
					);
				}
				$content .= '</select>';
			}
			if ( ! empty( $content ) ) {
				$content = sprintf( '<span class="sui-actions-right">%s</span>', $content );
			}
			return $content;
		}

		/**
		 * Renders a view file
		 *
		 * @param $file
		 * @param array      $params
		 * @param bool|false $return
		 * @return string
		 */
		public function render( $file, $params = array(), $return = false ) {
			if ( array_key_exists( 'this', $params ) ) {
				unset( $params['this'] );
			}
			extract( $params, EXTR_OVERWRITE ); // phpcs:ignorei
			if ( $return ) {
				ob_start();
			}
			$template_file = branda_dir( 'views/' . $file ) . '.php';
			if ( file_exists( $template_file ) ) {
				include $template_file;
			}
			if ( $return ) {
				return ob_get_clean();
			}
		}

		private function add_description( $data ) {
			$content = '';
			if ( empty( $data['description']['content'] ) ) {
				return $content;
			}
			$content .= sprintf( '<span class="sui-description">%s</span>', $data['description']['content'] );

			return $content;
		}

		/**
		 * Generate editor ID
		 *
		 * Generate editor ID to avoid the same ID on one admin page.
		 *
		 * @since 3.0.0
		 */
		private function get_editor_id( $module, $key = 'wp' ) {
			$id        = serialize( $module );
			$id       .= time();
			$id       .= rand();
			$id        = crc32( $id );
			$editor_id = sprintf(
				'branda-%s-%s',
				esc_attr( $key ),
				esc_attr( $id )
			);
			return $editor_id;
		}

		private function get_sui_icon( $data ) {
			$icon = '';
			if ( isset( $data['icon'] ) ) {
				$icon = sprintf(
					'<i class="sui-icon-%s" aria-hidden="true"></i> ',
					esc_attr( $data['icon'] )
				);
			}
			return $icon;
		}

		/**
		 * Place external plugins in teens MCE configuration.
		 *
		 * @since 3.2.0
		 */
		public function add_teeny_mce_placeholder_plugin( $mceInit, $editor_id ) {
			$file = branda_url( 'external/wp-tinymce-placeholder/mce.placeholder.js' );
			if ( preg_match( '/^branda/', $editor_id ) ) {
				$external_plugins = new stdClass();
				if ( isset( $mceInit['external_plugins'] ) ) {
					$external_plugins = json_decode( $mceInit['external_plugins'] );
					if ( isset( $external_plugins->placeholder ) ) {
						return $mceInit;
					}
				}
				$external_plugins->placeholder = $file;
				$mceInit['external_plugins']   = json_encode( $external_plugins );
			}
			return $mceInit;
		}
	}
}
