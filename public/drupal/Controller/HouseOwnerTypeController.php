<?php

namespace Drupal\residence\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;


/**
 * Class HouseOwnerTypeController
 * @package Drupal\residence\Controller
 */
class HouseOwnerTypeController extends ControllerBase
{
    /**
     * @return array
     */
    public function getTitle($id)
    {
        return UtilityController::generateTitle($id, 'house_owner_types', 'name');
    }

    /**
     * @return array
     */
    public function show($id)
    {
        $con = Database::getConnection();
        $query = $con->select('house_owner_types', 'h')
            ->condition('id', $id)
            ->fields('h');
        $data = $query->execute()->fetchAssoc();
        $active = $data['active'] ? "Yes" : "No";
        $back = Url::fromRoute('house_owner_type.index', [])->toString();
        $html = '
        <p><b>' . t('Name') . ': </b> ' . $data['name'] . '</p>
        <p><b>' . t('Description') . ': </b> ' . $data['description'] . '</p>
        <p><b>' . t('Active') . ': </b> ' . $active . '</p>';
        $html .= '<br><a href="' . $back . '" class="button button--primary">' . t('Back') . '</a>';

        return [
            '#type' => 'inline_template',
            '#template' => $html
        ];
    }

}
