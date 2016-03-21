<?php
add_action( 'add_meta_boxes', 'bory_details_portf' );
add_action('save_post', 'bory_save_bory_details_portf');

function bory_register_scripts(){ //Подключение скриптов
    wp_register_script('bory_metaform', get_template_directory_uri().'-child' .'/js/borymeta.js',array('jquery'), '', true);
    wp_enqueue_script('bory_metaform');
}
/*
*
*
*Метабокс для портфолио
*
*/
//Создание meta Box
function bory_details_portf() {


        add_meta_box(
            'bory-metabox_portf',
            'Описание товара',
            'bory_mbox_form',
            'portfolio', 
			'normal', 
			'high'
        );
    
}
//***********************************
//Описание работы meta Box
function bory_mbox_form($post ) {
    $bory_title = get_post_meta($post->ID, 'bory_title');
    $bory_desc= get_post_meta($post->ID, 'bory_desc');
 
    wp_nonce_field( 'bory_inner_mbox', 'bory_inner_mbox_box_nonce' );

 
                                         // вывод формы на экран								
					echo "<div id='my_grup_form'>";	
					
				for($i=0;$i<count($bory_desc[0]);$i++){							
                        ?>	
                       <div class='bory_form'><p><label for='bory_title'>Характеристика</label><br>
					   <input name='bory_title[]'type='text' class='prod_title' value='<?php echo $bory_title[0][$i] ?>' placeholder="Название"/>:
					   <input name='bory_desc[]'type='text' class='prod_title'  style='width:300px;' value='<?php echo $bory_desc[0][$i] ?>' placeholder="Описание" />
					   </p></div>
                    <?php
				}
				echo "</div>";
				echo "<input name='add_f' id='add_form' type='button' value='Добавить'>";
				echo "<p style='color:red;font-size: 10px;'>*- Если не заполнить поле \"Название\" при обновлении эта Характеристика удалится  </p>"	;	
			
}
function bory_save_bory_details_portf($post_id){

    if ( ! isset( $_POST['bory_inner_mbox_box_nonce'] ) )// Есть частный случай?
        return $post_id;

    $nonce = $_POST['bory_inner_mbox_box_nonce'];

    if ( ! wp_verify_nonce( $nonce, 'bory_inner_mbox' ) )//Валидность частного случая.
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
     $title=$_POST['bory_title'];
	 $desc=$_POST['bory_desc'];
	 $t= array();
	 $d= array();
	 $i=0;
	 foreach($title as $item){     //Очистка массива от пустых значений
	 
		 if(!empty($item)){
			$t[]= $item;
			$d[]=$desc[$i];
		 }
		 $i++;
	 }

    update_post_meta( $post_id, 'bory_title', $t);
	update_post_meta( $post_id, 'bory_desc', $d);
  
  
}

// Вывод
/*
<?php $bory_title = get_post_meta($post->ID, 'bory_title');
          $bory_desc= get_post_meta($post->ID, 'bory_desc');
		  $i=0;
		 if(count($bory_title[0])>0){ 
         foreach( $bory_title[0] as $item){
		  if(!empty( $bory_desc[0][$i])){
		  ?>
		  
		  <span class="portfolio_detail_title"><?php echo $item; ?></span> <?php echo $bory_desc[0][$i]; ?></br>
		 
		  <?php
		  }
		  $i++;
	  }
		 }
		  ?>

*/









