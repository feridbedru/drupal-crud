<?php

namespace Drupal\residence\FilterForm;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;


class HousePurposeSearchForm extends FormBase
{

    /**
     * The Database Connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    protected $database;

    public $header;

    /**
    * {@inheritdoc}
    */
    public function getFormId()
    {
        return 'house_purpose_search_form';
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        $controller = new static($container->get('database'));
        $controller->setStringTranslation($container->get('string_translation'));
        return $controller;
    }

    /**
     * HousePurposeSearchForm constructor.
     *
     * @param \Drupal\Core\Database\Connection $database
     *   The database connection.
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
                $this->header = [
            ['data' => $this->t('#'), 'field' => 'h.id'],
            ['data' => $this->t('Name'), 'field' => 'h.name'],
            ['data' => $this->t('Description'), 'field' => 'h.description'],
            ['data' => $this->t('Operations'), 'colspan' => 2], 
        ];

        $form['filters'] = [
            '#type'  => 'details',
            '#title' => $this->t('Search and Filter'),
            '#tree' => TRUE,
            '#open'  => TRUE,
        ];

        $form['filters']['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#default_value' => (isset($data['name'])) ? $data['name'] : '',
            '#attributes' => array('class' => array('form-control')),
            '#prefix' => '<div class="form-group col-md-2">',
            '#suffix' => '</div>',
        ];


        $form['filters']['actions'] = [
            '#type' => 'actions'
        ];

        $form['filters']['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => t('Filter'),
            '#wrapper_attributes' => ['class' => 'mb-3'],
            '#ajax' => [
                'callback' => [$this, 'findRows'],
                'wrapper' => 'list-container',
            ],
        ];

        $name = "";
        $resources = $this->getDataRows($name, );

        // render table
        $form['tablesort_table'] = [
            '#theme' => 'table',
            '#header' => $this->header,
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

    private function getDataRows($name, )
    {
        $query = $this->database->select('house_purposes', 'h')
            ->fields('h', ['id', 'name', 'description'])
            ->extend('Drupal\Core\Database\Query\TableSortExtender')
            ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
            ->limit(10);

        if ($name != "") {
            $query->condition('h.name', '%' . $name . '%', 'like');
        }

        $results = $query
            ->orderByHeader($this->header)
            ->execute();

        $rows = [];
        $count = 0;

        foreach ($results as $data) {
            if ($data->id != 0) {

                $count++;
                $linkDelete = '';
                $linkEdit = '';

                $url_view = Url::fromRoute('residence.house_purpose.show', ['id' => $data->id], []);
                if (\Drupal::currentUser()->hasPermission('house_purpose delete')) {
                    $delete_options = array(
                        'attributes' => array(
                            'class' => array(
                                'button button--danger button--small',
                            ),
                        ),
                    );
                    $url_delete = Url::fromRoute('residence.house_purpose.delete', ['id' => $data->id], []);
                    $url_delete->setOptions($delete_options);
                    $linkDelete = Link::fromTextAndUrl('Delete', $url_delete);
                }

                if (\Drupal::currentUser()->hasPermission('house_purpose edit')) {
                    $edit_options = array(
                        'attributes' => array(
                            'class' => array(
                                'button button--secondary button--small',
                            ),
                        ),
                    );
                    $url_edit = Url::fromRoute('residence.house_purpose.edit', ['id' => $data->id], []);
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
        $rows = $this->getDataRows($field["name"], );
        $form['table']['#rows'] = $rows;

        return $form['table'];
    }
}
