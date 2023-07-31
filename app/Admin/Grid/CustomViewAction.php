<?php

namespace App\Admin\Grid;

use Encore\Admin\Actions\RowAction;
use Encore\Admin\Facades\Admin;

class CustomViewAction extends RowAction
{
    public $name = 'View';
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
    return <<<SCRIPT

    $('.grid-edit-row').on('click', function () {
        var id = $(this).data('id');
        var editUrl = '/admin/edu/report-detail/' + id;
        window.location.href = editUrl;
    });

    SCRIPT;
    }

    public function render()
    {
        Admin::script($this->script());

        return "<a class='fa fa-eye btn btn-sm btn-secondary grid-edit-row' data-id='{$this->id}'>Xem</a>";
    }
    public function __toString()
    {
        return $this->render();
    }
}
