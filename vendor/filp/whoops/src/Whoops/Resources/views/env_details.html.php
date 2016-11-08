<?php /* List data-table values, i.e: $_SERVER, $_GET, .... */ ?>
<div class="details">
    <h2>系统信息</h2>
    <div class="data-table-container" id="data-tables">
        <?php foreach ($tables as $label => $data): ?>
            <div class="data-table" id="sg-<?php echo $tpl->escape($tpl->slug($label)) ?>">
                <?php if (!empty($data)): ?>
                    <label><?php echo $tpl->escape($label) ?></label>
                    <table class="data-table">
                        <thead>
                        <tr>
                            <td class="data-table-k">Key</td>
                            <td class="data-table-v">Value</td>
                        </tr>
                        </thead>
                        <?php foreach ($data as $k => $value): ?>
                            <tr>
                                <td><?php echo $tpl->escape($k) ?></td>
                                <td><?php echo $tpl->dump($value) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                <?php else: ?>
                    <label class="empty"><?php echo $tpl->escape($label) ?></label>
                    <span class="empty">empty</span>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
</div>