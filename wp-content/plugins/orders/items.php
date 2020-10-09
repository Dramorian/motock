<?php
/**
 * Edit Pages Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

global $wpdb;
global $orders_table_name;
$order_items = $wpdb->get_results( "SELECT * FROM $orders_table_name ORDER BY date DESC" );
?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php echo esc_html( $title );  ?>
        </h2>
        <form id="posts-filter" action="" method="get">
            <?php if ($order_items) { ?>
                <div class="clear"></div>
                <table class="widefat orders fixed" cellspacing="0">
                    <thead>
                    <tr>
                        <th scope="col" class="manage-column column-date" style="">Дата/Время</th>
                        <th scope="col" id="name" class="manage-column column-title" style="">ФИО</th>
                        <th scope="col" id="phone" class="manage-column column-title" style="">Email</th>
                        <th scope="col" id="url" class="manage-column column-title" style="">Телефон</th>
                        <th scope="col" id="shipping" class="manage-column column-title" style="">Город</th>
                        <th scope="col" id="payment" class="manage-column column-title" style="">Способ отправки</th>
                        <th scope="col" id="payment" class="manage-column column-title" style="">Метод оплаты</th>
                        <th scope="col" id="message" class="manage-column column-title" style="">Товар</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-date" style="">Дата/Время</th>
                        <th scope="col" id="name" class="manage-column column-title" style="">ФИО</th>
                        <th scope="col" id="phone" class="manage-column column-title" style="">Email</th>
                        <th scope="col" id="url" class="manage-column column-title" style="">Телефон</th>
                        <th scope="col" id="shipping" class="manage-column column-title" style="">Город</th>
                        <th scope="col" id="payment" class="manage-column column-title" style="">Способ отправки</th>
                        <th scope="col" id="payment" class="manage-column column-title" style="">Метод оплаты</th>
                        <th scope="col" id="message" class="manage-column column-title" style="">Товар</th>
                    </tr>
                    </tfoot>
                    <tbody class="page_rows_test" >
                    <?php foreach ($order_items as $item):?>
                    <tr>
                        <?php $content = json_decode($item->content)?>
                        <th><?php echo $item->date?></th>
                        <?php foreach ($content as $value):?>
                            <th><?php echo $value;?></th>
                        <?php endforeach;?>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="clear"></div>
                <p><?php _e('No orders found.') ?></p>
                <?php
            } // end if ($posts)
            ?>
        </form>
    </div>
<?php
