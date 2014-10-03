<?php

namespace Modules\Files\Fields;

use Mindy\Base\Mindy;
use Mindy\Form\Fields\Field;
use Mindy\Helper\JavaScript;
use Mindy\Utils\RenderTrait;

class FilesField extends Field
{
    use RenderTrait;

    public $relatedFileField = 'file';
    public $relatedSortingField = 'position';

    public $uploadUrl;
    public $sortUrl;
    public $deleteUrl;
    public $template = "files/fields/files.html";

    public function getUploadUrl()
    {
        if (!$this->uploadUrl) {
            $this->uploadUrl = Mindy::app()->urlManager->reverse('files.files_upload');
        }
        return $this->uploadUrl;
    }

    public function getSortUrl()
    {
        if (!$this->sortUrl) {
            $this->sortUrl = Mindy::app()->urlManager->reverse('files.files_sort');
        }
        return $this->sortUrl;
    }

    public function getDeleteUrl()
    {
        if (!$this->deleteUrl) {
            $this->deleteUrl = Mindy::app()->urlManager->reverse('files.files_delete');
        }
        return $this->deleteUrl;
    }

    public function getData($encoded = true)
    {
        $model = $this->form->getInstance();
        $data = [
            'uploadUrl' => $this->getUploadUrl(),
            'sortUrl' => $this->getSortUrl(),
            'deleteUrl' => $this->getDeleteUrl(),
            'listId' => $this->getListId(),
            'flowData' => [
                'pk' => $model->pk,
                'name' => $this->getName(),
                'class' => $model::className(),
                'fileField' => $this->relatedFileField,
                Mindy::app()->request->csrf->csrfTokenName => Mindy::app()->request->csrf->csrfToken
            ],
            'sortData' => [
                'field' => $this->relatedSortingField,
                'name' => $this->getName(),
                'class' => $model::className(),
                Mindy::app()->request->csrf->csrfTokenName => Mindy::app()->request->csrf->csrfToken
            ],
            'deleteData' => [
                'name' => $this->getName(),
                'class' => $model::className(),
                Mindy::app()->request->csrf->csrfTokenName => Mindy::app()->request->csrf->csrfToken
            ]
        ];
        return ($encoded) ? JavaScript::encode(($data)) : $data;
    }

    public function getQuerySet()
    {
        $qs = $this->form->getInstance()->getField($this->getName())->getManager()->getQuerySet();
        return $qs->order([$this->relatedSortingField]);
    }

    public function render()
    {
        $items = $this->getQuerySet()->all();
        $model = $this->form->getInstance();

        echo $this->renderTemplate($this->template, [
            'items' => $items,
            'data' => $this->getData(true),
            'id' => $this->getId(),
            'filesId' => $this->getListId(),
            'fileField' => $this->relatedFileField,
            'modelPk' => $model->pk
        ]);
    }

    public function getListId()
    {
        return $this->getId() . '_files';
    }
}