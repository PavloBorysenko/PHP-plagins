<?php

/*
  Plugin Name: Scheme product meta
  Author URI:http://pavloborysenko.esy.es/
  Author: Pavlo Borysenko
  Version: 1.0
  Description: Adds the schema.org/Product to the page/post. [product id=% ]  id - by default, the value of the current post.
 */

add_action('admin_enqueue_scripts','bory_register_scripts'); // актив. подключение скриптов для админ
add_action('wp_enqueue_scripts','bory_register_style');//актив. подключение стилей для пользовательской части
add_action( 'add_meta_boxes', 'bory_mbox_create' );
add_action('save_post', 'bory_save_mbox_product');
add_action('widgets_init','bory_product_widget');
add_shortcode('product', 'bory_product_print');

//Подключение стилей
function bory_register_style(){
    wp_register_style('borystyle', plugins_url('/style/bory-style.css',__FILE__));
    wp_enqueue_style('borystyle');
}

function bory_register_scripts(){ //Подключение скриптов
    wp_register_script('boryuploader', plugins_url('/js/bory.uploader.js',__FILE__),array('jquery'));
    wp_enqueue_script('boryuploader');
}
//***********************************

//Создание meta Box
function bory_mbox_create() {

    $screens = array( 'post', 'page' );

    foreach ( $screens as $screen ):  //добовление мета бокса на стр. редактир. статьи и страници

        add_meta_box(
            'bory-metabox',
            'Реализация  schema.org/Product',
            'bory_mbox_form',
            $screen,
            'side',
            'high'
        );
    endforeach;
}
//***********************************

//Создание widgets
function bory_product_widget(){
    register_widget('BORY_Widget_product');
}
//**********************************


//Описание работы meta Box
function bory_mbox_form($post ) {
    $default = plugins_url('/img/noimages.jpg',__FILE__) ;
    $value= get_post_meta($post->ID, 'img_prod',true);
    $title= get_post_meta($post->ID, 'title_prod',true);
    $price_prod= get_post_meta($post->ID, 'price_prod',true);
    $description= get_post_meta($post->ID, 'desc_prod',true);
    $type_offer= get_post_meta($post->ID, 'offer_prod',true);
    $type_price_prod= get_post_meta($post->ID, 'type_price_prod',true);

    wp_nonce_field( 'bory_inner_mbox', 'bory_inner_mbox_box_nonce' );

    if(!$type_offer):
        $type_offer="Offer";
    endif;
    if(!$type_price_prod):
        $type_price_prod="UAH";
    endif;


    if( $value ) :
        $image_attributes = wp_get_attachment_image_src( $value );
        $src = $image_attributes[0];
     else:
        $src = $default;
    endif;

                                            // вывод формы на экран
    ?>
	<div>
		<img data-src="<?php echo $default ?>" src="<?php echo $src ?>" width="120px" height="95px" />
		<div>
			<input type="hidden" name="img_prod" id="img_prod" value="<?php echo $value ?>" />
			<button type="submit" class="upload_image_button button">Загрузить</button>
			<button type="submit" class="remove_image_button button">&times;</button>
		</div>
	</div>
    <div>
        <label for="title_prod">Заголовок</label><br>
        <input name="title_prod" id="title_prod" type="text" class="prod_title" value="<?php echo esc_attr($title) ?>"/><br>
        <label for="desc_prod">Описание</label><br>
        <textarea  rows="4" cols="30"  name="desc_prod" id="desc_prod" type="text" class="prod_title" >
        <?php echo esc_attr($description) ?>
        </textarea><br>

        <input type="radio" name="offer_prod" id="offer_prod" value="Offer"
            <?php if( esc_attr($type_offer)=="Offer")echo "checked" ?> >
        <span>Одно предложение</span> <br>
        <input type="radio" name="offer_prod" id="offer_prod" value="AggregateOffer"
            <?php if( esc_attr($type_offer)=="AggregateOffer")echo "checked" ?> >
        <span> Группа предложений</span><br><br>


        <label for="price_prod">Цена</label><br>
        <input name="price_prod" id="price_prod" type="text"  value="<?php echo esc_attr($price_prod) ?>"/>
        <select name="type_price_prod" id="type_price_prod">
            <option value="<?php echo esc_attr($type_price_prod) ?>"><?php echo  esc_attr($type_price_prod) ?></option>
            <option value="RUB">RUB</option>
            <option value="UAH">UAH</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
        </select><br>
        <p style="font-size: 0.8em;color: red;">
            *Для корректной работы,пожалуйста, заполните все поля.</p>


    </div>
	<?php
}

