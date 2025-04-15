<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\BatchModel;

class BatchController extends Controller
{

    public function createBatch()
    {
        var_dump($_POST);
        $_POST['po_id'] = 1;
        $batchModel = new BatchModel();
        $batchModel->createBatch($_POST);
        $_SESSION['message'] = 'Batch added successfully';
        $_SESSION['message_type'] = 'success';
        View::redirect('/products/' . $_POST['product_id']);
    }

    public function updateBatch(array $params)
    {
        if (!$this->validator->validateUpdateBatch($_POST)) {
            $_SESSION['message'] = $this->validator->getError();
            $_SESSION['message_type'] = 'error';
            View::redirect('/products/' . $_POST['product_id']);
        }

        $batchModel = new BatchModel();
        $_POST['id'] = $params['id'];
        $batchModel->updateBatch($_POST);
        $_SESSION['message'] = 'Batch updated successfully';
        $_SESSION['message_type'] = 'success';
        View::redirect('/products/' . $_POST['product_id']);
    }
}
