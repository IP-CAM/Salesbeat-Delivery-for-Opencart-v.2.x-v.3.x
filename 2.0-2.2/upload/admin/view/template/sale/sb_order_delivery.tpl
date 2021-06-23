<?= $header; ?><?= $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-sb-order-delivery" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?= $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
        <li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-info-circle"></i><?= $text_sb_order_delivery; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form-sb-order-delivery" class="form-horizontal">
          <div id="sb-cart-widget"></div>
          <div id="sb-cart-widget-result"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  window.addEventListener('load', function () {
    SalesbeatAdminDelivery.init({
      url: "<?= str_replace('&amp;', '&', $save_delivery); ?>",
      token: "<?= $widget['token'] ?>",
      city_code: "",
      products: <?= $widget['products'] ?>
    });
  });
</script>
<?= $footer ?>