function bory_save_mbox_product($post_id){

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

    $title = sanitize_text_field( $_POST['title_prod'] );
    $description = sanitize_text_field( $_POST['desc_prod'] );
    $price = sanitize_text_field( $_POST['price_prod'] );
    $img = sanitize_text_field( $_POST['img_prod'] );
    $type_off = sanitize_text_field( $_POST['offer_prod'] );
    $type_pr = sanitize_text_field( $_POST['type_price_prod'] );

    update_post_meta( $post_id, 'title_prod', $title);
    update_post_meta( $post_id, 'desc_prod', $description);
    update_post_meta( $post_id, 'price_prod', $price);
    update_post_meta( $post_id, 'img_prod', $img);
    update_post_meta( $post_id, 'offer_prod', $type_off);
    update_post_meta( $post_id, 'type_price_prod', $type_pr);
}
//**********************************

//Описание работы Widget
class BORY_Widget_product extends WP_Widget{
    function __construct(){
        $args=array(
            'name'=>'Show Product',
            'description'=>'Показ Продукта/Услуги с разметкой Schema.org/Product'
        );
        parent::__construct('bory_wid_true','',$args);
    }
    function widget($args,$instans){

        $id=get_the_ID();
        if(is_page()||is_single())://Условия вывода только на Странице и Сингл статьи
            if(( get_post_meta($id, 'title_prod',true)/*&&( get_post_meta($id, 'desc_prod',true))*/))://Если раскомментировать + проверка на наличие описания  товара

                echo $args['before_widget'];
                echo $args['before_title'].$instans['title'].$args['after_title'];
                echo  get_html($id,'wid');
                echo $args['after_widget'];
            endif;

        endif;

    }

    function form($instans){
        extract($instans)
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title') ?>">Заголовок виджета:</label>
            <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>"
                   class="widefat" value="<?php if(isset($title)) echo esc_attr($title);  ?>" >
        </p>

    <?php
    }
}
//**********************************



// Функция возврата HTML для вывода схемы. $plaze-определяет класс стилей
function get_html($p_id,$plaze){
    $html='';
    $value= get_post_meta($p_id, 'img_prod',true);
    $title= get_post_meta($p_id, 'title_prod',true);
    $price_prod= get_post_meta($p_id, 'price_prod',true);
    $description= get_post_meta($p_id, 'desc_prod',true);
    $type_offer= get_post_meta($p_id, 'offer_prod',true);
    $type_price_prod= get_post_meta($p_id, 'type_price_prod',true);
    $image_attributes = wp_get_attachment_image_src( $value,array(250,195) );
    $src = $image_attributes[0];

        if($type_offer=='Offer'):// проверка на вывод "Цена" или "Цена от" зависит от типа предложения
            $price_text='Цена: ';
            $price='price';
         else :
            $price_text='Цена от: ';
            $price='lowPrice';
        endif;

    $html='<div class="bory_'.$plaze.'"><div itemscope itemtype="http://schema.org/Product"> <h1 itemprop="name">'.$title .'</h1>';
    $html.='<img src="'.$src.'" itemprop="image">';
    $html.= '<p><span itemprop="description">'.$description.'</span></p>';

    $html.='<div class="fin_pr" itemprop="offers" itemscope itemtype="http://schema.org/'.$type_offer.'"><span>'.$price_text.'</span><span itemprop="'.$price.'">'.$price_prod.'</span>';
    $html.='  <span  itemprop="priceCurrency">'.$type_price_prod.'</span></div>';

    $html.='</div></div>';
    return $html;
}
//**********************************


//Описание шорт кода
function bory_product_print($atts){

    $atts=shortcode_atts(         //параметры по умолчанию
        array(
              'id'=>get_the_ID()
        ),$atts
    );

    if(get_post_meta($atts['id'], 'title_prod',true)):
        return get_html($atts['id'],'page');
    endif;

    return '';
}
//**********************************