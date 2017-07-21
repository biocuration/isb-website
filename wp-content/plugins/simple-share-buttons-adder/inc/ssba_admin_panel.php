<?php

function ssba_admin_header()
{
	// open wrap
	$htmlHeader = '<div class="ssba-admin-wrap">';

	// navbar/header
	$htmlHeader .= '<nav class="navbar navbar-default">
					  <div class="container-fluid">
					    <div class="navbar-header">
					      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					      </button>
					      <a class="navbar-brand" href="https://simplesharebuttons.com"><img src="'.plugins_url().'/simple-share-buttons-adder/images/simplesharebuttons.png" alt="Simple Share Buttons Plus" class="ssba-logo-img" /></a>
					    </div>

					    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					      <ul class="nav navbar-nav navbar-right">
					        <li><a href="https://simplesharebuttons.com/wordpress-faq/" target="_blank">FAQ</a></li>
					        <li><a data-toggle="modal" data-target="#ssbaSupportModal" href="#">Support</a></li>
					        <li><a class="btn btn-primary ssba-navlink-blue" href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=navlink" target="_blank">Plus <i class="fa fa-plus"></i></a></li>
					      </ul>
					    </div>
					  </div>
					</nav>';

		$htmlHeader.= '<div class="modal fade" id="ssbaSupportModal" tabindex="-1" role="dialog" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						        <h4 class="modal-title">Simple Share Buttons Support</h4>
						      </div>
						      <div class="modal-body">
						        <p>Please note that the this plugin relies mostly on WordPress community support from other  users.</p>
						        <p>If you wish to receive official support, please consider purchasing <a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=support_modal" target="_blank"><b>Simple Share Buttons Plus</b></a></p>
						        <div class="row">
    						        <div class="col-sm-6">
    						            <a href="https://wordpress.org/support/plugin/simple-share-buttons-adder" target="_blank"><button class="btn btn-block btn-default">Community support</button></a>
                                    </div>
                                    <div class="col-sm-6">
    						            <a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=support_modal" target="_blank"><button class="btn btn-block btn-primary">Check out Plus</button></a>
    						        </div>
                                </div>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						      </div>
						    </div>
						  </div>
						</div>';

		// open container - closed in footer
		$htmlHeader .= '<div class="container">';

	// return
	return $htmlHeader;
}

function ssba_admin_footer()
{
	// row
	$htmlFooter = '<footer class="row">';

		// col
		$htmlFooter .= '<div class="col-sm-12">';

			// link to show footer content
			$htmlFooter .= '<a href="https://simplesharebuttons.com" target="_blank">Simple Share Buttons Adder</a> <span class="badge">'.SSBA_VERSION.'</span>';

			// show more/less links
			$htmlFooter .= '<button type="button" class="ssba-btn-thank-you pull-right btn btn-primary" data-toggle="modal" data-target="#ssbaFooterModal"><i class="fa fa-info"></i></button>';

			$htmlFooter.= '<div class="modal fade" id="ssbaFooterModal" tabindex="-1" role="dialog" aria-labelledby="ssbaFooterModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						        <h4 class="modal-title">Simple Share Buttons</h4>
						      </div>
						      <div class="modal-body">
						        <p>Many thanks for choosing <a href="https://simplesharebuttons.com" target="_blank">Simple Share Buttons</a> for your share buttons plugin, we\'re confident you won\'t be disappointed in your decision. If you require any support, please visit the <a href="https://wordpress.org/support/plugin/simple-share-buttons-adder" target="_blank">support forum</a>.</p>
						        <p>If you like the plugin, we\'d really appreciate it if you took a moment to <a href="https://wordpress.org/support/view/plugin-reviews/simple-share-buttons-adder" target="_blank">leave a review</a>, if there\'s anything missing to get 5 stars do please <a href="https://simplesharebuttons.com/contact/" target="_blank">let us know</a>. If you feel your website is worthy of appearing on our <a href="https://simplesharebuttons.com/showcase/" target="_blank">showcase page</a> do <a href="https://simplesharebuttons.com/contact/" target="_blank">get in touch</a>.</p>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						      </div>
						    </div>
						  </div>
						</div>';

		// close col
		$htmlFooter .= '</div>';

	// close row
	$htmlFooter .= '</footer>';

	// close container - opened in header
	$htmlFooter .= '</div>';

	// close ssba-admin-wrap - opened in header
	$htmlFooter .= '</div>';

	// return
	return $htmlFooter;
}

