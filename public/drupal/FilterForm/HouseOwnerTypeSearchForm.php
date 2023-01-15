<?php

namespace Drupal\residence\FilterForm;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\residence\Controller\UtilityController;

class HouseOwnerTypeSearchForm extends FormBase
{

    /**
    * {@inheritdoc}
    */
    public function getFormId()
    {
        return 'house_owner_type_search_form';
    }

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $header_table = array(
            'id' => t('#'),
            'name' => $this->t('Name'), 
            'description' => $this->t('Description'), 
            [
                'data' => $this->t('Operations'),
                'colspan' => 2,
            ],
        );

        $form['filters'] = [
            '#type'  => 'container',
            '#open'  => true,
            '#attributes' => ['class' => 'row'],
        ];

        $form['filters']['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#default_value' => (isset($data['name'])) ? $data['name'] : '',
            '#attributes' => array('class' => array('form-control')),
            '#prefix' => '<div class="form-group col-md-2">',
            '#suffix' => '</div>',
        ];

        $form['filters']['description'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Description'),
            '#default_value' => (isset($data['description'])) ? $data['description'] : '',
            '#attributes' => array('class' => array('form-control')),
            '#prefix' => '<div class="form-group col-md-2">',
            '#suffix' => '</div>',
        ];


        $util = new UtilityController();
        $selectedLangCode = $util->getLanguage()['selectedLanguage'];
        $language_list = $util->getLanguage()['languageList'];

        $form['filters']['language'] = [
            '#type' => 'select',
            '#title' => $this->t('Language'),
            '#options' => $language_list,
            '#attributes' => array('class' => array('form-control')),
            '#default_value' => $selectedLangCode,
            '#prefix' => '<div class="form-group col-md-2">',
            '#suffix' => '</div>',
        ];

        $form['filters']['actions'] = [
            '#type' => 'actions'
        ];

        $form['filters']['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Search'),
            '#wrapper_attributes' => ['class' => 'mb-3'],
            '#ajax' => [
                'callback' => [$this, 'findRows'],
                'wrapper' => 'list-container',
            ],
        ];

        $name = "";
        $description = "";
        $language = UtilityController::getDefaultLanguage();
        $resources = $this->getDataRows($name, $description, $language);

        // render table
        $form['table'] = [
            '#id' => 'list-container',
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' =>  $resources,
            '#empty' => $this->t('No records found'),
            '#sticky' => TRUE,
        ];

        $form['pager'] = [
            '#type' => 'pager',
        ];

        $form['#attached']['library'][] = 'residence/bootstrap';

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

    private function getDataRows($name, $description, $language)
    {
        $query = \Drupal::database()->select('house_owner_types', 'h')
            ->fields('h', ['id', 'name', 'description', 'language'])
            ->extend('Drupal\Core\Database\Query\TableSortExtender')
            ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
            ->limit(10);

        if ($name != "") {
            $query->condition('h.name', '%' . $name . '%', 'like');
        }
        if ($description != "") {
            $query->condition('h.description',  $description,  '=');
        }
        if ($language != "") {
            $query->condition('h.language', $language, '=');
        }
        $query->orderBy('id', 'DESC');
        $results = $query->execute()->fetchAll();

        $rows = [];
        $count = 0;

        foreach ($results as $data) {
            if ($data->id != 0) {

                $count++;
                $linkDelete = '';
                $linkEdit = '';

                $url_view = Url::fromRoute('house_owner_type.show', ['id' => $data->id], []);
                $url_translate = Url::fromRoute('house_owner_type.translate', ['parent_id' => $data->id], []);
                if (\Drupal::currentUser()->hasPermission('house_owner_type delete')) {
                    $delete_options = array(
                        'attributes' => array(
                            'class' => array(
                                'button button--danger',
                            ),
                        ),
                    );
                    $url_delete = Url::fromRoute('house_owner_type.delete', ['id' => $data->id], []);
                    $url_delete->setOptions($delete_options);
                    $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
                }

                if (\Drupal::currentUser()->hasPermission('house_owner_type edit')) {
                    $edit_options = array(
                        'attributes' => array(
                            'class' => array(
                                'button button--secondary',
                            ),
                        ),
                    );
                    $url_edit = Url::fromRoute('house_owner_type.edit', ['id' => $data->id], []);
                    $linkEdit = Link::fromTextAndUrl('Edit', $url_edit);
                    $url_edit->setOptions($edit_options);
                }
            
                $con = Database::getConnection();

                $linkView = Link::fromTextAndUrl('View', $url_view);
                $rows[$data->id] = array(
                    'id' => $count,
                    'name' => $data->name,
                    'description' => $data->description,
                    'edit' =>  $linkEdit,
                    'delete' => $linkDelete,
                );
            }
        }
        return $rows;
    }

    function findRows(array &$form, FormStateInterface $form_state)
    {
        $field = $form_state->getValues();
        $rows = [];
        $rows = $this->getDataRows($field["name"], $field["description"],  $field["language"]);
        $form['table']['#rows'] = $rows;

        return $form['table'];
    }
}
