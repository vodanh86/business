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

    public function href()
    {
        // Return the URL to the view page
        return route('admin.edu.report-student.show', ['id' => $this->id]);
    }

    public function render()
    {
        return "<a class='fa fa-eye btn btn-sm btn-secondary grid-view-row' href='{$this->href()}'>Xem</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