function ssba_admin_panel($arrSettings)
{
    // set var
    $htmlShareButtonsForm = '';

	// if user is accepting terms
	if (isset($_GET['accept-terms']) && $_GET['accept-terms'] == 'Y') {
		// save acceptance
		ssba_update_options(array('accepted_sharethis_terms' => 'Y'));

        // hide the notice for now, it will disappear upon reload
        $htmlShareButtonsForm .= '<style>#sharethis_terms_notice { display: none }.ssbp-facebook_save { background-color: #365397 !important; }</style>';

		// show notice
		add_action( 'admin_notices', 'accepted_sharethis_terms_notice' );
	}

	// include the forms helper
	include_once 'ssbp_forms.php';

	// prepare array of buttons
    $arrButtons = json_decode(get_option('ssba_buttons'), true);

	// get the font family needed
	$htmlShareButtonsForm .= '<style>'.ssba_get_font_family().'</style>';

	// if left to right
	if (is_rtl()) {
    	// move save button
    	$htmlShareButtonsForm .= '<style>.ssba-btn-save{left: 0!important;
                                        right: auto !important;
                                        border-radius: 0 5px 5px 0;}
                                </style>';
	}

	// add header
	$htmlShareButtonsForm .= ssba_admin_header();

	// initiate forms helper
	$ssbpForm = new ssbpForms;

	// opening form tag
	$htmlShareButtonsForm .= $ssbpForm->open(false);

	// heading
	$htmlShareButtonsForm .= '<h2>Share Buttons Settings</h2>';

	// if terms have just been accepted
	if (isset($_GET['accept-terms']) && $_GET['accept-terms'] == 'Y') {
		$htmlShareButtonsForm.= '<div class="alert alert-success text-center">
			<p>Thanks for accepting the terms, you can now take advantage of the great new features!</p>
		</div>';
	} elseif ($arrSettings['accepted_sharethis_terms'] != 'Y') {
        $htmlShareButtonsForm.= '<div class="alert alert-warning text-center">
			<p>The Facebook save button requires acceptance of the terms before it can be used. <a href="options-general.php?page=simple-share-buttons-adder&accept-terms=Y"><span class="button button-secondary">I accept</span></a></p>
		</div>';
    }

	// tabs
	$htmlShareButtonsForm .= '<ul class="nav nav-tabs">
								  <li class="active"><a href="#core" data-toggle="tab">Core</a></li>
								  <li><a href="#styling" data-toggle="tab">Styling</a></li>
								  <li><a href="#counters" data-toggle="tab">Counters</a></li>
								  <li><a href="#advanced" data-toggle="tab">Advanced</a></li>
								  <li class="dropdown">
								    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								      CSS <span class="caret"></span>
								    </a>
								    <ul class="dropdown-menu">
								      <li><a href="#css_additional" data-toggle="tab">Additional</a></li>
								      <li><a href="#css_custom" data-toggle="tab">Custom</a></li>
								    </ul>
								  </li>
								</ul>';
	// tab content div
	$htmlShareButtonsForm .= '<div id="ssbaTabContent" class="tab-content">';

		//======================================================================
		// 		CORE
		//======================================================================
		$htmlShareButtonsForm .= '<div class="tab-pane fade active in" id="core">';

			// basic info
			$htmlShareButtonsForm .= '<blockquote><p>The <b>simple</b> options you can see below are all you need to complete to get your <b>share buttons</b> to appear on your website. Once you\'re done here, you can further customise the share buttons via the Styling tab.</p></blockquote>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-12">';

				// locations array
				$locs = array(
					'Homepage' => array(
						'value' => 'ssba_homepage',
						'checked' => ($arrSettings['ssba_homepage'] == 'Y'  ? true : false)
					),
					'Pages'	=> array(
						'value' => 'ssba_pages',
						'checked' => ($arrSettings['ssba_pages'] == 'Y'  ? true : false)
					),
					'Posts' => array(
						'value' => 'ssba_posts',
						'checked' => ($arrSettings['ssba_posts'] == 'Y'  ? true : false)
					),
					'Excerpts' => array(
						'value' => 'ssba_excerpts',
						'checked' => ($arrSettings['ssba_excerpts'] == 'Y'  ? true : false)
					),
					'Categories/Archives' => array(
						'value' => 'ssba_cats_archs',
						'checked' => ($arrSettings['ssba_cats_archs'] == 'Y'  ? true : false)
					)
				);
				// locations
				$opts = array(
					'form_group' 	=> false,
					'label' 		=> 'Locations',
					'tooltip'		=> 'Enable the locations you wish for share buttons to appear',
					'value'			=> 'Y',
					'checkboxes'	=> $locs
				);
				$htmlShareButtonsForm .= $ssbpForm->ssbp_checkboxes($opts);

				// placement
	            $opts = array(
	                'form_group'	=> false,
	                'type'       	=> 'select',
	                'name'          => 'ssba_before_or_after',
	                'label'        	=> 'Placement',
	                'tooltip'       => 'Place share buttons before or after your content',
	                'selected'      => $arrSettings['ssba_before_or_after'],
	                'options'       => array(
	                                        'After'    => 'after',
	                                        'Before'    => 'before',
	                                        'Both'        => 'both',
	                                    ),
	            );
				$htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

	            // share text
	            $opts = array(
	                'form_group'    => false,
	                'type'          => 'text',
	                'placeholder'	=> 'Keeping sharing simple...',
	                'name'          => 'ssba_share_text',
	                'label'        	=> 'Share Text',
	                'tooltip'       => 'Add some custom text by your share buttons',
	                'value'         => $arrSettings['ssba_share_text'],
	            );
				$htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

				// networks
				$htmlShareButtonsForm .= '<label for="ssba_choices" class="control-label" data-toggle="tooltip" data-placement="right" data-original-title="Drag, drop and reorder those buttons that you wish to include">Networks</label>
											<div class="">';

					$htmlShareButtonsForm .= '<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
													<div class="ssbp-container">
														<ul id="ssbasort1" class="ssbp-list ssbaSortable">';
								$htmlShareButtonsForm .= getAvailableSSBA($arrSettings['ssba_selected_buttons'], $arrSettings);
							$htmlShareButtonsForm .= '</ul>
													</div>
												</div>';
						$htmlShareButtonsForm .= '<div class="well">';
						$htmlShareButtonsForm .= '<div class="ssba-well-instruction"><i class="fa fa-download"></i> Drop icons below</div>';
						$htmlShareButtonsForm .= '<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
													<div class="ssbp-container">
														<ul id="ssbasort2" class="ssba-include-list ssbp-list ssbaSortable">';
								$htmlShareButtonsForm .= getSelectedSSBA($arrSettings['ssba_selected_buttons'], $arrSettings);
							$htmlShareButtonsForm .= '</ul>
												</div>';
						$htmlShareButtonsForm .= '</div>';
					$htmlShareButtonsForm .= '</div>';
					$htmlShareButtonsForm .= '<input type="hidden" name="ssba_selected_buttons" id="ssba_selected_buttons" value="'.$arrSettings['ssba_selected_buttons'].'"/>';

                    // plus plug
                    $htmlShareButtonsForm .= '<div class="well text-center">';
                        $htmlShareButtonsForm .= '<h2>Simple Share Buttons Plus</h2>';
                        $htmlShareButtonsForm .= '<h5 class="margin-bottom">Get <strong>Xing</strong> and <strong>WhatsApp</strong> buttons...</h5>';
                        $htmlShareButtonsForm .= '<div class="ssbp-wrap ssbp--centred ssbp--theme-4">
                                                    <div class="ssbp-container">
                                                        <ul class="ssbp-list">
                                                            <li class="ssbp-option-item ui-sortable-handle" id="buffer"><a href="javascript:;" class="ssbp-btn ssbp-whatsapp"></a></li>
                                                            <li class="ssbp-option-item ui-sortable-handle" id="diggit"><a href="javascript:;" class="ssbp-btn ssbp-xing"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>';

                        $htmlShareButtonsForm .= '<a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=feature" target="_blank"><span class="btn btn-primary">Simple Share Buttons Plus</span></a>';
                        $htmlShareButtonsForm .= '<div class="ssba-spacer"></div>';
                        $htmlShareButtonsForm .= '<p>Extra buttons are just the tip of the iceberg... <strong>Simple Share Buttons Plus</strong> comes with a great deal of extra features, from <strong>GeoIP click tracking</strong> to <strong>mobile-responsive</strong> share bars. <a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=feature" target="_blank"><strong>Find out more here</strong></a></p>';
                        $htmlShareButtonsForm .= '<div class="ssba-spacer"></div>';
                    $htmlShareButtonsForm .= '</div>';

				$htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

		// close share buttons tab
		$htmlShareButtonsForm .= '</div>';

		//======================================================================
		// 		STYLING
		//======================================================================
		$htmlShareButtonsForm .= '<div class="tab-pane fade" id="styling">';

			// intro info
			$htmlShareButtonsForm .= '<blockquote><p>Use the options below to choose your favourite button set and how it should appear. <strong>If you wish to upload your own custom images</strong> please select \'Custom\' from the Image Set dropdown.</p></blockquote>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-7">';

			    // IMAGES --------------------------------
                $htmlShareButtonsForm .= '<div class="well">';

                    // heading
                    $htmlShareButtonsForm .= '<h3>Images</h3>';

    				// placement
    	            $opts = array(
    	                'form_group'	=> false,
    	                'type'       	=> 'select',
    	                'name'          => 'ssba_image_set',
    	                'label'        	=> 'Image set',
    	                'tooltip'       => 'Choose your favourite set of buttons, or set to custom to choose your own',
    	                'selected'      => $arrSettings['ssba_image_set'],
    	                'options'       => array(
    	                                        'Arbenta'   => 'arbenta',
    	                                        'Custom'    => 'custom',
    	                                        'Metal'     => 'metal',
    	                                        'Pagepeel'  => 'pagepeel',
    	                                        'Plain'     => 'plain',
    	                                        'Retro'     => 'retro',
    	                                        'Ribbons'   => 'ribbons',
    	                                        'Simple'    => 'simple',
    	                                        'Somacro'   => 'somacro',
    	                                    ),
    	            );
    				$htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

    				// custom images well
                    $htmlShareButtonsForm .= '<div id="ssba-custom-images" '.($arrSettings['ssba_image_set'] != 'custom' ? 'style="display: none;"' : NULL).'>';

                        // loop through each button
                        foreach ($arrButtons as $button => $arrButton) {
                            // enable custom images
                            $opts = array(
                                'form_group'    => false,
                                'type'          => 'image_upload',
                                'name'          => 'ssba_custom_'.$button,
                                'label'         => $arrButton['full_name'],
                                'tooltip'       => 'Upload a custom '.$arrButton['full_name'].' image',
                                'value'         => $arrSettings['ssba_custom_'.$button],
                            );
                            $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);
                        }

                    // close custom images
                    $htmlShareButtonsForm .= '</div>';

                    // button size
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'number_addon',
                        'addon'       	=> 'px',
                        'placeholder'   => '35',
                        'name'          => 'ssba_size',
                        'label'        	=> 'Button Size',
                        'tooltip'       => 'Set the size of your buttons in pixels',
                        'value'         => $arrSettings['ssba_size'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // alignment
                    $opts = array(
                        'form_group'	=> false,
                        'type'       	=> 'select',
                        'name'          => 'ssba_align',
                        'label'        	=> 'Alignment',
                        'tooltip'       => 'Align your buttons the way you wish',
                        'selected'      => $arrSettings['ssba_align'],
                        'options'       => array(
                                                'Left'      => 'left',
                                                'Centre'    => 'center',
                                                'Right'     => 'right',
                                            ),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // padding
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'number_addon',
                        'addon'       	=> 'px',
                        'placeholder'   => '10',
                        'name'          => 'ssba_padding',
                        'label'        	=> 'Padding',
                        'tooltip'       => 'Apply some space around your images',
                        'value'         => $arrSettings['ssba_padding'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // close images well
                $htmlShareButtonsForm .= '</div>';

                // SHARE TEXT STYLING --------------------------------
                $htmlShareButtonsForm .= '<div class="well">';

                    // heading
                    $htmlShareButtonsForm .= '<h3>Share Text</h3>';

                    // font colour
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'colorpicker',
                        'name'          => 'ssba_font_color',
                        'label'        	=> 'Font Colour',
                        'tooltip'       => 'Choose the colour of your share text',
                        'value'         => $arrSettings['ssba_font_color'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // font family
                    $opts = array(
                        'form_group'	=> false,
                        'type'       	=> 'select',
                        'name'          => 'ssba_font_family',
                        'label'        	=> 'Font Family',
                        'tooltip'       => 'Choose a font available or inherit the font from your website',
                        'selected'      => $arrSettings['ssba_font_family'],
                        'options'       => array(
                                                'Reenie Beanie'             => 'Reenie Beanie',
                                                'Indie Flower'              => 'Indie Flower',
                                                'Inherit from my website'   => '',
                                            ),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // font size
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'number_addon',
                        'addon'       	=> 'px',
                        'placeholder'   => '20',
                        'name'          => 'ssba_font_size',
                        'label'        	=> 'Font Size',
                        'tooltip'       => 'Set the size of the share text in pixels',
                        'value'         => $arrSettings['ssba_font_size'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // font weight
                    $opts = array(
                        'form_group'	=> false,
                        'type'       	=> 'select',
                        'name'          => 'ssba_font_weight',
                        'label'        	=> 'Font Weight',
                        'tooltip'       => 'Set the weight of the share text',
                        'selected'      => $arrSettings['ssba_font_weight'],
                        'options'       => array(
                                                'Bold'      => 'bold',
                                                'Normal'    => 'normal',
                                                'Light'     => 'light',
                                            ),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // text placement
                    $opts = array(
                        'form_group'	=> false,
                        'type'       	=> 'select',
                        'name'          => 'ssba_text_placement',
                        'label'        	=> 'Text placement',
                        'tooltip'       => 'Choose where you want your text to be displayed, in relation to the buttons',
                        'selected'      => $arrSettings['ssba_text_placement'],
                        'options'       => array(
                                                'Above' => 'above',
                                                'Left'  => 'left',
                                                'Right' => 'right',
                                                'Below' => 'below',
                                            ),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // close share text well
                $htmlShareButtonsForm .= '</div>';

                // CONTAINER TEXT STYLING --------------------------------
                $htmlShareButtonsForm .= '<div class="well">';

                    // heading
                    $htmlShareButtonsForm .= '<h3>Container</h3>';

                    // container padding
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'number_addon',
                        'addon'       	=> 'px',
                        'placeholder'   => '10',
                        'name'          => 'ssba_div_padding',
                        'label'        	=> 'Container Padding',
                        'tooltip'       => 'Add some padding to your share container',
                        'value'         => $arrSettings['ssba_div_padding'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // div background colour
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'colorpicker',
                        'name'          => 'ssba_div_background',
                        'label'        	=> 'Container Background Colour',
                        'tooltip'       => 'Choose the colour of your share container',
                        'value'         => $arrSettings['ssba_div_background'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // div border colour
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'colorpicker',
                        'name'          => 'ssba_div_border',
                        'label'        	=> 'Container Border Colour',
                        'tooltip'       => 'Choose the colour of your share container border',
                        'value'         => $arrSettings['ssba_div_border'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // container border width
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'number_addon',
                        'addon'       	=> 'px',
                        'placeholder'   => '1',
                        'name'          => 'ssba_border_width',
                        'label'        	=> 'Container Border Width',
                        'tooltip'       => 'Set the width of the share container border',
                        'value'         => $arrSettings['ssba_border_width'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // rounded container corners
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'checkbox',
                        'name'          => 'ssba_div_rounded_corners',
                        'label'        	=> 'Rounded Container Corners',
                        'tooltip'       => 'Switch on to enable rounded corners for your share container',
                        'value'         => 'Y',
                        'checked'       => ($arrSettings['ssba_div_rounded_corners'] == 'Y'  ? 'checked' : null),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // close container well
                $htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-5">';

			    // plus plug
			    $htmlShareButtonsForm .= '<div class="well">';
    			    $htmlShareButtonsForm .= '<h2>Get responsive</h2>';
    			    $htmlShareButtonsForm .= '<p class="lead">Looking for <strong>fixed</strong> and <strong>responsive</strong> share buttons?</p>';
    			    $htmlShareButtonsForm .= '<p>With <strong>Simple Share Buttons Plus</strong> you can pick from 10 different styles, that are all <strong>mobile-responsive</strong>. You can also pick icon/button colours and their hover colours!</p>';
    			    $htmlShareButtonsForm .= '<img class="ssba-responsive-img" src="' . plugins_url() . '/simple-share-buttons-adder/images/simple-share-buttons-mockups.png' . '" />';
    			    $htmlShareButtonsForm .= '<div class="text-center ssba-spacer"><span class="text-20 label label-success">Only $10</span></div>';
    			    $htmlShareButtonsForm .= '<a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=styling_tab" target="_blank"><span class="ssba-spacer btn btn-block btn-primary">Get Plus!</span></a>';
    			    $htmlShareButtonsForm .= '<div class="ssba-spacer"></div>';
			    $htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

		// close share buttons tab
		$htmlShareButtonsForm .= '</div>';

		//======================================================================
		// 		COUNTERS
		//======================================================================
		$htmlShareButtonsForm .= '<div class="tab-pane fade" id="counters">';

			// intro info
			$htmlShareButtonsForm .= '<blockquote><p>You can tweak share counter settings to your liking here.</p></blockquote>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-7">';

                // share count
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_show_share_count',
                    'label'        	=> 'Share Count',
                    'tooltip'       => 'Check the box if you wish to enable share counts. Enabling this option will slow down the loading of any pages that use share buttons',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_show_share_count'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // show count once
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_share_count_once',
                    'label'        	=> 'Show Once',
                    'tooltip'       => 'This option is recommended, it deactivates share counts for categories and archives allowing them to load more quickly',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_share_count_once'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // share counters style
                $opts = array(
                    'form_group'	=> false,
                    'type'       	=> 'select',
                    'name'          => 'ssba_share_count_style',
                    'label'        	=> 'Counters Style',
                    'tooltip'       => 'Pick a setting to style the share counters',
                    'selected'      => $arrSettings['ssba_share_count_style'],
                    'options'       => array(
                                            'Default'	=> 'default',
                                            'White'    	=> 'white',
                                            'Blue'    	=> 'blue',
                                        ),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // newsharecounts.com enable
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'twitter_newsharecounts',
                    'label'        	=> 'newsharecounts.com Counts for Twitter',
                    'tooltip'       => 'Switch on to enable the use of the newsharecounts.com API for Twitter share counts',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['twitter_newsharecounts'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // info
                $htmlShareButtonsForm .= '<p>You shall need to follow the instructions here before enabling this feature - <a target="_blank" href="http://newsharecounts.com/">newsharecounts.com</a>';

                // open sharedcount well
                $htmlShareButtonsForm .= '<div class="well">';

                    // sharedcount heading
                    $htmlShareButtonsForm .= '<h3>sharedcount.com</h3>';
                    $htmlShareButtonsForm .= '<p>Only necessary if you are experiencing issues with Facebook share counts. <a href="https://admin.sharedcount.com/admin/signup.php" target="_blank">Signup for your free account here</a>.</p>';

                    // sharedcount enable
                    $opts = array(
                        'form_group'	=> false,
                        'type'          => 'checkbox',
                        'name'          => 'sharedcount_enabled',
                        'label'        	=> 'Enable sharedcount.com API',
                        'tooltip'       => 'Enable if you wish to enable the use of the sharedcount.com API',
                        'value'         => 'Y',
                        'checked'       => ($arrSettings['sharedcount_enabled'] == 'Y'  ? 'checked' : null),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // sharedcount plan
                    $opts = array(
                        'form_group'	=> false,
                        'type'       	=> 'select',
                        'name'          => 'sharedcount_plan',
                        'label'        	=> 'sharedcount.com plan',
                        'tooltip'       => 'Select your sharedcount.com plan',
                        'selected'      => $arrSettings['sharedcount_plan'],
                        'options'       => array(
                            'Free'      => 'free',
                            'Plus'      => 'plus',
                            'Business'  => 'business',
                        ),
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                    // sharedcount api key
                    $opts = array(
                        'form_group'    => false,
                        'type'          => 'text',
                        'placeholder'	=> '9b17c12712c691491ef95f46c51ce3917118fdf9',
                        'name'          => 'sharedcount_api_key',
                        'label'        	=> 'sharedcount.com API Key',
                        'tooltip'       => 'Add some text included in an email when people share that way',
                        'value'         => $arrSettings['sharedcount_api_key'],
                    );
                    $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // close well
                $htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-5">';

			    // plus plug
			    $htmlShareButtonsForm .= '<div class="well">';
    			    $htmlShareButtonsForm .= '<h2>Get speed and accuracy</h2>';
    			    $htmlShareButtonsForm .= '<p class="lead">Do you want <strong>fast</strong> and <strong>consistent share counts</strong>?</p>';
    			    $htmlShareButtonsForm .= '<p>With <strong>Simple Share Buttons Plus</strong> share counts are saved for the length of time you set, drastically speeding up page load time. Plus also comes with use of the SSB API for <a href="https://simplesharebuttons.com/plus/features/api/"><strong>consistent Facebook share counts</strong></a></p>';
    			    $htmlShareButtonsForm .= '<img class="ssba-responsive-img" src="' . plugins_url() . '/simple-share-buttons-adder/images/simple-share-buttons-mockups.png' . '" />';
    			    $htmlShareButtonsForm .= '<div class="text-center ssba-spacer"><span class="text-20 label label-success">Only $10</span></div>';
    			    $htmlShareButtonsForm .= '<a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=counters_tab" target="_blank"><span class="ssba-spacer btn btn-block btn-primary">Get Plus!</span></a>';
    			    $htmlShareButtonsForm .= '<div class="ssba-spacer"></div>';
			    $htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

		// close share buttons tab
		$htmlShareButtonsForm .= '</div>';

		//======================================================================
		// 		ADVANCED
		//======================================================================
		$htmlShareButtonsForm .= '<div class="tab-pane fade" id="advanced">';

			// intro info
			$htmlShareButtonsForm .= '<blockquote><p>You\'ll find a number of advanced and miscellaneous options below, to get your share buttons functioning how you would like.</p></blockquote>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-7">';

			    // link to ssb
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_link_to_ssb',
                    'label'        	=> 'Share Text Link',
                    'tooltip'       => 'Enabling this will set your share text as a link to simplesharebuttons.com to help others learn of the plugin',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_link_to_ssb'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // content priority
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'number',
                    'placeholder'   => '10',
                    'name'          => 'ssba_content_priority',
                    'label'         => 'Content Priority',
                    'tooltip'       => 'Set the priority for your share buttons within your content. 1-10, default is 10',
                    'value'         => $arrSettings['ssba_content_priority'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // share in new window
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_share_new_window',
                    'label'        	=> 'Open links in a new window',
                    'tooltip'       => 'Disabling this will make links open in the same window',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_share_new_window'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // nofollow
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_rel_nofollow',
                    'label'        	=> 'Add rel="nofollow"',
                    'tooltip'       => 'Enable this to add nofollow to all share links',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_rel_nofollow'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // widget share text
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'Keeping sharing simple...',
                    'name'          => 'ssba_widget_text',
                    'label'        	=> 'Widget Share Text',
                    'tooltip'       => 'Add custom share text when used as a widget',
                    'value'         => $arrSettings['ssba_widget_text'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // email share text
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'Share by email...',
                    'name'          => 'ssba_email_message',
                    'label'        	=> 'Email Text',
                    'tooltip'       => 'Add some text included in an email when people share that way',
                    'value'         => $arrSettings['ssba_email_message'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

				// facebook app id
				$opts = array(
					'form_group'    => false,
					'type'          => 'text',
					'placeholder'	=> '123456789123',
					'name'          => 'facebook_app_id',
					'label'        	=> 'Facebook App ID',
					'tooltip'       => 'Enter your Facebook App ID, e.g. 123456789123',
					'value'         => $arrSettings['facebook_app_id'],
                    'disabled'      => ($arrSettings['accepted_sharethis_terms'] != 'Y' ? 'disabled' : null),
				);
				$htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

				// info
				$htmlShareButtonsForm .= '<p>You shall need to follow the instructions here before enabling this feature - <a target="_blank" href="https://developers.facebook.com/docs/apps/register">https://developers.facebook.com/docs/apps/register</a></p>';

				// facebook insights
				$opts = array(
					'form_group'	=> false,
					'type'          => 'checkbox',
					'name'          => 'facebook_insights',
					'label'        	=> 'Facebook Insights',
					'tooltip'       => 'Enable this feature to enable Facebook Insights',
					'value'         => 'Y',
					'checked'       => ($arrSettings['facebook_insights'] == 'Y'  ? 'checked' : null),
                    'disabled'      => ($arrSettings['accepted_sharethis_terms'] != 'Y' ? 'disabled' : null),
				);
				$htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

				// info
				$htmlShareButtonsForm .= '<p>You shall need have created and added a Facebook App ID above to make use of this feature</p>';

				// twitter share text
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'Shared by Twitter...',
                    'name'          => 'ssba_twitter_text',
                    'label'        	=> 'Twitter Text',
                    'tooltip'       => 'Add some custom text for when people share via Twitter',
                    'value'         => $arrSettings['ssba_twitter_text'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // flattr user id
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'davidsneal',
                    'name'          => 'ssba_flattr_user_id',
                    'label'        	=> 'Flattr User ID',
                    'tooltip'       => 'Enter your Flattr ID, e.g. davidsneal',
                    'value'         => $arrSettings['ssba_flattr_user_id'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // flattr url
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'https://simplesharebuttons.com',
                    'name'          => 'ssba_flattr_url',
                    'label'        	=> 'Flattr URL',
                    'tooltip'       => 'This option is perfect for dedicated sites, e.g. https://simplesharebuttons.com',
                    'value'         => $arrSettings['ssba_flattr_url'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // buffer text
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'text',
                    'placeholder'	=> 'Shared by Buffer...',
                    'name'          => 'ssba_buffer_text',
                    'label'        	=> 'Custom Buffer Text',
                    'tooltip'       => 'Add some custom text for when people share via Buffer',
                    'value'         => $arrSettings['ssba_buffer_text'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // pin featured images
                $opts = array(
                    'form_group'	=> false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_pinterest_featured',
                    'label'        	=> 'Pin Featured Images',
                    'tooltip'       => 'Force the use of featured images for posts/pages when pinning',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_pinterest_featured'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // default pinterest image
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'image_upload',
                    'name'          => 'ssba_default_pinterest',
                    'label'         => 'Default Pinterest Image',
                    'tooltip'       => 'Upload a default Pinterest image',
                    'value'         => $arrSettings['ssba_default_pinterest'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

			// close col
			$htmlShareButtonsForm .= '</div>';

			// COLUMN --------------------------------
			$htmlShareButtonsForm .= '<div class="col-sm-5">';

			    // plus plug
			    $htmlShareButtonsForm .= '<div class="well">';
    			    $htmlShareButtonsForm .= '<h2>Get even more</h2>';
    			    $htmlShareButtonsForm .= '<p class="lead">Hoping for <strong>even more</strong> features?</p>';
    			    $htmlShareButtonsForm .= '<p>With <strong>Simple Share Buttons Plus</strong> there is an ever-growing \'Advanced\' features section, including <strong>bit.ly</strong> URL shortening, <strong>Google Analytics Event Tracking</strong> and <strong>Share-Meta</strong> Functionality.</p>';
    			    $htmlShareButtonsForm .= '<img class="ssba-responsive-img" src="' . plugins_url() . '/simple-share-buttons-adder/images/simple-share-buttons-mockups.png' . '" />';
    			    $htmlShareButtonsForm .= '<div class="text-center ssba-spacer"><span class="text-20 label label-success">Only $10</span></div>';
    			    $htmlShareButtonsForm .= '<a href="https://simplesharebuttons.com/plus/?utm_source=adder&utm_medium=plugin_ad&utm_campaign=product&utm_content=advanced_tab" target="_blank"><span class="ssba-spacer btn btn-block btn-primary">Get Plus!</span></a>';
    			    $htmlShareButtonsForm .= '<div class="ssba-spacer"></div>';
			    $htmlShareButtonsForm .= '</div>';

			// close col
			$htmlShareButtonsForm .= '</div>';

		// close share buttons tab
		$htmlShareButtonsForm .= '</div>';

		//======================================================================
        // 		ADDITIONAL CSS
        //======================================================================
        $htmlShareButtonsForm .= '<div class="tab-pane fade" id="css_additional">';

            // intro info
            $htmlShareButtonsForm .= '<blockquote><p>The contents of the text area below will be appended to Simple Share Button Adder\'s CSS.</p></blockquote>';

            // column for padding
            $htmlShareButtonsForm .= '<div class="col-sm-12">';

                // additional css
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'textarea',
                    'rows'          => '15',
                    'class'         => 'code-font',
                    'name'          => 'ssba_additional_css',
                    'label'         => 'Additional CSS',
                    'tooltip'       => 'Add your own additional CSS if you wish',
                    'value'         => $arrSettings['ssba_additional_css'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

            // close column
            $htmlShareButtonsForm .= '</div>';

        // close additional css
        $htmlShareButtonsForm .= '</div>';

        //======================================================================
        // 		CUSTOM CSS
        //======================================================================
        $htmlShareButtonsForm .= '<div class="tab-pane fade" id="css_custom">';

            // intro info
            $htmlShareButtonsForm .= '<blockquote><p>If you want to take over control of your share buttons\' CSS entirely, turn on the switch below and enter your custom CSS. <strong>ALL of Simple Share Buttons Adder\'s CSS will be disabled</strong>.</p></blockquote>';

            // column for padding
            $htmlShareButtonsForm .= '<div class="col-sm-12">';

                // enable custom css
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'checkbox',
                    'name'          => 'ssba_custom_styles_enabled',
                    'label'         => 'Enable Custom CSS',
                    'tooltip'       => 'Switch on to disable all SSBA styles and use your own custom CSS',
                    'value'         => 'Y',
                    'checked'       => ($arrSettings['ssba_custom_styles_enabled'] == 'Y'  ? 'checked' : null),
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

                // custom css
                $opts = array(
                    'form_group'    => false,
                    'type'          => 'textarea',
                    'rows'          => '15',
                    'class'         => 'code-font',
                    'name'          => 'ssba_custom_styles',
                    'label'         => 'Custom CSS',
                    'tooltip'       => 'Enter in your own custom CSS for your share buttons',
                    'value'         => $arrSettings['ssba_custom_styles'],
                );
                $htmlShareButtonsForm .= $ssbpForm->ssbp_input($opts);

            // close column
            $htmlShareButtonsForm .= '</div>';

        // close custom css
        $htmlShareButtonsForm .= '</div>';

	// close tab content div
	$htmlShareButtonsForm .= '</div>';

	// close off form with save button
	$htmlShareButtonsForm .= $ssbpForm->close();

	// add footer
	$htmlShareButtonsForm .= ssba_admin_footer();

	echo $htmlShareButtonsForm;
}

// get an html formatted of currently selected and ordered buttons
function getSelectedSSBA($strSelectedSSBA, $arrSettings) {
    //variables
    $htmlSelectedList = '';

    // prepare array of buttons
    $arrButtons = json_decode(get_option('ssba_buttons'), true);

    // if there are some selected buttons
	if ($strSelectedSSBA != '') {

		// explode saved include list and add to a new array
		$arrSelectedSSBA = explode(',', $strSelectedSSBA);

		// check if array is not empty
		if ($arrSelectedSSBA != '') {

			// for each included button
			foreach ($arrSelectedSSBA as $strSelected) {
                // if share this terms haven't been accepted and it's the facebook save button then make the button look disabled
                $disabled = ($arrSettings['accepted_sharethis_terms'] != 'Y' && $strSelected == 'facebook_save' ? 'style="background-color:#eaeaea;"' : null);

				// add a list item for each selected option
				$htmlSelectedList .= '<li class="ssbp-option-item" id="'.$strSelected.'"><a title="'.$arrButtons[$strSelected]["full_name"].'" href="javascript:;" class="ssbp-btn ssbp-'.$strSelected.'" '.$disabled.'></a></li>';
			}
		}
	}

	// return html list options
	return $htmlSelectedList;
}

function getAvailableSSBA($strSelectedSSBA, $arrSettings)
{
	// variables
	$htmlAvailableList = '';

	// prepare array of buttons
	$arrButtons = json_decode(get_option('ssba_buttons'), true);

	// explode saved include list and add to a new array
	$arrSelectedSSBA = explode(',', $strSelectedSSBA);

	// extract the available buttons
	$arrAvailableSSBA = array_diff(array_keys($arrButtons), $arrSelectedSSBA);

	// check if array is not empty
	if($arrSelectedSSBA != '')
	{
		// for each included button
		foreach($arrAvailableSSBA as $strAvailable) {
            // if share this terms haven't been accepted and it's the facebook save button then make the button look disabled
            $disabled = ($arrSettings['accepted_sharethis_terms'] != 'Y' && $strAvailable == 'facebook_save' ? 'style="background-color:#eaeaea;"' : null);

			// add a list item for each available option
			$htmlAvailableList .= '<li class="ssbp-option-item" id="'.$strAvailable.'"><a title="'.$arrButtons[$strAvailable]["full_name"].'" href="javascript:;" class="ssbp-btn ssbp-'.$strAvailable.'" '.$disabled.'></a></li>';
		}
	}

	// return html list options
	return $htmlAvailableList;
}

// get ssbp font family
function ssba_get_font_family()
{
	return "@font-face {
				font-family: 'ssbp';
				src:url('".plugins_url()."/simple-share-buttons-adder/fonts/ssbp.eot?xj3ol1');
				src:url('".plugins_url()."/simple-share-buttons-adder/fonts/ssbp.eot?#iefixxj3ol1') format('embedded-opentype'),
					url('".plugins_url()."/simple-share-buttons-adder/fonts/ssbp.woff?xj3ol1') format('woff'),
					url('".plugins_url()."/simple-share-buttons-adder/fonts/ssbp.ttf?xj3ol1') format('truetype'),
					url('".plugins_url()."/simple-share-buttons-adder/fonts/ssbp.svg?xj3ol1#ssbp') format('svg');
				font-weight: normal;
				font-style: normal;

				/* Better Font Rendering =========== */
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}";
}
