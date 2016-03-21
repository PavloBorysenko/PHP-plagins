<?php

add_action('wp_enqueue_scripts', 'testim_script');
function testim_script(){

wp_enqueue_script('validad', get_template_directory_uri().'/js/jquery.validate.min.js', array('jquery'));
wp_enqueue_script('form', get_template_directory_uri().'/js/my_form.js', array('jquery','validad'));


wp_localize_script( 'form', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

}

add_action("wp_ajax_add_testm", "add_testimoni");
add_action("wp_ajax_nopriv_add_testm", "add_testimoni");



function add_testimoni() {
//var_dump($_POST['form_data']) ;
	$defaults = array(
	'post_status'   => 'pending',
	'post_type'     => 'testimonials',
	'post_title'    => sanitize_text_field( $_POST['user_name'] ),
	'post_content'  => sanitize_text_field( $_POST['user_text'] ),
	'ping_status'   => get_option('default_ping_status'),
	'post_parent'   => 0,
	'menu_order'    => 0,
	'to_ping'       => '',
	'pinged'        => '',
	'guid'          => '',
	'post_content_filtered' => '',
	'import_id'     => 0,
	'meta_input'     =>  array( 'bory_email'=>sanitize_email( $_POST["user_email"] ) )  
);

$post_id = wp_insert_post( $defaults );
wp_die();
}



//Вывод
/*

<div class="row">
	
	<form id="my_form_t" >
	<div class="col-md-6 col-sm-6 col-xs-12">
	<input type="text" name="user_name" placeholder="Ваше имя*"/>
	
	</div>
	<div class="col-md-6 col-sm-6 col-sx-12">
	<input type="text" name="user_email" placeholder="Ваш e-mail*"/>
	</div>
	<div class="col-md-12 col-sm-12 col-sx-12">
	<textarea class="my_testim_text" name="user_text" placeholder="Ваш отзыв*" ></textarea>
	</div>
	<div class="col-md-6 col-sm-6 col-sx-12">
	<input class="add_testim" type="submit" value="ДОБАВИТЬ ОТЗЫВ"/>
	</div>
	<div class="col-md-6 col-sm-6 col-sx-12">
	<div id="my_error">
	<p>Пожалуйста, проверьте корректность заполнения полей</p>
	</div>
	</div>
	</form>
     <div class="col-sm-12 col-xs-12" id="my_send_ok"> <div id="text_ok"><p>
	 <i class="fa fa-check"></i>Спасибо!  Ваш отзыв отправлен и ожидает модерации.
	 </p></div></div>
	</div>
	

*/