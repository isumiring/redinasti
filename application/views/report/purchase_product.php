<?php if (!is_ajax_requested()) : ?>
<section class="well animated fadeInUp">
    <div class="row-fluid">
        <div class="span12">
            <div style='float: left;color: #00a429'>
                <ul class="breadcrumb">
                </ul>
            </div>
            <div style="clear:both;"></div>
            <?php if (isset($message)) { ?>
                <div style='float: left;color: #00a429'><?= $message ?></div>
            <?php } ?>
            <?php if (isset($tmp_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $tmp_msg ?></div>
            <?php } ?>
            <?php if (isset($success_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $success_msg ?></div>
            <?php } ?>
        </div>
    </div>  
    <hr>
    <div id='list_data'>
        <form action="<?=$form_action?>" onsubmit="return false;" id="form-range">
            <div class="input-daterange report-daterange" id="datepicker">
                <input type="text" class="input-small" name="from_date" id="startRange" value="<?=(isset($post['to_date'])) ? $post['to_date'] : ''?>" />
                <span class="add-on">to</span>
                <input type="text" class="input-small" name="to_date" id="endRange" value="<?=(isset($post['to_date'])) ? $post['to_date'] : ''?>" />
                <button type="button" class="btn btn-success reload" title="Reload Data" id="submit-range"><i class="icon-refresh"></i></button>
            </div>
        </form>
        <div class="display_message" tabindex="1">
            <?php
                if (isset($error_msg)) {
                    echo $error_msg;
                }
            ?>
        </div>
        <!-- start listing data -->
        <table class="table table-striped table-bordered table-hover" id="reportTable">
            <thead>
                <tr>
                    <th class="center" style='width:1px;'>No</th>
                    <th class="purchase_invoice">Invoice<span></span></th>
                    <th class="supplier">Supplier<span></span></th>
                    <th class="shippping_date">Tanggal<span></span></th>
                    <th class="purchase_type">Jumlah Jenis Barang<span></span></th>
                    <th class="purchase_qty">Jumlah QTY Barang<span></span></th>
                    <th class="purchase_qty">Total QTY Retur<span></span></th>
                    <th class="purchase_price">Total Harga<span></span></th>
                    <th class="purchase_price">Total Dibayar<span></span></th>
                    <!--<th class="center">Action</th>-->
                </tr>
            </thead>
            <tbody>
                <?php $i=0; $total_price=$total_qty=$total_paid=$total_retur=0; foreach ($data as $row) : $i++; ?>
                    <tr>
                        <td class="center"><?=$i?></td>
                        <td class="purchase_invoice"><?=$row['purchase_invoice']?></td>
                        <td class="supplier"><?=$row['supplier']?></td>
                        <td class="shippping_date"><?=iso_date($row['shipping_date'])?></td>
                        <td class="text-right purchase_type"><?=$row['total_product_type']?></td>
                        <td class="text-right purchase_qty"><?=$row['total_qty']?></td>
                        <td class="text-right purchase_retur"><?=$row['total_retur']?></td>
                        <td class="text-right total_price"><?=myprice($row['total_price'])?></td>
                        <td class="text-right total_paid"><?=myprice($row['total_paid'])?></td>
                        <!--
                        <td class="center">
                            <a href="<?=site_url($controller.'/sales_product_detail/'.$row['id_product'])?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
                        </td>
                        -->
                    </tr>
                    <?php $total_price += $row['total_price']; $total_qty += $row['total_qty']; $total_retur += $row['total_retur']; $total_paid += $row['total_paid'];?>
                <?php endforeach; ?>
                <tr class="footer">
                    <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong><?=$total_qty?></strong></td>
                    <td class="text-right"><strong><?=$total_retur?></strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_price)?></strong></td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_paid)?></strong></td>
                    <!--<td class="text-right">&nbsp;</td>-->
                </tr>
                <tr class="footer">
                    <td colspan="8" class="text-right">
                        <strong>GRAND TOTAL</strong> 
                        (total harga - jumlah yang sudah dibayar)<br/>
                        <small>*jika hasil (-) maka mempunyai piutang</small>
                    </td>
                    <td class="text-right"><strong>Rp. <?=myprice($total_price-$total_paid)?></strong></td>
                    <!--<td class="text-right">&nbsp;</td>-->
                </tr>
            </tbody>
        </table>
        <!-- end of listing data -->
        <hr/>
    </div>
</section>
<style>
    .ui-icon-carat-1-s,.ui-icon-carat-1-n{float: right;}
</style>
<script type="text/javascript">
    $(function() {
        $("#submit-range").click(function() {
            var button_html = $(this).html(),
                start = $("#startRange").val(),
                end = $("#endRange").val();
            if (start != '' && end != '') {
                $.ajax({
                    url:'<?=$form_action?>',
                    type:'post',
                    dataType:'json',
                    data:$("#form-range").serialize(),
                    beforeSend: function() {
                        $("#submit-range").attr('disabled',true);
                        $("#submit-range").html('Loading...');
                        $("#reportTable tbody").slideUp();
                        $("#reportTable tbody").css('display','none');
                        $("#reportTable tbody").empty();
                        $('.display_message').empty();
                    },
                    success: function(data) {
                        $("#submit-range").removeAttr('disabled');
                        if (data['error']) {
                            $('.display_message').html(data['error']).focus();
                        }
                        if (data['return']) {
                            $("#reportTable tbody").html(data['return']);
                            $("#reportTable tbody").slideDown();
                        }
                        $("#submit-range").html(button_html);
                    }
                });
                $("#form-range").submit();
            }
        });
    });
</script>
<?php else : ?>
    <?php $i=0; $total_price=$total_qty=$total_paid=0; foreach ($data as $row) : $i++; ?>
        <tr>
            <td class="center"><?=$i?></td>
            <td id="purchase_invoice"><?=$row['purchase_invoice']?></td>
            <td id="supplier"><?=$row['supplier']?></td>
            <td id="shippping_date"><?=iso_date($row['shipping_date'])?></td>
            <td id="purchase_type" class="text-right"><?=$row['total_product_type']?></td>
            <td id="purchase_qty" class="text-right"><?=$row['total_qty']?></td>
            <td id="total_price" class="text-right"><?=myprice($row['total_price'])?></td>
            <td id="total_paid" class="text-right"><?=myprice($row['total_paid'])?></td>
            <!--
            <td class="center">
                <a href="<?=site_url($controller.'/sales_product_detail/'.$row['id_product'])?>" title="Detail Record" class="btn btn-info"><i class="icon-edit"></i></a>
            </td>
            -->
        </tr>
        <?php $total_price += $row['total_price']; $total_qty += $row['total_qty']; $total_paid += $row['total_paid'];?>
    <?php endforeach; ?>
    <tr class="footer">
        <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
        <td class="text-right"><strong><?=$total_qty?></strong></td>
        <td class="text-right"><strong>Rp. <?=myprice($total_price)?></strong></td>
        <td class="text-right"><strong>Rp. <?=myprice($total_paid)?></strong></td>
        <!--<td class="text-right">&nbsp;</td>-->
    </tr>
    <tr class="footer">
        <td colspan="7" class="text-right">
            <strong>GRAND TOTAL</strong> 
            (total harga - jumlah yang sudah dibayar)<br/>
            <small>*jika hasil (-) maka mempunyai piutang</small>
        </td>
        <td class="text-right"><strong>Rp. <?=myprice($total_price-$total_paid)?></strong></td>
        <!--<td class="text-right">&nbsp;</td>-->
    </tr>
<?php endif; ?>