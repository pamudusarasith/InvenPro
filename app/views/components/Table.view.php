<div class="tbl">
    <table>
        <?php if (isset($headers)) : ?>
            <tr>
                <?php foreach ($headers as $header) : ?>
                    <th><?= $header ?></th>
                <?php endforeach; ?>
            </tr>
        <?php endif; ?>
        <?php if (isset($keys) && isset($rows)) {
            $rowIdField = isset($rowIdField) ? $rowIdField : "id";
            foreach ($rows as $row) : ?>
                <tr data-id="<?= array_key_exists($rowIdField, $row) ? htmlspecialchars($row[$rowIdField]) : "" ?>">
                    <?php foreach ($keys as $key) : ?>
                        <td><?= $row[$key] ? htmlspecialchars($row[$key]) : "NULL" ?></td>
                    <?php endforeach; ?>
                </tr>
        <?php endforeach;
        } ?>
    </table>
</div>
