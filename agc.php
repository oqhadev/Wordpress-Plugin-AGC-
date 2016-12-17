<?php
/**
 * Plugin Name: AGC(Auto Geerated Content) Info Kerja
 * Description: AGC infokerja.naker.go.id
 * Version: 1.0.0
 * Author: oqhatime
 * Author URI: https://oqhadev.com
 * License: GPL2
 */



add_action('admin_menu', 'loker_agc_create_menu');

function loker_agc_create_menu() {
	add_menu_page('Loker AGC Setting', 'Loker Agc','administrator', __FILE__, 'my_cool_plugin_settings_page' , plugins_url('z.png', __FILE__) );
	add_action( 'admin_init', 'register_loker_agc_settings' );
}

function register_loker_agc_settings() {
	register_setting( 'loker-agc', 'intervalUpdate' );
    register_setting( 'loker-agc', 'postedby' );
    register_setting( 'loker-agc', 'startaAgc' );
    register_setting( 'loker-agc', 'aAgc' );
}

function my_cool_plugin_settings_page() {
	?>
<div class="wrap">
<h2>Info Loker AGC Setting </h2>

<form method="post" action="options.php">
    <?php settings_fields( 'loker-agc' ); ?>
    <?php do_settings_sections( 'loker-agc' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Interval Update<small> *in minutes</small></th>
        <td><input type="text" name="intervalUpdate" onkeydown="return ( event.ctrlKey || event.altKey 
                    || (47<event.keyCode && event.keyCode<58 && event.shiftKey==false) 
                    || (95<event.keyCode && event.keyCode<106)
                    || (event.keyCode==8) || (event.keyCode==9) 
                    || (event.keyCode>34 && event.keyCode<40) 
                    || (event.keyCode==46) )" value="<?php echo 
        esc_attr( get_option('intervalUpdate') ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Posted by<small> *to change it just save, with ur chosen account</small></th>
        <td>
        <?php $user_info = get_userdata(get_option('postedby'));
            echo  $user_info->user_login ;
        ?>
         id(<?php echo  esc_attr( get_option('postedby') ); ?>)
        <input type="hidden" name="postedby" value="<?php echo get_current_user_id(); ?>"/></td>
        </tr>


        <tr valign="top">
        <th scope="row">Manual Grabber<small> *get content from start until start+300 *will active, every u open/see homepage, script will run,and make web run slowly</th>

        <td>
        Active 
        <input type="checkbox" 

        <?php if (get_option('aAgc')=="active"){echo 'checked="checked"';}?>
        name="aAgc" value="active">

        Start From <input type="text" name="startaAgc" onkeydown="return ( event.ctrlKey || event.altKey 
                    || (47<event.keyCode && event.keyCode<58 && event.shiftKey==false) 
                    || (95<event.keyCode && event.keyCode<106)
                    || (event.keyCode==8) || (event.keyCode==9) 
                    || (event.keyCode>34 && event.keyCode<40) 
                    || (event.keyCode==46) )" value="<?php echo 
        esc_attr( get_option('startaAgc') ); ?>" size="4" />
        </td>
        </tr>

         
    </table>
    
    <?php submit_button(); ?>

</form>
</div>



<?php 


} 

add_action( 'wp_head', 'infokerja' );

function infokerja() {
    require_once(ABSPATH . 'wp-config.php'); 
    require_once(ABSPATH . 'wp-includes/wp-db.php'); 
    require_once(ABSPATH . 'wp-admin/includes/taxonomy.php'); 
	global $current_user;
    get_currentuserinfo();
	if (get_option('aAgc')=="active"){aAgc();}
}

add_filter('cron_schedules', 'new_interval');

function new_interval($interval) {
    $interval['intervalUpdate'] = array('interval' => get_option('intervalUpdate')*60, 'display' => 'intervalUpdate');
    return $interval;
}

if (!wp_next_scheduled('AgcUpdate')) {
wp_schedule_event( time(), 'intervalUpdate', 'AgcUpdate' );
}

add_action ( 'AgcUpdate', 'agc' );

function get_match($regex,$content) {
	preg_match_all($regex,$content,$matches,PREG_SET_ORDER);
	return $matches;
}

