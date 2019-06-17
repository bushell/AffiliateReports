<?php
/*
Plugin Name: Affliate Report
Plugin URI:
Description: Total commissions for the month
Version: 0.1
Author: Gambling Ninja
Author URI: https://gamblingninja.com
License: GPL2
*/
require_once( plugin_dir_path( __FILE__ ) . '/custom_widgets.php' );

class Wptuts_Dashboard_Widgets {

    function __construct() {
        //add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    }

    function remove_dashboard_widgets() {

    }

    function add_dashboard_widgets() {
	  global $custom_dashboard_widgets;

	    foreach ( $custom_dashboard_widgets as $widget_id => $options ) {
		wp_add_dashboard_widget(
		    $widget_id,
		    $options['title'],
		    $options['callback']
		);
	    }
    }

}

$wdw = new Wptuts_Dashboard_Widgets();


// create custom plugin settings menu
add_action('admin_menu', 'affliate_report_create_menu');

function affliate_report_create_menu() {

	//create new top-level menu
	add_menu_page('Affliate Reports Plugin Settings', 'Affliate Reports Settings', 'administrator', __FILE__, 'affliate_report_settings_page' , plugins_url('/images/icon.png', __FILE__) );

	//call register settings function
	add_action( 'admin_init', 'register_affliate_report_settings' );
}


function register_affliate_report_settings() {
	//register our settings
	//register_setting( 'affliate-report-settings-group', 'key1' );
	//register_setting( 'affliate-report-settings-group', 'key2' );
	//register_setting( 'affliate-report-settings-group', 'mfortune_domain' );
	//register_setting( 'affliate-report-settings-group', 'skybet_domain' );
    register_setting( 'affliate-report-settings-group', 'casumo_domain' );
	//register_setting( 'affliate-report-settings-group', 'chart_data' );
	//register_setting( 'affliate-report-settings-group', 'chart_data2' );
    register_setting( 'affliate-report-settings-group', 'casumo_views' );
    register_setting( 'affliate-report-settings-group', 'casumo_clicks' );
    register_setting( 'affliate-report-settings-group', 'casumo_revenue' );

    register_setting( 'affliate-report-settings-group', 'casumo_ctr' );
    register_setting( 'affliate-report-settings-group', 'casumo_signups' );

    register_setting( 'affliate-report-settings-group', 'casumo_key' );
    register_setting( 'affliate-report-settings-group', 'casumo_date_to' );
    register_setting( 'affliate-report-settings-group', 'casumo_date_from' );


}

function affliate_report_settings_page() {
?>
<div class="wrap">
<h1>Affliate Reports</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'affliate-report-settings-group' ); ?>
    <?php do_settings_sections( 'affliate-report-settings-group' ); ?>
    <table class="form-table table">

    	<tr valign="top">
        <th scope="row">Domain</th>
        <td>
        <!--<?php $mfortunevalue =  esc_attr( get_option('mfortune_domain') ); ?>
        <input id="mfortune_domain" <?php echo $mfortunevalue=='mfortune'?'checked=""':'' ?> type="checkbox" name="mfortune_domain" value="mfortune" />mFortune
        <?php $skybetvalue =  esc_attr( get_option('skybet_domain') ); ?>
        <input id="skybet_domain" <?php echo $skybetvalue=='skybet'?'checked=""':'' ?> type="checkbox" name="skybet_domain" value="skybet" />Sky Bet-->
        <?php $casumovalue =  esc_attr( get_option('casumo_domain') ); ?>
        <input id="casumo_domain" <?php echo $casumovalue=='casumo'?'checked=""':'' ?> type="checkbox" name="casumo_domain" value="casumo" />Casumo
        </td>
        </tr>

        <!--<tr id="mfortunekey" valign="top">
        <th scope="row">mFortune Key</th>
        <td><input type="text" name="key1" value="<?php echo esc_attr( get_option('key1') ); ?>" /></td>
        </tr>



