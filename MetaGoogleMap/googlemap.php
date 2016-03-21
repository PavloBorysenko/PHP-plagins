<?php
add_action( 'add_meta_boxes', 'bory_googe_maps' );
add_action('save_post', 'bory_save_googe_maps');
//Гугл карты
function bory_googe_maps() {



    add_meta_box(
        'bory-maps',
        'Отметка на карте google(выводится только для категорий anons)',
        'bory_mbox_form_maps',
        'post',
        'side',
        'high'
    );

}



//Описание работы meta Box
function bory_mbox_form_maps($post ) {

    $lat= get_post_meta($post->ID, 'lat',true);
    $lng= get_post_meta($post->ID, 'lng',true);
    $zoom= get_post_meta($post->ID, 'zoom',true);
    $title= get_post_meta($post->ID, 'title_marker',true);
    $show= get_post_meta($post->ID, 'show',true);

    wp_nonce_field( 'bory_inner_mbox2', 'bory_inner_mbox_box_nonce2' );
    if(empty($zoom)){
        $zoom=8;
    }



    // вывод формы на экран
    ?>
    <div>

        <div>
            <p>Настройки Google maps</p>
            <input name='lat' type='text' class='prod_title' value='<?php echo $lat ?>' placeholder="Координата 1"/>
            <input name='lng' type='text' class='prod_title' value='<?php echo $lng ?>' placeholder="Координата 2"/>
            <input name='zoom' type='text' class='prod_title' value='<?php echo $zoom ?>' placeholder="Увеличение от 1до 14"/>
            <input name='title_marker' type='text' class='prod_title' value='<?php echo $title ?>' placeholder="Название маркера"/>
            <p><input type="checkbox" name="show" value="true"<?php if($show=="true") echo "checked"?> >Показывать карту?<p>
        </div>
    </div>

<?php
}

function bory_save_googe_maps($post_id){

    if ( ! isset( $_POST['bory_inner_mbox_box_nonce2'] ) )// Есть частный случай?
        return $post_id;

    $nonce = $_POST['bory_inner_mbox_box_nonce2'];

    if ( ! wp_verify_nonce( $nonce, 'bory_inner_mbox2' ) )//Валидность частного случая.
        return $post_id;

    if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE )// проверка на автосохранение
        return $post_id;

    if ( 'page' == $_POST['post_type'] ) {// проверка полномочий

        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    }else {

        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }



    $l = sanitize_text_field( $_POST['lat'] );
    $ln = sanitize_text_field( $_POST['lng'] );
    $z = sanitize_text_field( $_POST['zoom'] );
    $t = sanitize_text_field( $_POST['title_marker'] );
    $s = sanitize_text_field( $_POST['show'] );

    update_post_meta( $post_id, 'lat', $l);
    update_post_meta( $post_id, 'lng', $ln);
    update_post_meta( $post_id, 'zoom', $z);
    update_post_meta( $post_id, 'title_marker', $t);
    update_post_meta( $post_id, 'show', $s);

}
function prepare_maps($id){
    global $bory_maps_array;
    $cords1=get_post_meta($id, 'lat',true);
    $cords2=get_post_meta($id, 'lng',true);
    $zoom=get_post_meta($id, 'zoom',true);
    $tit=get_post_meta($id, 'title_marker',true);
   // $show=get_post_meta($id, 'show');
    $bory_maps_array = array(
        'zoom' => $zoom,
        'cords1' => $cords1,
        'cords2' => $cords2,
        'title' => $tit
    );


     add_action( 'wp_footer', 'maps_enqueue_script' );


}
function maps_enqueue_script() {
    global $bory_maps_array;
    wp_register_script('bory_maps', get_template_directory_uri() .'/js/mymaps.js',array('jquery'), '', true);
    wp_enqueue_script('bory_maps');
    wp_localize_script( 'bory_maps', 'boryObj', $bory_maps_array );
}


//Вывод
/*
 <?php 
     
              prepare_maps(get_the_ID());
         
         ?>
        <div class='col-sm-6 <?php if(get_post_meta($post->ID, "show",true)!="true"){  echo " hid"; } ?> '>



                <div id="map"  style="width: 100%; height: 300px"></div>

            </div>
			<style>.hide{display:none;}</style>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA55qjIVkbws0THLC1nYrGwsVUEHJawWUc&callback=initMap"
                 async defer></script>


*/


