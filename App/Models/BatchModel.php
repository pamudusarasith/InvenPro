<?php

namespace App\Models;

use App\Core\Model;

class BatchModel extends Model
{
    public function createBatch(array $data)
    {
        $sql = '
            INSERT INTO product_batch (product_id,branch_id,po_id,batch_code,manufactured_date,expiry_date,unit_price,initial_quantity,current_quantity )
            VALUES (?, ? ,? ,? ,? ,? ,? ,?, ?)
        ';

        self::$db->query($sql, [
            $data['product_id'],
            $_SESSION['user']['branch_id'],
            $data['po_id'],
            $data['batch_code'],
            $data['manufactured_date'],
            $data['expiry_date'],
            $data['unit_price'],
            $data['quantity'],
            $data['quantity']
        ]);
    }

    public function updateBatch(array $data)
    {
        $sql = '
            UPDATE product_batch
            SET
                batch_code = ?,
                manufactured_date = ?,
                expiry_date = ?,
            WHERE id = ?
        ';

        self::$db->query($sql, [
            $data['batch_code'],
            $data['manufactured_date'],
            $data['expiry_date'],
            $data['id']
        ]);
    }
}
