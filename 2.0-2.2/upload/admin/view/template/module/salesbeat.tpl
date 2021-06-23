<?= $header; ?><?= $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?= $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($error_warning): ?>
    <div class="alert alert-danger alert-dismissible">
      <i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active">
              <a href="#d_home" data-toggle="tab">
                <i class="fa fa-home fa-fw"></i> <span><?= $tab_home; ?></span>
              </a>
            </li>
            <li>
              <a href="#d_setting" data-toggle="tab">
                <i class="fa fa-cog fa-fw"></i> <span><?= $tab_setting; ?></span>
              </a>
            </li>
            <li>
              <a href="#d_pay_systems" data-toggle="tab">
                <i class="fa fa-credit-card fa-fw"></i> <span><?= $tab_pay_systems; ?></span>
              </a>
            </li>
          </ul>

          <div class="tab-content">
            <div id="d_home" class="tab-pane active">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?= $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="module_salesbeat_status" id="input-status" class="form-control">
                    <?php if ($module_salesbeat_status): ?>
                      <option value="1" selected="selected"><?= $text_enabled; ?></option>
                      <option value="0"><?= $text_disabled; ?></option>
                    <?php else: ?>
                      <option value="1"><?= $text_enabled; ?></option>
                      <option value="0" selected="selected"><?= $text_disabled; ?></option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
            </div>

            <div id="d_setting" class="tab-pane">
              <legend><?= $legend_system ?></legend>

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-api-token"><?= $entry_api_token; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_api_token" value="<?= $module_salesbeat_api_token; ?>" placeholder="<?= $entry_api_token; ?>" id="input-api-token" class="form-control" />
                  <?php if ($error_api_token): ?>
                    <div class="text-danger"><?= $error_api_token; ?></div>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-secret-token"><?= $entry_secret_token; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_secret_token" value="<?= $module_salesbeat_secret_token; ?>" placeholder="<?= $entry_secret_token; ?>" id="input-secret-token" class="form-control" />
                </div>
                <?php if ($error_secret_token): ?>
                  <div class="text-danger"><?= $error_secret_token; ?></div>
                <?php endif; ?>
              </div>

              <legend><?= $legend_default_dimensions ?></legend>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-default-width"><?= $entry_default_width ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_default_width" value="<?= $module_salesbeat_default_width ?>" placeholder="<?= $entry_default_width ?>" id="input-default-width" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-default-height"><?= $entry_default_height ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_default_height" value="<?= $module_salesbeat_default_height ?>" placeholder="<?= $entry_default_height ?>" id="input-default-height" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-default-length"><?= $entry_default_length ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_default_length" value="<?= $module_salesbeat_default_length ?>" placeholder="<?= $entry_default_length ?>" id="input-default-length" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-default-weight"><?= $entry_default_weight ?></label>
                <div class="col-sm-10">
                  <input type="text" name="module_salesbeat_default_weight" value="<?= $module_salesbeat_default_weight ?>" placeholder="<?= $entry_default_weight ?>" id="input-default-weight" class="form-control" />
                </div>
              </div>
            </div>

            <div id="d_pay_systems" class="tab-pane">
              <legend><?= $legend_pay_systems; ?></legend>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-pay-systems-cash"><?= $entry_pay_systems_cash; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($module_salesbeat_pay_systems as $pay_system): ?>
                      <div class="checkbox">
                        <label>
                          <?php if (in_array($pay_system['code'], $module_salesbeat_pay_systems_cash)): ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_cash[]" value="<?= $pay_system['code']; ?>" checked="checked"/>
                            <?= $pay_system['name']; ?>
                          <?php else: ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_cash[]" value="<?= $pay_system['code']; ?>"/>
                            <?= $pay_system['name']; ?>
                          <?php endif; ?>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-pay-systems-card"><?= $entry_pay_systems_card; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($module_salesbeat_pay_systems as $pay_system): ?>
                      <div class="checkbox">
                        <label>
                          <?php if (in_array($pay_system['code'], $module_salesbeat_pay_systems_card)): ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_card[]" value="<?= $pay_system['code']; ?>" checked="checked"/>
                            <?= $pay_system['name']; ?>
                          <?php else: ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_card[]" value="<?= $pay_system['code']; ?>"/>
                            <?= $pay_system['name']; ?>
                          <?php endif; ?>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-pay-systems-online"><?= $entry_pay_systems_online; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($module_salesbeat_pay_systems as $pay_system): ?>
                      <div class="checkbox">
                        <label>
                          <?php if (in_array($pay_system['code'], $module_salesbeat_pay_systems_online)): ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_online[]" value="<?= $pay_system['code']; ?>" checked="checked"/>
                            <?= $pay_system['name']; ?>
                          <?php else: ?>
                            <input type="checkbox" name="module_salesbeat_pay_systems_online[]" value="<?= $pay_system['code']; ?>"/>
                            <?= $pay_system['name']; ?>
                          <?php endif; ?>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>

              <legend><?= $legend_pay_systems2; ?></legend>

              <div class="form-group">
                <div class="col-sm-12 text-center">
                  <input type="button" value="<?= $button_sync_pay_systems; ?>" class="btn btn-primary" data-action="sync_pay_systems">
                  <input type="hidden" name="module_salesbeat_pay_systems_last_sync" value="<?= $module_salesbeat_pay_systems_last_sync; ?>" data-input="pay_systems_last_sync">
                  <div data-result="sync_pay_systems">
                    <?php
                    if (!empty($module_salesbeat_pay_systems_last_sync)):
                      echo $entry_pay_systems_last_sync . ' ' . $module_salesbeat_pay_systems_last_sync;
                    else:
                      echo $error_pay_systems_last_sync;
                    endif;
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    let ModuleSalesbeat = {
      resultBlock: document.querySelector('[data-result="sync_pay_systems"]'),
      hiddenInput: document.querySelector('[data-input="pay_systems_last_sync"]'),

      syncPaySystem: function()
      {
        let me = this;

        this.resultBlock.innerHTML = '<?= $entry_load_systems_last_sync; ?>';
        this.sendPost('<?= str_replace("&amp;", "&", $link_sync_pay_system); ?>', {action: 'sync_pay_systems'}, function(data) {me.resultPaySystem(JSON.parse(data))});
      },
      resultPaySystem: function(data) {
        if (data.status === 'success') {
          this.hiddenInput.value = data.time;

          alert('<?= $success_pay_systems_sync; ?>');
          this.resultBlock.innerHTML = data.message;
        } else {
          alert('<?= $error_pay_systems_sync; ?>');
          this.resultBlock.innerHTML = data.message;
        }
      },
      sendPost: function (url, data, callback)
      {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.onreadystatechange = function() {
          if (xhr.status !== 200) {
            console.log('<?= $error_server; ?> ' + this.status);
          } else if (xhr.readyState === 4) {
            callback(xhr.response);
          }
        };
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        xhr.send(JSON.stringify(data));
      },
    };

    let button = document.querySelector('[data-action="sync_pay_systems"]');
    button.onclick = function(e) {
      e.preventDefault();

      ModuleSalesbeat.syncPaySystem();
    }
  })();
</script>
<?= $footer ?>