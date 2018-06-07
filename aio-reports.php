<div class="wrap aio_admin_wrapper">
<?php 
$nonce = wp_create_nonce("clock_in_nonce");
$link = admin_url('admin-ajax.php?action=clock_in_nonce&post_id='.$post->ID.'&nonce='.$nonce);
$logo = plugins_url('/images/logo.png', __FILE__);
?>    
    <a href="https://codebangers.com" target="_blank"><img src="<?php echo $logo; ?>" style="width:15%;"></a>
    <hr>
    <h1>Reports</h1>
    <h2 class="nav-tab-wrapper">
        <?php $tab = $_GET['tab'];
if ($tab == null) {
    $tab = "simple_report";
}
?>
        <a href="?page=aio-reports-sub&tab=simple_report" class="nav-tab<?php if ($tab == "simple_report") {
    echo " nav-tab-active";
}?>">
            <i class="dashicons dashicons-admin-users"></i>
            <?php _e('Date Range');?>
        </a>
        <a href="?page=aio-reports-sub&tab=custom_reports" class="nav-tab<?php if ($tab == "custom_reports") {
    echo " nav-tab-active";
}?>">
            <i class="dashicons dashicons-menu"></i>
            <?php _e('Report Wizard');?>
        </a>
    </h2>
<?php
 
if ($tab == "simple_report") {
    include("aio-simple-report.php");
}

if ($tab == "custom_reports"){
    include("aio-report-wizard.php");
}

?>
</div>