<?php if (!empty($rows)) :?>
<div class="alert alert-danger" role="alert"><i class="glyphicon glyphicon-exclamation-sign"></i> <strong><?php echo count($rows) . ' ' . lang('common_error_import_rows'); ?></strong></div>
<table class="table table-hover error mt-10">
    <thead>
    <tr>
        <th width="45px"></th>
        <?php $index = 'A'; foreach ($columns as $column) :?>
        <th>
            <?php if (!empty($column)) :?>
            <?php $field_part = explode(':', $column); ?>
            <?php echo lang('common_' . $field_part[1]); ?>
            <?php else :?>
            <?php echo lang('common_column') . ' ' . $index; ?>
            <?php endif; ?>
        </th>
        <?php $index++; endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php $index = 1; foreach ($rows as $row) :?>
    <tr>
        <td width="45px"><strong><?php echo ($index); ?>. </strong></td>
        <?php foreach ($row as $value) :?>
        <td>
            <?php if (!empty($value)) :?>
            <?php echo $value; ?>
            <?php else :?>
            <p class="empty-data"><?php echo lang('common_empty_data'); ?></p>
            <?php endif; ?>
        </td>
        <?php endforeach; ?>
    </tr>
    <?php $index++; endforeach; ?>
    </tbody>
</table>
<?php endif; ?>