function get_data($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function post($title,$content,$postname,$category_id) {
    $date = date('Y/m/d H:i:s');
    $my_post = array(
    'post_title'    => $title,
    'post_content'  => $content,
    'post_name'     => $postname,
    'post_status'   => 'publish',
    'post_author'   => get_option('postedby'),
    'post_category' => array($category_id)
    );
    wp_insert_post( $my_post );
}

function aAgc(){
	$year=date('Y');
	$mo=date('m');
        
        switch ($mo) {
        case '01':
            $mo="Januari";
            break;
        case '02':
            $mo="Februari";
            break;
        case '03':
            $mo="Maret";
            break;
        case '04':
            $mo="April";
            break;
        case '05':
            $mo="Mei";
            break;
        case '06':
            $mo="Juni";
            break;
        case '07':
            $mo="Juli";
            break;
        case '08':
            $mo="Agustus";
            break;
        case '09':
            $mo="September";
            break;
        case '10':
            $mo="Oktober";
            break;
        case '11':
            $mo="November";
            break;
        case '12':
            $mo="Desember";
            break;
    
        default:
            $mo="";
            break;
        }


        $start=get_option('startaAgc');
        $end=$start+300;
        for ($wew=$start; $wew <= $end ; $wew++) { 
           
            $urlContent = "http://infokerja.naker.go.id/portalmodule/lowongan/detail?idLowongan=".$wew;
            $getContent = get_data($urlContent);
            $lowonganContent = get_match('/<input type="hidden" name="nmLowongan" id="nmLowongan" value="(.*)"/isU',$getContent);
            $dpContent = get_match('/<h5 class="text-uppercase">Deskripsi Pekerjaan<\/h5>(.*)<br/isU',$getContent);
            $ktContent = get_match('/<h5 class="text-uppercase">Kebutuhan Tenaga<\/h5>(.*)<br/isU',$getContent);
            $bwContent = get_match('/<h5 class="text-uppercase">Batas Waktu<\/h5>(.*)<br/isU',$getContent);
            $pendidikanContent = get_match('/<h5 class="text-uppercase">Pendidikan<\/h5>(.*)<br/isU',$getContent);
            $gjContent = get_match('/<h5 class="text-uppercase">Gol.Jabatan<\/h5>(.*)<br/isU',$getContent);
            $suContent = get_match('/<h5>Sektor Usaha<\/h5>[^>]*<p>(.*)<\/p>/isU',$getContent);
            $perusahaanContent = get_match('/<input type="hidden" name="nmPerusahaan" id="nmPerusahaan" value="(.*)"/isU',$getContent);
            $cpContent = get_match('/<h5 class="text-uppercase">Contact Person<\/h5>(.*)<\/div/isU',$getContent);
            $puContent = get_match('/<h1 class="heading-title company-overview">Persyaratan Umum<\/h1>(.*)<\/div/isU',$getContent);
            $pkContent = get_match('/<h1 class="heading-title company-overview">Persyaratan Khusus<\/h1>(.*)                                              <\/div/isU',$getContent);
            $berkasContent = get_match('/<h1 class="heading-title map">Kelengkapan Berkas Lamaran<\/h1>(.*)<\/div/isU',$getContent);
            $lokasiContent = get_match('/<h5>Alamat<\/h5>(.*)<\/div>/isU',$getContent);
            $hpContent = get_match('/<h5>Telp\/HP<\/h5>(.*)<\/div>/isU',$getContent);

            foreach ($lowonganContent as $i => $vall) {
                $alamat=str_replace("\n","",strip_tags($lokasiContent[$i][1]));
                $category = get_match('/ , (.*) ,/isU',$alamat);
                $hp=str_replace("\n","",strip_tags($hpContent[$i][1]));
                foreach ($category as $i => $valll) {
                $category =$valll[1];
                }
            
    

            $jobb =str_replace("'","",$vall[1]);
                $jobb =str_replace("\"","",$jobb);
                $jobb =str_replace(" ","-",$jobb);
                $jobb =str_replace("'","",$jobb);
                

            $peru =str_replace("'","",$perusahaanContent[$i][1]);
                $peru =str_replace("\"","",$peru);
                $peru =str_replace(" ","-",$peru);
                $peru =str_replace("'","",$peru);
                $peru =str_replace(".","",$peru);

            $permalink=$jobb.'-'.$peru.'-'.$mo.'-'.$year.'-'.rand(100,999 );
            $judul = $perusahaanContent[$i][1].' '.$vall[1].' '.$mo.' '.$year;

            if  (gettype($category)<>'string'){
            continue;
            }
    
            $category_id = get_cat_ID($category);
                if  ($category_id=='0'){
                    wp_create_category($category);
                }        
            $category_id = get_cat_ID($category);
            if ($perusahaanContent[$i][1]==""){
                continue;
            }

            $fullcontent='Lowongan : '.$vall[1].'</br>';
            $fullcontent=$fullcontent.'Kebutuhan Tenaga : '.str_replace("\n","",strip_tags($ktContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Batas Waktu : '.str_replace("\n","",strip_tags($bwContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Pendidikan : '.str_replace("\n","",strip_tags($pendidikanContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Gol.jabatan : '.str_replace("\n","",strip_tags($gjContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'sektor usaha : '.$suContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'Perusahaan : '.$perusahaanContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'Alamat/Kode Pos : '.$alamat.'</br>';
            $fullcontent=$fullcontent.'Telp/HP : '.$hp.'</br>';
            $fullcontent=$fullcontent.'Deskripsi Pekerjaan : '.$dpContent[$i][1].'</br>';

            $fullcontent=$fullcontent.'Contact Person : '.str_replace("\n","",strip_tags($cpContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'<h2>Persyaratan</h2></br>';
            $fullcontent=$fullcontent.'Berkas : '.$berkasContent[$i][1].'';
            $fullcontent=$fullcontent.'Persyaratan Khusus : '.str_replace("\n","",strip_tags($pkContent[$i][1])).'</br>';
        	//$fullcontent=$fullcontent.'Persyaratan Umum : '.$puContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'<h2>Cara Melamar</h2></br>';
            $fullcontent=$fullcontent.$alamat.'</br>Telp/HP : '.$hp.'</br>';

            global $wpdb;           
            $titles = $wpdb->get_col("SELECT post_title
                                          FROM $wpdb->posts
                                          WHERE post_title = '".$judul."'
                                          ");
                if  ($wpdb->num_rows=='0'){
                    post($judul,$fullcontent,$permalink,$category_id);
                }               
                else {
                }
            }
        }
	update_option( 'aAgc', '' );
}

function agc() {
        $url = "http://infokerja.naker.go.id/portalmodule/lowongan/lowongannasional";
        $agc = get_data($url);
        $id = get_match('/onClick="detailLowongan\((.*)\)/isU',$agc);
        $tanggal = get_match('/<p class="job_valid"><font color="blue"><h4>Lowongan Terdaftar Tanggal : (.*) /isU',$agc);
        

        $today=date('d');
        $year=date('Y');
        $mo=date('m');
        
        switch ($mo) {
        case '01':
            $mo="Januari";
            break;
        case '02':
            $mo="Februari";
            break;
        case '03':
            $mo="Maret";
            break;
        case '04':
            $mo="April";
            break;
        case '05':
            $mo="Mei";
            break;
        case '06':
            $mo="Juni";
            break;
        case '07':
            $mo="Juli";
            break;
        case '08':
            $mo="Agustus";
            break;
        case '09':
            $mo="September";
            break;
        case '10':
            $mo="Oktober";
            break;
        case '11':
            $mo="November";
            break;
        case '12':
            $mo="Desember";
            break;
    
        default:
            $mo="";
            break;
        }

        $now= date('Y-m-d');
        
        foreach ($id as $i => $val) {   
            
        if ($i==4){break;}      
            $urlContent = "http://infokerja.naker.go.id/portalmodule/lowongan/detail?idLowongan=".$val[1];
            $getContent = get_data($urlContent);
            $lowonganContent = get_match('/<input type="hidden" name="nmLowongan" id="nmLowongan" value="(.*)"/isU',$getContent);
            $dpContent = get_match('/<h5 class="text-uppercase">Deskripsi Pekerjaan<\/h5>(.*)<br/isU',$getContent);
            $ktContent = get_match('/<h5 class="text-uppercase">Kebutuhan Tenaga<\/h5>(.*)<br/isU',$getContent);
            $bwContent = get_match('/<h5 class="text-uppercase">Batas Waktu<\/h5>(.*)<br/isU',$getContent);
            $pendidikanContent = get_match('/<h5 class="text-uppercase">Pendidikan<\/h5>(.*)<br/isU',$getContent);
            $gjContent = get_match('/<h5 class="text-uppercase">Gol.Jabatan<\/h5>(.*)<br/isU',$getContent);
            $suContent = get_match('/<h5>Sektor Usaha<\/h5>[^>]*<p>(.*)<\/p>/isU',$getContent);
            $perusahaanContent = get_match('/<input type="hidden" name="nmPerusahaan" id="nmPerusahaan" value="(.*)"/isU',$getContent);
            $cpContent = get_match('/<h5 class="text-uppercase">Contact Person<\/h5>(.*)<\/div/isU',$getContent);
            $puContent = get_match('/<h1 class="heading-title company-overview">Persyaratan Umum<\/h1>(.*)<\/div/isU',$getContent);
            $pkContent = get_match('/<h1 class="heading-title company-overview">Persyaratan Khusus<\/h1>(.*)                                              <\/div/isU',$getContent);
            $berkasContent = get_match('/<h1 class="heading-title map">Kelengkapan Berkas Lamaran<\/h1>(.*)<\/div/isU',$getContent);
            $lokasiContent = get_match('/<h5>Alamat<\/h5>(.*)<\/div>/isU',$getContent);
            $hpContent = get_match('/<h5>Telp\/HP<\/h5>(.*)<\/div>/isU',$getContent);

            foreach ($lowonganContent as $i => $vall) {

                $alamat=str_replace("\n","",strip_tags($lokasiContent[$i][1]));
            $category = get_match('/ , (.*) ,/isU',$alamat);
            $hp=str_replace("\n","",strip_tags($hpContent[$i][1]));
            foreach ($category as $i => $valll) {
            $category =$valll[1];
            }
            
            $jobb =str_replace("'","",$vall[1]);
                $jobb =str_replace("\"","",$jobb);
                $jobb =str_replace(" ","-",$jobb);
                $jobb =str_replace("'","",$jobb);
                

            $peru =str_replace("'","",$perusahaanContent[$i][1]);
                $peru =str_replace("\"","",$peru);
                $peru =str_replace(" ","-",$peru);
                $peru =str_replace("'","",$peru);
                $peru =str_replace(".","",$peru);

            $permalink=$jobb.'-'.$peru.'-'.$mo.'-'.$year.'-'.rand(100,999 );
            $judul = $perusahaanContent[$i][1].' '.$vall[1].' '.$mo.' '.$year;

            if  (gettype($category)<>'string'){
            continue;
            }
    
            $category_id = get_cat_ID($category);
                if  ($category_id=='0'){
                    wp_create_category($category);
                }        
            $category_id = get_cat_ID($category);
            if ($perusahaanContent[$i][1]==""){
                continue;

            }
            $fullcontent='Lowongan : '.$vall[1].'</br>';
            $fullcontent=$fullcontent.'Kebutuhan Tenaga : '.str_replace("\n","",strip_tags($ktContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Batas Waktu : '.str_replace("\n","",strip_tags($bwContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Pendidikan : '.str_replace("\n","",strip_tags($pendidikanContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'Gol.jabatan : '.str_replace("\n","",strip_tags($gjContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'sektor usaha : '.$suContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'Perusahaan : '.$perusahaanContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'Alamat/Kode Pos : '.$alamat.'</br>';
            $fullcontent=$fullcontent.'Telp/HP : '.$hp.'</br>';
            $fullcontent=$fullcontent.'Deskripsi Pekerjaan : '.$dpContent[$i][1].'</br>';

            $fullcontent=$fullcontent.'Contact Person : '.str_replace("\n","",strip_tags($cpContent[$i][1])).'</br>';
            $fullcontent=$fullcontent.'<h2>Persyaratan</h2></br>';
            $fullcontent=$fullcontent.'Berkas : '.$berkasContent[$i][1].'';
            $fullcontent=$fullcontent.'Persyaratan Khusus : '.str_replace("\n","",strip_tags($pkContent[$i][1])).'</br>';
        //  $fullcontent=$fullcontent.'Persyaratan Umum : '.$puContent[$i][1].'</br>';
            $fullcontent=$fullcontent.'<h2>Cara Melamar</h2></br>';
            $fullcontent=$fullcontent.$alamat.'</br>Telp/HP : '.$hp.'</br>';

            global $wpdb;           
            $titles = $wpdb->get_col("SELECT post_title
                                          FROM $wpdb->posts
                                          WHERE post_title = '".$judul."'
                                          ");
                if  ($wpdb->num_rows=='0'){
                    post($judul,$fullcontent,$permalink,$category_id);

                }               
                else {
                }


                
            }
        }
}


?>