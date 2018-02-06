<?php
/**
 * Admin panel template.
 *
 * The template wrapper for the admin panel.
 *
 * @package SimpleShareButtonsAdder
 */

echo $this->admin_header(); // WPCS: XSS ok.
echo $this->forms->open( false ); // WPCS: XSS ok.
?>
<h2><?php echo esc_html__( 'Settings', 'simple-share-buttons-adder' ); ?></h2>

<?php
// If terms have just been accepted.
if ( isset( $_GET['accept-terms'] ) && 'Y' === $_GET['accept-terms'] ) { // WPCS: CSRF ok. ?>
	<div class="alert alert-success text-center">
		<p><?php echo esc_html__( 'Thanks for accepting the terms, you can now take advantage of the great new features!', 'simple-share-buttons-adder' ); ?></p>
	</div>
<?php } elseif ( 'Y' !== $arr_settings['accepted_sharethis_terms'] ) { ?>
	<div class="alert alert-warning text-center">
		<p>
			<?php echo esc_html__( 'The Facebook save button requires acceptance of the terms before it can be used.', 'simple-share-buttons-adder' ); ?>
			<a href="options-general.php?page=simple-share-buttons-adder&accept-terms=Y">
				<span class="button button-secondary">
					<?php echo esc_html__( 'I accept', 'simple-share-buttons-adder' ); ?>
				</span>
			</a>
		</p>
	</div>
<?php } ?>
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#classic-share-buttons" data-toggle="tab"><?php echo esc_html__( 'Classic Share Buttons', 'simple-share-buttons-adder' ); ?></a>
	</li>
</ul>
<div id="ssbaTabContent" class="tab-content">
	<?php include_once( "{$this->plugin->dir_path}/templates/classic-tab.php" ); ?>
</div>

<?php
echo $this->forms->close(); // WPCS: XSS ok.
echo $this->admin_footer(); // WPCS: XSS ok.
?>
