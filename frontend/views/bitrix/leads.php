<?php
if (empty($model)) {
    echo 'Пусто';
    exit();
}
?>
<table border="1">
    <thead>
    <tr>
        <th>№</th>
        <th>Название</th>
        <th>Статус</th>
    </tr>
    </thead>
    <? foreach ($model as $item):
        ?>
        <tbody>
        <tr>
            <td><?= $item->id ?></td>
            <td><?= $item->name ?></td>
            <td><?= $item->status ?></td>
        </tr>
        </tbody>
    <?php endforeach; ?>
</table>
