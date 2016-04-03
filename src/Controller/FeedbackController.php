<?php

namespace Drupal\sfeedback\Controller;

use Drupal\Core\Link;
use Drupal\Core\Controller\ControllerBase;
use Drupal\sfeedback\Storage\FeedbackStorage;

class FeedbackController extends ControllerBase
{
    public function content()
    {
        $content = [];

        $content['add-feedback'] = [
            '#prefix' => '<p>',
            '#markup' => Link::createFromRoute(t('New feedback'), 'sfeedback.admin.create')->toString(),
            '#suffix' => '</p>'
        ];

        $tableHeader = [
            'id' => t('Id'),
            'fullname' => t('FullName'),
            'email' => t('E-mail'),
            'text' => t('Text'),
            'actions' => t('Actions')
        ];

        $rows = [];
        foreach (FeedbackStorage::getInstance()->getAll() as $id => $feedback) {
            $rows[] = [
                'data' => [
                    $id,
                    $feedback->fullname,
                    $feedback->email,
                    $feedback->text,
                    [
                        'data' => [
                            '#markup' =>
                                Link::createFromRoute(t('Edit'), 'sfeedback.admin.update', ['id' => $id])->toString()  . ' ' .
                                Link::createFromRoute(t('Delete'), 'sfeedback.admin.delete', ['id' => $id])->toString()
                        ]
                    ]
                ]
            ];
        }

        $content['sfeedback-table'] = [
            '#type' => 'table',
            '#header' => $tableHeader,
            '#tableselect' => true,
            '#empty' => t('There are no item yet. Add new item.'),
            '#rows' => $rows,
            '#attributes' => ['id' => 'sfeedback-table']
        ];

        return $content;
    }
}