        <tr id="mfortunedata" valign="top">
        <th scope="row">Data</th>
        <td>
        <?php $chartdatavalue =  esc_attr( get_option('chart_data') ); ?>
        <input <?php echo $chartdatavalue=='cpac'?'checked=""':'' ?> type="radio" name="chart_data" value="cpac" />CPA Commision
        <input <?php echo $chartdatavalue=='netrevenue'?'checked=""':'' ?> type="radio" name="chart_data" value="netrevenue" />Net Revenue
        </td>
        </tr>



        <tr id="skybetkey" valign="top">
        <th scope="row">Sky Bet Key</th>
        <td><input type="text" name="key2" value="<?php echo esc_attr( get_option('key2') ); ?>" /></td>
        </tr>

        <tr id="skybetdata" valign="top">
        <th scope="row">Data</th>
        <td>
        <?php $chartdatavalue =  esc_attr( get_option('chart_data2') ); ?>
        <input <?php echo $chartdatavalue=='cpac'?'checked=""':'' ?> type="radio" name="chart_data2" value="cpac" />CPA Commision
        <input <?php echo $chartdatavalue=='netrevenue'?'checked=""':'' ?> type="radio" name="chart_data2" value="netrevenue" />Net Revenue
        </td>
        </tr>-->

        <tr id="casumokey" valign="top">
            <th scope="row">Casumo Key</th>
            <td><input type="text" name="casumo_key" style="width:330px;" value="<?php echo esc_attr( get_option('casumo_key') ); ?>" /></td>
        </tr>

        <tr id="casumodate" valign="top">
            <th scope="row">Date</th>
            <td>
                <input placeholder="Date from" type="date" name="casumo_date_from" value="<?php echo esc_attr( get_option('casumo_date_from') ); ?>" />
                <input placeholder="Date to" type="date" name="casumo_date_to" value="<?php echo esc_attr( get_option('casumo_date_to') ); ?>" />
            </td>
        </tr>

        <tr id="casumodata" valign="top">
        <th scope="row">Data</th>
        <td>
            <?php $casumo_revenue =  esc_attr( get_option('casumo_revenue') ); ?>
            <input <?php echo $casumo_revenue?'checked=""':'' ?> type="checkbox" name="casumo_revenue" />Net Revenue</br>
            <?php $casumo_views =  esc_attr( get_option('casumo_views') ); ?>
            <input <?php echo $casumo_views?'checked=""':'' ?> type="checkbox" name="casumo_views" />Views</br>
            <?php $casumo_clicks =  esc_attr( get_option('casumo_clicks') ); ?>
            <input <?php echo $casumo_clicks?'checked=""':'' ?> type="checkbox" name="casumo_clicks" />Clicks</br>
            <?php $casumo_ctr =  esc_attr( get_option('casumo_ctr') ); ?>
            <input <?php echo $casumo_ctr?'checked=""':'' ?> type="checkbox" name="casumo_ctr" />CTR</br>
            <?php $casumo_signups =  esc_attr( get_option('casumo_signups') ); ?>
            <input <?php echo $casumo_signups?'checked=""':'' ?> type="checkbox" name="casumo_signups" />Signups
        </td>
        </tr>

    </table>

    <?php submit_button(); ?>

</form>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#mfortune_domain').change(function() {
	        if(jQuery(this).is(":checked")) {
	            jQuery("#mfortunekey").show();
	            jQuery("#mfortunedata").show();
	        }
	        else{
				jQuery("#mfortunekey").hide();
	            jQuery("#mfortunedata").hide();
	        }
    	});
    	jQuery('#skybet_domain').change(function() {
	        if(jQuery(this).is(":checked")) {
	            jQuery("#skybetkey").show();
	            jQuery("#skybetdata").show();
	        }
	        else{
				jQuery("#skybetkey").hide();
	            jQuery("#skybetdata").hide();
	        }
    	});
        jQuery('#casumo_domain').change(function() {
	        if(jQuery(this).is(":checked")) {
	            jQuery("#casumokey").show();
	            jQuery("#casumodata").show();
	        }
	        else{
				jQuery("#casumokey").hide();
	            jQuery("#casumodata").hide();
	        }
    	});
        jQuery('#casumo_domain').trigger('change');
    	jQuery('#mfortune_domain').trigger('change');
    	jQuery('#skybet_domain').trigger('change');
	});
</script>

<?php } ?>
